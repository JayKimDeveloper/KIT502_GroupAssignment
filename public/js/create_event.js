
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("createEventForm");
    const resetButton = document.getElementById("resetBtn");
    const cancelButton = document.getElementById("cancelBtn");
    const messageBox = document.getElementById("formMessage");
    const categorySelect = document.getElementById("categoryId");

    loadCategories();

    form.addEventListener("submit", async function (event) {
        event.preventDefault();
        clearMessage();

        const startDatetime = document.getElementById("startDatetime").value;
        const endDatetime = document.getElementById("endDatetime").value;
        const capacity = Number(document.getElementById("capacity").value);
        const price = Number(document.getElementById("price").value);

        if (endDatetime < startDatetime) {
            showMessage("End date cannot be before start date.", true);
            return;
        }

        if (capacity < 1) {
            showMessage("Capacity must be at least 1.", true);
            return;
        }

        if (price < 0) {
            showMessage("Price cannot be negative.", true);
            return;
        }

        const formData = new FormData(form);

        try {
            const response = await fetch(APP_URL + "/api/events", {
                method: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken()
                },
                credentials: "same-origin",
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                showMessage(getErrorMessage(result), true);
                return;
            }

            showMessage("New event has been created.", false);
            form.reset();

            setTimeout(function () {
                window.location.href = APP_URL + "/manage_events";
            }, 800);
        } catch (error) {
            showMessage("Something went wrong while creating the event.", true);
        }
    });

    resetButton.addEventListener("click", function () {
        clearMessage();
    });

    cancelButton.addEventListener("click", function () {
        if (confirm("Are you sure you want to cancel? All changes will be lost.")) {
            window.location.href = APP_URL + "/manage_events";
        }
    });

    async function loadCategories() {
        try {
            const response = await fetch(APP_URL + "/api/categories", {
                headers: { "Accept": "application/json" },
                credentials: "same-origin"
            });

            const result = await response.json();

            if (!response.ok) {
                return;
            }

            for (let i = 0; i < result.data.length; i++) {
                const category = result.data[i];
                const option = document.createElement("option");
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            }
        } catch (error) {
            // The event can still be created without a category.
        }
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]').content;
    }

    function getErrorMessage(result) {
        if (result.errors) {
            const firstField = Object.keys(result.errors)[0];
            return result.errors[firstField][0];
        }

        return result.message || "The event could not be created.";
    }

    function showMessage(message, isError) {
        messageBox.textContent = message;
        messageBox.style.color = isError ? "#d9342b" : "#2b7a3d";
    }

    function clearMessage() {
        messageBox.textContent = "";
    }
});