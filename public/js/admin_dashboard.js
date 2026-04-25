const eventStorageKey = "techevents_events";
const userStorageKey = "techevents_users";

let events = [];
let users = [];

document.addEventListener("DOMContentLoaded", async function () {
    await loadUsers();
    await loadEvents();

    sortEvents();
    renderAdminStats();
    renderUsers();
    renderEvents();
});

async function loadEvents() {
    const response = await fetch("data/events.json");
    events = await response.json();
}

async function loadUsers() {
    const response = await fetch("data/users.json");
    users = await response.json();
}
function saveUsers() {
    localStorage.setItem(userStorageKey, JSON.stringify(users));
}

function saveEvents() {
    localStorage.setItem(eventStorageKey, JSON.stringify(events));
}

function sortEvents() {
    events.sort(function (a, b) {
        return new Date(b.dateTime) - new Date(a.dateTime);
    });
}

function renderAdminStats() {
    let confirmedCount = 0;
    let registrations = 0;

    for (let i = 0; i < events.length; i++) {
        if (events[i].status === "Confirmed") {
            confirmedCount++;
        }

        registrations += events[i].sold;
    }

    document.getElementById("totalUsers").textContent = users.length;
    document.getElementById("totalEvents").textContent = events.length;
    document.getElementById("confirmedEvents").textContent = confirmedCount;
    document.getElementById("totalRegistrations").textContent = registrations;
}

function renderUsers() {
    let tableBody = document.getElementById("usersTableBody");
    tableBody.innerHTML = "";

    for (let i = 0; i < users.length; i++) {
        let user = users[i];

        tableBody.innerHTML += `
            <tr>
                <td>${user.name}</td>
                <td>${user.email}</td>
                <td><span class="role-badge">${user.role}</span></td>
                <td>${user.registered}</td>
                <td><span class="status-badge">${user.status}</span></td>
                <td class="actions-cell">
                    <button type="button" class="action-btn edit-btn" onclick="editUser(${user.id})" aria-label="Edit"></button>
                    <button type="button" class="action-btn delete-btn" onclick="deleteUser(${user.id})" aria-label="Delete"></button>
                </td>
            </tr>
        `;
    }
}

function renderEvents() {
    let tableBody = document.getElementById("adminEventsTableBody");
    tableBody.innerHTML = "";

    for (let i = 0; i < events.length; i++) {
        let event = events[i];

        tableBody.innerHTML += `
            <tr>
                <td>${event.title}</td>
                <td>${event.organiser}</td>
                <td>${formatDateTime(event.dateTime)}</td>
                <td>${event.capacity}</td>
                <td>${event.sold}</td>
                <td><span class="${event.status === "Draft" ? "draft-badge" : "status-badge"}">${event.status}</span></td>
                <td class="view-text">
                    <button type="button" onclick="viewEvent(${event.id})">View</button>
                </td>
                <td class="actions-cell">
                    <button type="button" class="action-btn edit-btn" onclick="editEvent(${event.id})" aria-label="Edit"></button>
                </td>
                <td class="actions-cell">
                    <button type="button" class="action-btn delete-btn" onclick="deleteEvent(${event.id})" aria-label="Delete"></button>
                </td>
            </tr>
        `;
    }
}

function formatDateTime(dateTime) {
    let date = new Date(dateTime);

    return date.toLocaleString("en-AU", {
        day: "numeric",
        month: "short",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true
    });
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
        "Status: " + event.status
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

    let newStatus = prompt("Edit status (Draft, Confirmed, Cancelled):", event.status);
    if (newStatus === null || newStatus.trim() === "") {
        return;
    }

    event.title = newTitle.trim();
    event.status = newStatus.trim();

    sortEvents();
    renderAdminStats();
    renderEvents();

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

    renderAdminStats();
    renderEvents();

    alert("Event deleted");
}

function editUser(id) {
    let user = getUserById(id);

    if (!user) {
        return;
    }

    let newName = prompt("Edit user name:", user.name);
    if (newName === null || newName.trim() === "") {
        return;
    }

    let newRole = prompt("Edit role (Attendee or Organiser):", user.role);
    if (newRole === null || newRole.trim() === "") {
        return;
    }

    user.name = newName.trim();
    user.role = newRole.trim();

    saveUsers();
    renderUsers();

    alert("User updated");
}

function deleteUser(id) {
    let confirmed = confirm("Are you sure you want to delete this user?");

    if (!confirmed) {
        return;
    }

    users = users.filter(function (user) {
        return user.id !== id;
    });

    saveUsers();
    renderAdminStats();
    renderUsers();

    alert("User deleted");
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