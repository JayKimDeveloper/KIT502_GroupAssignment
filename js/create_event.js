document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("createEventForm");
    const resetButton = document.getElementById("resetBtn");

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        let title = document.getElementById("title").value.trim();
        let description = document.getElementById("description").value.trim();
        let startDate = document.getElementById("start_date").value;
        let endDate = document.getElementById("end_date").value;
        let location = document.getElementById("location").value.trim();
        let capacity = document.getElementById("capacity").value;
        let price = document.getElementById("price").value;
        let status = document.getElementById("status").value;

        if (
            title === "" ||
            description === "" ||
            startDate === "" ||
            endDate === "" ||
            location === "" ||
            capacity === "" ||
            price === "" ||
            status === ""
        ) {
            alert("Please fill in all fields.");
            return;
        }

        if (endDate < startDate) {
            alert("End date cannot be before start date.");
            return;
        }

        alert("New event has been created");
        window.location.href = "manage_events.html";
    });

    resetButton.addEventListener("click", function () {
        alert("Form has been reset");
    });
});