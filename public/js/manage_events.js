let events = [];

document.addEventListener("DOMContentLoaded", async function () {
    await loadEvents();
    showStats();
    showEvents();
});

async function apiRequest(url, options = {}) {
    const headers = options.headers || {};

    headers["Accept"] = "application/json";

    if (!(options.body instanceof FormData)) {
        headers["Content-Type"] = headers["Content-Type"] || "application/json";
    }

    if (options.method && options.method !== "GET") {
        headers["X-CSRF-TOKEN"] = getCsrfToken();
    }

    const response = await fetch(url, {
        credentials: "same-origin",
        ...options,
        headers
    });

    const result = await response.json();

    if (!response.ok) {
        throw new Error(getErrorMessage(result));
    }

    return result;
}

async function loadEvents() {
    try {
        const result = await apiRequest(APP_URL + "/api/events/mine");
        events = result.data;
        sortEvents();
        setMessage("");
    } catch (error) {
        events = [];
        setMessage(error.message || "Please log in as an organiser or admin to manage events.");
    }
}

function sortEvents() {
    events.sort(function (a, b) {
        return new Date(b.start_datetime) - new Date(a.start_datetime);
    });
}

function showStats() {
    let totalEvents = events.length;
    let totalTickets = 0;
    let totalRevenue = 0;

    for (let i = 0; i < events.length; i++) {
        const event = events[i];
        const sold = getTicketsSold(event);
        const price = Number(event.price);

        totalTickets += sold;
        totalRevenue += sold * price;
    }

    document.getElementById("totalEvents").textContent = totalEvents;
    document.getElementById("ticketsSold").textContent = totalTickets;
    document.getElementById("totalRevenue").textContent = formatPrice(totalRevenue);
}

function showEvents() {
    const tableBody = document.getElementById("eventsTableBody");
    tableBody.innerHTML = "";

    if (events.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="10">No events found.</td></tr>`;
        return;
    }

    for (let i = 0; i < events.length; i++) {
        const event = events[i];
        const sold = getTicketsSold(event);
        const availableSeats = event.available_seats ?? Math.max(0, event.capacity - sold);

        tableBody.innerHTML += `
            <tr>
                <td>${escapeHtml(event.title)}</td>
                <td><span class="category-badge">${escapeHtml(event.category?.name || "No category")}</span></td>
                <td>${escapeHtml(event.description)}</td>
                <td>${escapeHtml(event.organiser?.name || "Unknown")}</td>
                <td>${formatDateTime(event.start_datetime)}</td>
                <td>${escapeHtml(event.location)}</td>
                <td>${formatPrice(Number(event.price))}</td>
                <td>
                    <div class="ticket-info">
                        <strong>${sold} / ${event.capacity}</strong>
                        <span>${availableSeats} left</span>
                    </div>
                </td>
                <td><span class="status-badge">${escapeHtml(event.status)}</span></td>
                <td class="actions-cell">
                    <button type="button" class="action-btn view-btn" onclick="viewEvent(${event.id})">View</button>
                    <button type="button" class="action-btn edit-btn" onclick="editEvent(${event.id})">Edit</button>
                    <button type="button" class="action-btn delete-btn" onclick="deleteEvent(${event.id})">Delete</button>
                </td>
            </tr>
        `;
    }
}

function formatDateTime(dateTime) {
    const date = new Date(dateTime);

    if (Number.isNaN(date.getTime())) {
        return "Not set";
    }

    return date.toLocaleString("en-AU", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true
    });
}

function formatPrice(price) {
    if (price === 0) {
        return "Free";
    }

    return "$" + price.toFixed(2);
}

function viewEvent(id) {
    const event = getEventById(id);

    if (!event) {
        return;
    }

    alert(
        "Event: " + event.title + "\n" +
        "Category: " + (event.category?.name || "No category") + "\n" +
        "Description: " + event.description + "\n" +
        "Organiser: " + (event.organiser?.name || "Unknown") + "\n" +
        "Date & Time: " + formatDateTime(event.start_datetime) + "\n" +
        "Location: " + event.location + "\n" +
        "Price: " + formatPrice(Number(event.price)) + "\n" +
        "Status: " + event.status + "\n" +
        "Tickets: " + getTicketsSold(event) + " / " + event.capacity
    );
}

async function editEvent(id) {
    const event = getEventById(id);

    if (!event) {
        return;
    }

    const newTitle = prompt("Edit event title:", event.title);
    if (newTitle === null || newTitle.trim() === "") {
        return;
    }

    const newDescription = prompt("Edit description:", event.description);
    if (newDescription === null || newDescription.trim() === "") {
        return;
    }

    const newStatus = prompt("Edit status (draft, published, cancelled):", event.status);
    if (newStatus === null || !["draft", "published", "cancelled"].includes(newStatus.trim())) {
        alert("Status must be draft, published, or cancelled.");
        return;
    }

    const newCapacityText = prompt("Edit capacity:", event.capacity);
    if (newCapacityText === null || Number(newCapacityText) < 1) {
        return;
    }

    try {
        await apiRequest(`${APP_URL}/api/events/${id}`, {
            method: "PUT",
            body: JSON.stringify({
                title: newTitle.trim(),
                description: newDescription.trim(),
                status: newStatus.trim(),
                capacity: Number(newCapacityText)
            })
        });

        await loadEvents();
        showStats();
        showEvents();
        alert("Event updated.");
    } catch (error) {
        alert(error.message);
    }
}

async function deleteEvent(id) {
    const confirmed = confirm("Are you sure you want to delete this event?");

    if (!confirmed) {
        return;
    }

    try {
        await apiRequest(`${APP_URL}/api/events/${id}`, {
            method: "DELETE"
        });

        await loadEvents();
        showStats();
        showEvents();
        alert("Event deleted.");
    } catch (error) {
        alert(error.message);
    }
}

function getEventById(id) {
    for (let i = 0; i < events.length; i++) {
        if (events[i].id === id) {
            return events[i];
        }
    }

    return null;
}

function getTicketsSold(event) {
    if (event.registered_count !== undefined) {
        return Number(event.registered_count);
    }

    return Math.max(0, Number(event.capacity) - Number(event.available_seats || 0));
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

function getErrorMessage(result) {
    if (result.errors) {
        const firstField = Object.keys(result.errors)[0];
        return result.errors[firstField][0];
    }

    return result.message || "Request failed.";
}

function setMessage(message) {
    document.getElementById("manageMessage").textContent = message;
}

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}