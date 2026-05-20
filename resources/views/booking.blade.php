<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Bookings | TechEvents UTAS</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>
<body>
@include('partials.navbar')

<main class="page bookings-page">
    <div class="container">

        <section class="bookings-header">
            <h1 class="page-title">My Bookings</h1>
            <p class="page-subtitle">View your booked events and cancel before the 1-day cutoff.</p>
            <p id="bookingMessage" class="page-subtitle"></p>
        </section>

        {{-- This section only appears when user comes from Buy Tickets button --}}
        @if(request('event_id'))
            <section class="bookings-panel" style="margin-bottom: 24px;">
                <h2 class="page-title" style="font-size: 1.6rem;">Book Tickets</h2>

                <p class="page-subtitle">
                    Select how many tickets you want to buy for this event.
                </p>

                <form id="ticketBookingForm">
                    <input type="hidden" id="eventId" value="{{ request('event_id') }}">

                    <div style="margin-bottom: 16px;">
                        <label for="ticketQuantity">Number of tickets</label>
                        <input
                            type="number"
                            id="ticketQuantity"
                            min="1"
                            value="1"
                            required
                            style="width: 100%; padding: 10px; margin-top: 6px;"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Confirm Booking
                    </button>

                    <p id="ticketBookingMessage" class="page-subtitle" style="margin-top: 12px;"></p>
                </form>
            </section>
        @endif

        <section class="bookings-panel">
            <div class="table-wrapper">
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Event</th>
                            <th>Date &amp; Time</th>
                            <th>Location</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTableBody"></tbody>
                </table>
            </div>
        </section>

    </div>
</main>

<footer class="footer">
    <div class="container">
        <p>TechEvents UTAS — connecting students with the tech community.</p>
    </div>
    <div class="footer-bottom">
        <div class="container">
            © 2025 TechEvents UTAS
        </div>
    </div>
</footer>

<script>
    window.APP_URL = "{{ url('/') }}";

    const ticketBookingForm = document.getElementById('ticketBookingForm');

    if (ticketBookingForm) {
        ticketBookingForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const eventId = document.getElementById('eventId').value;
            const quantity = document.getElementById('ticketQuantity').value;
            const message = document.getElementById('ticketBookingMessage');

            message.textContent = '';

            try {
                const res = await fetch(`${window.APP_URL}/api/bookings`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        event_id: eventId,
                        quantity: quantity
                    })
                });

                const data = await res.json();

                if (res.status === 401) {
                    window.location.href = `${window.APP_URL}/login`;
                    return;
                }

                if (res.ok) {
                    message.textContent = data.message || 'Booking confirmed successfully.';
                    message.style.color = 'green';

                    setTimeout(() => {
                        window.location.href = `${window.APP_URL}/my_bookings`;
                    }, 1000);
                } else {
                    message.textContent = data.message || 'Booking failed. Please try again.';
                    message.style.color = 'red';
                }
            } catch (error) {
                message.textContent = 'Network error. Please try again.';
                message.style.color = 'red';
            }
        });
    }
</script>

<script src="{{ asset('js/booking.js') }}"></script>
</body>
</html>