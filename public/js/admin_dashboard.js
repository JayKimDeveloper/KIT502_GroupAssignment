
let users = [];
let events = [];

document.addEventListener("DOMContentLoaded", async function () {
    const createUserButton = document.getElementById("createUserBtn");

    if (createUserButton) {
        createUserButton.addEventListener("click", createUser);
    }

    await loadDashboard();
});

async function loadDashboard() {
    try {
        await Promise.all([
            loadStats(),
            loadUsers(),
            loadEvents()
        ]);

        renderUsers();
        renderEvents();
        setMessage("");
    } catch (error) {
        setMessage(error.message || "Please log in as an admin to view this dashboard.");
    }
}

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

async function loadStats() {
    const result = await apiRequest("/api/admin/stats");
    const stats = result.data;

    document.getElementById("totalUsers").textContent = stats.total_users;
    document.getElementById("totalEvents").textContent = stats.total_events;
    document.getElementById("upcomingEvents").textContent = stats.upcoming_events;
    document.getElementById("totalRegistrations").textContent = stats.total_registrations;
}

async function loadUsers() {
    const result = await apiRequest("/api/admin/users");
    users = result.data;
}

async function loadEvents() {
    const result = await apiRequest("/api/admin/events");
    events = result.data;
    sortEvents();
}

function sortEvents() {
    events.sort(function (a, b) {
        return new Date(b.start_datetime) - new Date(a.start_datetime);
    });
}

function renderUsers() {
    const tableBody = document.getElementById("usersTableBody");
    tableBody.innerHTML = "";

    if (users.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="5">No users found.</td></tr>`;
        return;
    }

    for (let i = 0; i < users.length; i++) {
        const user = users[i];

        tableBody.innerHTML += `
            <tr>
                <td>${escapeHtml(user.name)}</td>
                <td>${escapeHtml(user.email)}</td>
                <td><span class="role-badge">${escapeHtml(user.role)}</span></td>
                <td>${formatDate(user.created_at)}</td>
                <td class="actions-cell">
                    <button type="button" class="action-btn edit-btn" onclick="editUser(${user.id})" title="Edit user"></button>
                    <button type="button" class="action-btn role-btn" onclick="changeUserRole(${user.id})" title="Promote or demote user">Role</button>
                    <button type="button" class="action-btn delete-btn" onclick="deleteUser(${user.id})" title="Delete user"></button>
                </td>
            </tr>
        `;
    }
}

function renderEvents() {
    const tableBody = document.getElementById("adminEventsTableBody");
    tableBody.innerHTML = "";

    if (events.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="9">No events found.</td></tr>`;
        return;
    }

    for (let i = 0; i < events.length; i++) {
        const event = events[i];
        const registered = Number(event.registered_count ?? (event.capacity - event.available_seats));

        tableBody.innerHTML += `
            <tr>
                <td>${escapeHtml(event.title)}</td>
                <td>${escapeHtml(event.organiser?.name || "Unknown")}</td>
                <td>${formatDateTime(event.start_datetime)}</td>
                <td>${event.capacity}</td>
                <td>${registered}</td>
                <td><span class="${event.status === "draft" ? "draft-badge" : "status-badge"}">${escapeHtml(event.status)}</span></td>
                <td class="view-text"><button type="button" onclick="viewEvent(${event.id})">View</button></td>
                <td class="actions-cell"><button type="button" class="action-btn edit-btn" onclick="editEvent(${event.id})" title="Edit event"></button></td>
                <td class="actions-cell"><button type="button" class="action-btn delete-btn" onclick="deleteEvent(${event.id})" title="Delete event"></button></td>
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
        month: "short",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true
    });
}

function formatDate(dateTime) {
    const date = new Date(dateTime);

    if (Number.isNaN(date.getTime())) {
        return "Not set";
    }

    return date.toLocaleDateString("en-AU", {
        day: "numeric",
        month: "short",
        year: "numeric"
    });
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
        "Status: " + event.status
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

    const newStatus = prompt("Edit status (draft, published, cancelled):", event.status);
    if (newStatus === null || !["draft", "published", "cancelled"].includes(newStatus.trim())) {
        alert("Status must be draft, published, or cancelled.");
        return;
    }

    try {
        await apiRequest(`/api/events/${id}`, {
            method: "PUT",
            body: JSON.stringify({
                title: newTitle.trim(),
                status: newStatus.trim()
            })
        });

        await loadDashboard();
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
        await apiRequest(`/api/events/${id}`, {
            method: "DELETE"
        });

        await loadDashboard();
        alert("Event deleted.");
    } catch (error) {
        alert(error.message);
    }
}

async function createUser() {
    const name = prompt("Enter user name:");
    if (name === null || name.trim() === "") {
        return;
    }

    const email = prompt("Enter user email:");
    if (email === null || email.trim() === "") {
        return;
    }

    const role = prompt("Enter role (admin, organiser, attendee):", "attendee");
    if (role === null || !["admin", "organiser", "attendee"].includes(role.trim())) {
        alert("Role must be admin, organiser, or attendee.");
        return;
    }

    const password = prompt("Enter temporary password, for example Pass@123:");
    if (password === null || password.trim().length < 6) {
        alert("Password must be at least 6 characters.");
        return;
    }

    try {
        await apiRequest("/api/admin/users", {
            method: "POST",
            body: JSON.stringify({
                name: name.trim(),
                email: email.trim(),
                role: role.trim(),
                password: password.trim(),
                password_confirmation: password.trim()
            })
        });

        await loadDashboard();
        alert("User created.");
    } catch (error) {
        alert(error.message);
    }
}

async function editUser(id) {
    const user = getUserById(id);

    if (!user) {
        return;
    }

    const newName = prompt("Edit user name:", user.name);
    if (newName === null || newName.trim() === "") {
        return;
    }

    const newEmail = prompt("Edit user email:", user.email);
    if (newEmail === null || newEmail.trim() === "") {
        return;
    }

    const newRole = prompt("Edit role (admin, organiser, attendee):", user.role);
    if (newRole === null || !["admin", "organiser", "attendee"].includes(newRole.trim())) {
        alert("Role must be admin, organiser, or attendee.");
        return;
    }

    try {
        await apiRequest(`/api/admin/users/${id}`, {
            method: "PUT",
            body: JSON.stringify({
                name: newName.trim(),
                email: newEmail.trim(),
                role: newRole.trim()
            })
        });

        await loadDashboard();
        alert("User updated.");
    } catch (error) {
        alert(error.message);
    }
}

async function changeUserRole(id) {
    const user = getUserById(id);

    if (!user) {
        return;
    }

    const newRole = prompt("Promote/demote role (admin, organiser, attendee):", user.role);
    if (newRole === null || !["admin", "organiser", "attendee"].includes(newRole.trim())) {
        alert("Role must be admin, organiser, or attendee.");
        return;
    }

    try {
        await apiRequest(`/api/admin/users/${id}/role`, {
            method: "PATCH",
            body: JSON.stringify({ role: newRole.trim() })
        });

        await loadDashboard();
        alert("Role updated.");
    } catch (error) {
        alert(error.message);
    }
}

async function deleteUser(id) {
    const confirmed = confirm("Are you sure you want to delete this user?");

    if (!confirmed) {
        return;
    }

    try {
        await apiRequest(`/api/admin/users/${id}`, {
            method: "DELETE"
        });

        await loadDashboard();
        alert("User deleted.");
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

function getUserById(id) {
    for (let i = 0; i < users.length; i++) {
        if (users[i].id === id) {
            return users[i];
        }
    }

    return null;
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
    document.getElementById("adminMessage").textContent = message;
}

function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}