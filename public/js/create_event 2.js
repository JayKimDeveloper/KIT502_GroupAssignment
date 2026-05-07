document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("createEventForm");
    const resetButton = document.getElementById("resetBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    

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

        if (capacity < 1 || capacity > 2) {
           alert("Capacity must be between 1 and 2.");
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

    cancelBtn.addEventListener("click", function () {
        if (confirm("Are you sure you want to cancel? All changes will be lost.")) {
            window.location.href = "index.html";
        }
    });
});