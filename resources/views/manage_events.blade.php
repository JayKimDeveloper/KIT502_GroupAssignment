
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Events | TechEvents UTAS</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/manage_events.css') }}">
</head>
<body>
@include('partials.navbar')

    <main class="manage-events-page">
        <div class="container">

            <div class="manage-header">
                <div>
                    <h1 class="page-title">Manage Events</h1>
                    <p class="page-subtitle">Create, edit, and manage your events</p>
                    <p id="manageMessage" class="page-subtitle"></p>
                </div>

                <a href="{{ url('/create_event') }}" class="create-btn">
                    <span>+</span>
                    Create New Event
                </a>
            </div>

            <section class="stats-grid">
                <div class="stats-card">
                    <h3>Total Events</h3>
                    <p id="totalEvents">0</p>
                </div>

                <div class="stats-card">
                    <h3>Tickets Sold</h3>
                    <p id="ticketsSold">0</p>
                </div>

                <div class="stats-card">
                    <h3>Total Revenue</h3>
                    <p id="totalRevenue" class="revenue-text">$0</p>
                </div>
            </section>

            <section class="events-panel">
                <div class="panel-header">
                    <h2>Your Events</h2>
                </div>

                <div class="table-wrapper">
                    <table class="events-table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Organiser</th>
                                <th>Date &amp; Time</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Tickets</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="eventsTableBody"></tbody>
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

    <script>window.APP_URL = "{{ url('/') }}";</script>
    <script src="{{ asset('js/manage_events.js') }}"></script>
</body>
</html>