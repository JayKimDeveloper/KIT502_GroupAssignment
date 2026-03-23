const eventStorageKey = "techevents_events";

let events = [];

document.addEventListener("DOMContentLoaded", async function () {
    await loadEvents();
    sortEvents();
    showStats();
    showEvents();
});

async function loadEvents() {
    const savedEvents = localStorage.getItem(eventStorageKey);

    if (savedEvents) {
        events = JSON.parse(savedEvents);
    } else {
        const response = await fetch("data/events.json");
        events = await response.json();
        saveEvents();
    }
}

function saveEvents() {
    localStorage.setItem(eventStorageKey, JSON.stringify(events));
}

function sortEvents() {
    events.sort(function (a, b) {
        return new Date(b.dateTime) - new Date(a.dateTime);
    });
}

function showStats() {
    let totalEvents = events.length;
    let totalTickets = 0;
    let totalRevenue = 0;

    for (let i = 0; i < events.length; i++) {
        totalTickets += events[i].sold;
        totalRevenue += events[i].sold * events[i].price;
    }

    document.getElementById("totalEvents").textContent = totalEvents;
    document.getElementById("ticketsSold").textContent = totalTickets;
    document.getElementById("totalRevenue").textContent = "$" + totalRevenue;
}

function showEvents() {
    let tableBody = document.getElementById("eventsTableBody");
    tableBody.innerHTML = "";

    for (let i = 0; i < events.length; i++) {
        let event = events[i];

        tableBody.innerHTML += `
            <tr>
                <td>${event.title}</td>
                <td><span class="category-badge">${event.category}</span></td>
                <td>${event.description}</td>
                <td>${event.organiser}</td>
                <td>${formatDateTime(event.dateTime)}</td>
                <td>${event.location}</td>
                <td>${formatPrice(event.price)}</td>
                <td>
                    <div class="ticket-info">
                        <strong>${event.sold} / ${event.capacity}</strong>
                        <span>${event.capacity - event.sold} left</span>
                    </div>
                </td>
                <td><span class="status-badge">${event.status}</span></td>
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
    let date = new Date(dateTime);

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

    return "$" + price;
}

function viewEvent(id) {
    let event = getEventById(id);

    if (!event) {
        return;
    }

    alert(
        "Event: " + event.title + "\n" +
        "Category: " + event.category + "\n" +
        "Description: " + event.description + "\n" +
        "Organiser: " + event.organiser + "\n" +
        "Date & Time: " + formatDateTime(event.dateTime) + "\n" +
        "Location: " + event.location + "\n" +
        "Price: " + formatPrice(event.price) + "\n" +
        "Status: " + event.status + "\n" +
        "Tickets: " + event.sold + " / " + event.capacity
    );
}

function editEvent(id) {
    let event = getEventById(id);

    if (!event) {
        return;
    }

    let newTitle = prompt("Edit event title:", event.title);
    if (newTitle === null || newTitle.trim() === "") {
        return;
    }

    let newDescription = prompt("Edit description (max 100 words):", event.description);
    if (newDescription === null || newDescription.trim() === "") {
        return;
    }

    let newOrganiser = prompt("Edit organiser:", event.organiser);
    if (newOrganiser === null || newOrganiser.trim() === "") {
        return;
    }

    let newStatus = prompt("Edit status (Draft, Confirmed, Cancelled):", event.status);
    if (newStatus === null || newStatus.trim() === "") {
        return;
    }

    event.title = newTitle.trim();
    event.description = newDescription.trim();
    event.organiser = newOrganiser.trim();
    event.status = newStatus.trim();
    event.capacity = 2;

    if (event.sold > 2) {
        event.sold = 2;
    }

    sortEvents();
    showStats();
    showEvents();

    alert("Event updated");
}

function deleteEvent(id) {
    let confirmed = confirm("Are you sure you want to delete this event?");

    if (!confirmed) {
        return;
    }

    events = events.filter(function (event) {
        return event.id !== id;
    });


    showStats();
    showEvents();

    alert("Event deleted");
}

function getEventById(id) {
    for (let i = 0; i < events.length; i++) {
        if (events[i].id === id) {
            return events[i];
        }
    }

    return null;
}