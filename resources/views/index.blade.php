<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TechEvents UTAS</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

</head>

<body>
@include('partials.navbar')

    <div class="page">
        <!-- HERO -->
        <section class="hero">
            <div class="container hero-content">
                <div class="hero-text">
                    <h1><b>Discover Tech Events at UTAS</b></h1>
                    <p>Join workshops, hackathons, and networking events designed for technology enthusiasts.</p>
                    <a href="{{ url('/events') }}" class="btn btn-primary">Browse Events</a>
                </div>
                <div class="hero-image">
                    <img src="{{ asset('images/landing.png') }}" alt="Tech event">
                </div>
            </div>
        </section>

        <div class="container">
            <!-- LATEST EVENTS -->
            <section class="featured">
                <h2>Latest Events</h2>
                <div id="recent-events-grid" class="event-grid">
                    <p id="recent-loading" style="color:#888;">Loading events…</p>
                </div>
            </section>
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

    <script>
        function formatDate(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleDateString('en-AU', { day: 'numeric', month: 'long', year: 'numeric' });
        }

        function formatPrice(price) {
            return parseFloat(price) === 0 ? 'Free' : '$' + parseFloat(price).toFixed(2);
        }

        function buildRecentCard(event) {
            const imgSrc = event.image_path ? event.image_path : '{{ asset("images/event1.png") }}';
            const badge  = event.category ? event.category.name : 'Event';
            const seats  = event.available_seats;

            return `
                <div class="event-card">
                    <div class="event-image">
                        <img src="${imgSrc}" alt="${event.title}" onerror="this.src='{{ asset('images/event1.png') }}'">
                    </div>
                    <div class="event-badge">${badge}</div>
                    <h3>${event.title}</h3>
                    <div class="event-meta">
                        <span class="meta-item">📍 ${event.location}</span>
                        <span class="meta-item">📅 ${formatDate(event.start_datetime)}</span>
                        <span class="meta-item price">${formatPrice(event.price)}</span>
                        <span class="meta-item">🎟 ${seats} seat${seats !== 1 ? 's' : ''} left</span>
                    </div>
                    <a href="{{ url('/events') }}" class="btn btn-primary btn-block">View Details</a>
                </div>`;
        }

        async function loadRecentEvents() {
            const grid    = document.getElementById('recent-events-grid');
            const loading = document.getElementById('recent-loading');
            try {
                const res  = await fetch("{{ url('/api/events/recent') }}", {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const json = await res.json();
                const events = json.data || [];

                console.log("JKYH Event recents: "+events);

                if (events.length === 0) {
                    grid.innerHTML = '<p style="color:#888;">No upcoming events yet.</p>';
                    return;
                }
                grid.innerHTML = events.map(buildRecentCard).join('');
            } catch (e) {
                if (loading) loading.textContent = 'Could not load events.';
            }
        }

        loadRecentEvents();
    </script>

</body>

</html>
