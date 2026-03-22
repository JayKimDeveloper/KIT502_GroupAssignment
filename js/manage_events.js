const storageKey = "techEventsManageStyle";

const defaultEvents = [
    {
        id: 1,
        title: "AI Hackathon 2025",
        category: "Hackathon",
        date: "2025-06-12",
        location: "Hobart Campus",
        price: 10,
        sold: 50,
        capacity: 100,
        status: "Published"
    },
    {
        id: 2,
        title: "Cyber Security Workshop",
        category: "Workshop",
        date: "2025-06-20",
        location: "Launceston Campus",
        price: 0,
        sold: 10,
        capacity: 40,
        status: "Published"
    },
    {
        id: 3,
        title: "Web Development Bootcamp",
        category: "Workshop",
        date: "2025-07-05",
        location: "Launceston Campus",
        price: 15,
        sold: 15,
        capacity: 30,
        status: "Published"
    }
];

let events = [];

$(document).ready(function () {
    loadEvents();
    renderStats();
    renderTable();

    $(document).on("click", ".edit-btn", function () {
        const eventId = Number($(this).data("id"));
        openEditModal(eventId);
    });

    $(document).on("click", ".view-btn", function () {
        const eventId = Number($(this).data("id"));
        viewEvent(eventId);
    });

    $(document).on("click", ".delete-btn", function () {
        const eventId = Number($(this).data("id"));
        deleteEvent(eventId);
    });

    $("#editEventForm").on("submit", function (e) {
        e.preventDefault();
        saveChanges();
    });

    $("#closeModal, #cancelModal").on("click", closeModal);

    $("#editModal").on("click", function (e) {
        if (e.target.id === "editModal") {
            closeModal();
        }
    });
});

function loadEvents() {
    const savedEvents = localStorage.getItem(storageKey);

    if (savedEvents) {
        events = JSON.parse(savedEvents);
    } else {
        events = [...defaultEvents];
        saveEvents();
    }
}

function saveEvents() {
    localStorage.setItem(storageKey, JSON.stringify(events));
}

function renderStats() {
    const totalEvents = events.length;
    const totalTickets = events.reduce((sum, eventItem) => sum + Number(eventItem.sold), 0);
    const totalRevenue = events.reduce((sum, eventItem) => {
        return sum + (Number(eventItem.price) * Number(eventItem.sold));
    }, 0);

    $("#totalEvents").text(totalEvents);
    $("#ticketsSold").text(totalTickets);
    $("#totalRevenue").text("$" + totalRevenue);
}

function renderTable() {
    const tableBody = $("#eventsTableBody");
    tableBody.empty();

    events.forEach(function (eventItem) {
        const row = `
            <tr>
                <td>${eventItem.title}</td>
                <td><span class="category-badge">${eventItem.category}</span></td>
                <td>${formatDate(eventItem.date)}</td>
                <td>${eventItem.location}</td>
                <td>${formatPrice(eventItem.price)}</td>
                <td>
                    <div class="ticket-info">
                        <strong>${eventItem.sold} / ${eventItem.capacity}</strong>
                        <span>${eventItem.capacity - eventItem.sold} left</span>
                    </div>
                </td>
                <td><span class="status-badge ${eventItem.status.toLowerCase()}">${eventItem.status}</span></td>
                <td class="actions-cell">
                    <button type="button" class="action-btn view-btn" data-id="${eventItem.id}" title="View">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>

                    <button type="button" class="action-btn edit-btn" data-id="${eventItem.id}" title="Edit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                        </svg>
                    </button>

                    <button type="button" class="action-btn delete-btn" data-id="${eventItem.id}" title="Delete">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 6h18"></path>
                            <path d="M8 6V4h8v2"></path>
                            <path d="M19 6l-1 14H6L5 6"></path>
                            <path d="M10 11v6"></path>
                            <path d="M14 11v6"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `;

        tableBody.append(row);
    });
}

function formatDate(dateString) {
    const eventDate = new Date(dateString);

    return eventDate.toLocaleDateString("en-AU", {
        day: "numeric",
        month: "long",
        year: "numeric"
    });
}

function formatPrice(price) {
    if (Number(price) === 0) {
        return "Free";
    }

    return "$" + price;
}

function viewEvent(eventId) {
    const selectedEvent = events.find(eventItem => eventItem.id === eventId);

    if (!selectedEvent) {
        return;
    }

    alert(
        "Event: " + selectedEvent.title + "\n" +
        "Category: " + selectedEvent.category + "\n" +
        "Date: " + formatDate(selectedEvent.date) + "\n" +
        "Location: " + selectedEvent.location
    );
}

function openEditModal(eventId) {
    const selectedEvent = events.find(eventItem => eventItem.id === eventId);

    if (!selectedEvent) {
        return;
    }

    $("#editId").val(selectedEvent.id);
    $("#editTitle").val(selectedEvent.title);
    $("#editCategory").val(selectedEvent.category);
    $("#editDate").val(selectedEvent.date);
    $("#editLocation").val(selectedEvent.location);
    $("#editPrice").val(selectedEvent.price);
    $("#editStatus").val(selectedEvent.status);
    $("#editSold").val(selectedEvent.sold);
    $("#editCapacity").val(selectedEvent.capacity);

    $("#editModal").css("display", "flex");
    $("body").addClass("no-scroll");
}

function closeModal() {
    $("#editModal").hide();
    $("body").removeClass("no-scroll");
}

function saveChanges() {
    const eventId = Number($("#editId").val());
    const eventIndex = events.findIndex(eventItem => eventItem.id === eventId);

    if (eventIndex === -1) {
        return;
    }

    events[eventIndex].title = $("#editTitle").val().trim();
    events[eventIndex].category = $("#editCategory").val();
    events[eventIndex].date = $("#editDate").val();
    events[eventIndex].location = $("#editLocation").val().trim();
    events[eventIndex].price = Number($("#editPrice").val());
    events[eventIndex].status = $("#editStatus").val();
    events[eventIndex].sold = Number($("#editSold").val());
    events[eventIndex].capacity = Number($("#editCapacity").val());

    saveEvents();
    renderStats();
    renderTable();
    closeModal();
}

function deleteEvent(eventId) {
    const selectedEvent = events.find(eventItem => eventItem.id === eventId);

    if (!selectedEvent) {
        return;
    }

    const confirmDelete = confirm('Delete "' + selectedEvent.title + '"?');

    if (!confirmDelete) {
        return;
    }

    events = events.filter(eventItem => eventItem.id !== eventId);
    saveEvents();
    renderStats();
    renderTable();
}