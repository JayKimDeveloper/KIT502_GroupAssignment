
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard | TechEvents UTAS</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
</head>
<body>
@include('partials.navbar')

    <main class="page admin-dashboard-page">
        <div class="container">

            <section class="page-header">
                <h1 class="page-title">Admin Dashboard</h1>
                <p class="page-subtitle">Manage users, events, and view system statistics</p>
                <p id="adminMessage" class="page-subtitle"></p>
            </section>

            <section class="stats-grid">
                <div class="stats-card">
                    <div class="stats-info">
                        <h3>Total Users</h3>
                        <p id="totalUsers">0</p>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-info">
                        <h3>Total Events</h3>
                        <p id="totalEvents">0</p>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-info">
                        <h3>Upcoming Events</h3>
                        <p id="upcomingEvents">0</p>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-info">
                        <h3>Total Registrations</h3>
                        <p id="totalRegistrations">0</p>
                    </div>
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="panel-header panel-header-with-action">
                    <h2>User Management</h2>
                    <button type="button" class="btn btn-primary" id="createUserBtn">Create User</button>
                </div>

                <div class="table-wrapper">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="usersTableBody"></tbody>
                    </table>
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="panel-header">
                    <h2>Event Management</h2>
                </div>

                <div class="table-wrapper">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Event Title</th>
                                <th>Organiser</th>
                                <th>Date &amp; Time</th>
                                <th>Capacity</th>
                                <th>Registered</th>
                                <th>Status</th>
                                <th>View</th>
                                <th>Edit</th>
                                <th>Delete</th>
                            </tr>
                        </thead>

                        <tbody id="adminEventsTableBody"></tbody>
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
                © 2025 TechEvents UTAS. All rights reserved.
            </div>
        </div>
    </footer>

    <script>window.APP_URL = "{{ url('/') }}";</script>
    <script src="{{ asset('js/admin_dashboard.js') }}"></script>
</body>
</html>