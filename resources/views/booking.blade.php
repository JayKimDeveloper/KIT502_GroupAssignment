
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
    <nav class="navbar">
        <div class="container">
            <a href="{{ url('/index') }}" class="nav-brand">Tech<span>Events</span></a>

            <ul class="nav-links">
                <li><a href="{{ url('/index') }}">Home</a></li>
                <li><a href="{{ url('/events') }}">Events</a></li>
                <li><a href="{{ url('/booking') }}" class="active">My Bookings</a></li>
                <li><a href="{{ url('/create_event') }}">Create Event</a></li>
                <li><a href="{{ url('/manage_events') }}">Manage Events</a></li>
                <li><a href="{{ url('/admin_dashboard') }}">Admin</a></li>
            </ul>

            <div class="nav-actions">
                <a href="{{ url('/login') }}" class="btn btn-outline">Log in</a>
                <a href="{{ url('/register') }}" class="btn btn-primary">Sign up</a>
            </div>
        </div>
    </nav>

    <main class="page bookings-page">
        <div class="container">
            <section class="bookings-header">
                <h1 class="page-title">My Bookings</h1>
                <p class="page-subtitle">View your booked events and cancel before the 1-day cutoff.</p>
                <p id="bookingMessage" class="page-subtitle"></p>
            </section>

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

    <script src="{{ asset('js/booking.js') }}"></script>
</body>
</html>     