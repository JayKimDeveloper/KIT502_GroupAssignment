<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Events | TechEvents</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/events.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>

<body>

    <nav class="navbar">
        <div class="container">
            <a href="index.html" class="nav-brand">Tech<span>Events</span></a>

            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
                <li><a href="events.html" class="active">Events</a></li>
                <li><a href="create_event.html">Create Event</a></li>
                <li><a href="manage_events.html">Manage Events</a></li>
                <li><a href="admin_dashboard.html">Admin</a></li>
            </ul>

            <div class="nav-actions">
                <a href="login.html" class="btn btn-outline">Log in</a>
                <a href="register.html" class="btn btn-primary">Sign up</a>
            </div>
        </div>
    </nav>


    <div class="page">

        <div class="container">

            <h1>All Events</h1>

            <!-- FILTERS -->
            <div class="filters-section">
                <div class="filter-group">
                    <label>Category</label>
                    <select>
                        <option>All Categories</option>
                        <option>Workshop</option>
                        <option>Hackathon</option>
                        <option>Tech Talk</option>
                        <option>Networking</option>
                        <option>Career Event</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Date</label>
                    <input type="date" placeholder="dd/mm/yyyy">
                </div>

                <div class="filter-group">
                    <label>Location</label>
                    <select>
                        <option>All Locations</option>
                        <option>Hobart Campus</option>
                        <option>Launceston Campus</option>
                    </select>
                </div>

                <button class="btn btn-primary">Apply Filter</button>
            </div>

            <!-- EVENTS GRID -->
            <div class="events-grid">

                <div class="event-card">
                    <div class="event-image">
                        <img src="images/event1.png" alt="AI Hackathon">
                    </div>
                    <div class="event-badge">Hackathon</div>
                    <h3>AI Hackathon 2025</h3>
                    <div class="event-meta">
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">location_on</span> Hobart Campus</span>
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">event</span> 12 June 2025</span>
                        <span class="meta-item price">$10</span>
                    </div>
                    <a href="event_details/event-ai-hackathon.html" class="btn btn-primary btn-block">View Details</a>
                </div>

                <div class="event-card">
                    <div class="event-image">
                        <img src="images/event2.png" alt="Cyber Security Workshop">
                    </div>
                    <div class="event-badge">Workshop</div>
                    <h3>Cyber Security Workshop</h3>
                    <div class="event-meta">
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">location_on</span> Launceston Campus</span>
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">event</span> 20 June 2025</span>
                        <span class="meta-item price">Free</span>
                    </div>
                    <a href="event_details/event-cyber-workshop.html" class="btn btn-primary btn-block">View Details</a>
                </div>

                <div class="event-card">
                    <div class="event-image">
                        <img src="images/event3.png" alt="Tech Industry Panel">
                    </div>
                    <div class="event-badge">Tech Talk</div>
                    <h3>Tech Industry Panel Discussion</h3>
                    <div class="event-meta">
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">location_on</span> Hobart Campus</span>
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">event</span> 25 June 2025</span>
                        <span class="meta-item price">Free</span>
                    </div>
                    <a href="event_details/event-panel-discussion.html" class="btn btn-primary btn-block">View Details</a>
                </div>

                <div class="event-card">
                    <div class="event-image">
                        <img src="images/event4.png" alt="Networking Night">
                    </div>
                    <div class="event-badge">Networking</div>
                    <h3>Student Tech Networking Night</h3>
                    <div class="event-meta">
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">location_on</span> Hobart Campus</span>
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">event</span> 28 June 2025</span>
                        <span class="meta-item price">$5</span>
                    </div>
                    <a href="event_details/event-networking-night.html" class="btn btn-primary btn-block">View Details</a>
                </div>

                <div class="event-card">
                    <div class="event-image">
                        <img src="images/event5.png" alt="Web Development">
                    </div>
                    <div class="event-badge">Workshop</div>
                    <h3>Web Development Bootcamp</h3>
                    <div class="event-meta">
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">location_on</span> Launceston Campus</span>
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">event</span> 5 July 2025</span>
                        <span class="meta-item price">$15</span>
                    </div>
                    <a href="event_details/event-web-bootcamp.html" class="btn btn-primary btn-block">View Details</a>
                </div>

                <div class="event-card">
                    <div class="event-image">
                        <img src="images/event6.png" alt="Tech Careers Fair">
                    </div>
                    <div class="event-badge">Career Event</div>
                    <h3>Tech Careers Fair 2025</h3>
                    <div class="event-meta">
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">location_on</span> Hobart Campus</span>
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">event</span> 10 July 2025</span>
                        <span class="meta-item price">Free</span>
                    </div>
                    <a href="event_details/event-careers-fair.html" class="btn btn-primary btn-block">View Details</a>
                </div>

            </div>

        </div>

    </div>


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
</body>

</html>