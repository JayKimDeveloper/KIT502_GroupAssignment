
let bookings = [];

document.addEventListener("DOMContentLoaded", async function () {
    await loadBookings();
    renderBookings();
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

async function loadBookings() {
    try {
        const result = await apiRequest(APP_URL + "/api/bookings/mine");
        bookings = result.data;
        setMessage("");
    } catch (error) {
        bookings = [];
        setMessage(error.message || "Please log in as an attendee to view bookings.");
    }
}

function renderBookings() {
    const tableBody = document.getElementById("bookingsTableBody");
    tableBody.innerHTML = "";

    if (bookings.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7">No bookings found.</td></tr>`;
        return;
    }

    for (let i = 0; i < bookings.length; i++) {
        const booking = bookings[i];
        const event = booking.event || {};
        const actionButton = booking.can_be_cancelled
            ? `<button type="button" class="cancel-booking-btn" onclick="cancelBooking(${booking.id})">Cancel</button>`
            : `<span class="muted-text">Not available</span>`;

        tableBody.innerHTML += `
            <tr>
                <td>${escapeHtml(booking.booking_reference)}</td>
                <td>${escapeHtml(event.title || "Unknown event")}</td>
                <td>${formatDateTime(event.start_datetime)}</td>
                <td>${escapeHtml(event.location || "Not set")}</td>
                <td>${escapeHtml(booking.payment_status)}</td>
                <td><span class="booking-status">${escapeHtml(booking.status)}</span></td>
                <td>${actionButton}</td>
            </tr>
        `;
    }
}

async function cancelBooking(id) {
    const confirmed = confirm("Are you sure you want to cancel this booking?");

    if (!confirmed) {
        return;
    }

    try {
        await apiRequest(`${APP_URL}/api/bookings/${id}`, {
            method: "DELETE"
        });

        await loadBookings();
        renderBookings();
        alert("Booking cancelled.");
    } catch (error) {
        alert(error.message);
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
    document.getElementById("bookingMessage").textContent = message;
}

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}