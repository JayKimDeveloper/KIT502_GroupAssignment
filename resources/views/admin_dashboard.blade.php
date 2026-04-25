<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | TechEvents UTAS</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="index.html" class="nav-brand">Tech<span>Events</span></a>

            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="events.html">Events</a></li>
                <li><a href="create_event.html">Create Event</a></li>
                <li><a href="manage_events.html">Manage Events</a></li>
                <li><a href="admin_dashboard.html" class="active">Admin</a></li>
            </ul>

            <div class="nav-actions">
                <a href="login.html" class="btn btn-outline">Log in</a>
                <a href="register.html" class="btn btn-primary">Sign up</a>
            </div>
        </div>
    </nav>

    <main class="page admin-dashboard-page">
        <div class="container">

            <section class="page-header">
                <h1 class="page-title">Admin Dashboard</h1>
                <p class="page-subtitle">Manage users, events, and view system statistics</p>
            </section>

            <section class="stats-grid">
                <div class="stats-card">
                    <div class="stats-info">
                        <h3>Total Users</h3>
                        <p id="totalUsers">4</p>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-info">
                        <h3>Total Events</h3>
                        <p id="totalEvents">4</p>
                    </div>

                </div>

                <div class="stats-card">
                    <div class="stats-info">
                        <h3>Confirmed Events</h3>
                        <p id="confirmedEvents">3</p>
                    </div>
                </div>

                <div class="stats-card">
                    <div class="stats-info">
                        <h3>Total Registrations</h3>
                        <p id="totalRegistrations">175</p>
                    </div>
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="panel-header">
                    <h2>User Management</h2>
                </div>

                <div class="table-wrapper">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registered</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="usersTableBody">
                            <!-- Users will come from JavaScript -->
                        </tbody>
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

                        <tbody id="adminEventsTableBody">
                            <!-- Events will come from JavaScript -->
                        </tbody>
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
    <script src="js/admin_dashboard.js"></script>
</body>
</html>