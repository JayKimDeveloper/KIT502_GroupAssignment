let events = [
    {
        id: 1,
        title: "Web Development Bootcamp",
        category: "Workshop",
        description: "Learn HTML, CSS and JavaScript in a practical beginner friendly session.",
        organiser: "TechEvents UTAS",
        dateTime: "2025-07-05T14:00",
        location: "Launceston Campus",
        price: 15,
        sold: 1,
        capacity: 2,
        status: "Confirmed"
    },
    {
        id: 2,
        title: "Cyber Security Workshop",
        category: "Workshop",
        description: "A simple session about cyber safety, passwords and common online risks.",
        organiser: "UTAS IT Club",
        dateTime: "2025-06-20T10:30",
        location: "Launceston Campus",
        price: 0,
        sold: 0,
        capacity: 2,
        status: "Draft"
    },
    {
        id: 3,
        title: "AI Hackathon 2025",
        category: "Hackathon",
        description: "Students solve real world problems using AI ideas, teamwork and creativity.",
        organiser: "TechEvents UTAS",
        dateTime: "2025-06-12T09:00",
        location: "Hobart Campus",
        price: 10,
        sold: 2,
        capacity: 2,
        status: "Confirmed"
    },
    {
        id: 4,
        title: "Cloud Computing Seminar",
        category: "Seminar",
        description: "An introduction to cloud computing, hosting, storage and modern platforms.",
        organiser: "School of ICT",
        dateTime: "2025-05-28T13:15",
        location: "Online",
        price: 22,
        sold: 1,
        capacity: 2,
        status: "Cancelled"
    }
];

document.addEventListener("DOMContentLoaded", function () {
    sortEvents();
    showStats();
    showEvents();
});

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
        totalTickets = totalTickets + events[i].sold;
        totalRevenue = totalRevenue + (events[i].sold * events[i].price);
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

    let newDescription = prompt("Edit description (max 100 words):", event.description);
    if (newDescription === null || newDescription.trim() === "") {
        return;
    }

    if (countWords(newDescription) > 100) {
        alert("Description must be 100 words or less.");
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

    newStatus = newStatus.trim();

    if (
        newStatus !== "Draft" &&
        newStatus !== "Confirmed" &&
        newStatus !== "Cancelled"
    ) {
        alert("Status must be Draft, Confirmed, or Cancelled.");
        return;
    }

    event.title = newTitle.trim();
    event.description = newDescription.trim();
    event.organiser = newOrganiser.trim();
    event.status = newStatus;

    sortEvents();
    showStats();
    showEvents();

    alert("Event updated");
}

function deleteEvent(id) {
    let isConfirmed = confirm("Are you sure you want to delete this event?");

    if (!isConfirmed) {
        return;
    }

    for (let i = 0; i < events.length; i++) {
        if (events[i].id === id) {
            events.splice(i, 1);
            break;
        }
    }

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

function countWords(text) {
    let words = text.trim().split(/\s+/);
    let total = 0;

    for (let i = 0; i < words.length; i++) {
        if (words[i] !== "") {
            total++;
        }
    }

    return total;
}