<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Events | TechEvents</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>

<body>
@include('partials.navbar')

    <div class="page">

        <div class="container">

            <h1>All Events</h1>

            <!-- FILTERS -->
            <div class="filters-section">
                <div class="filter-group">
                    <label for="category-filter">Category</label>
                    <select id="category-filter">
                        <option value="">All Categories</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="date-filter">Date</label>
                    <input type="date" id="date-filter">
                </div>

                <div class="filter-group">
                    <label for="location-filter">Location</label>
                    <select id="location-filter">
                        <option value="">All Locations</option>
                        <option value="Hobart">Hobart Campus</option>
                        <option value="Launceston">Launceston Campus</option>
                    </select>
                </div>

                <button id="apply-filter-btn" class="btn btn-primary">Apply Filter</button>
            </div>

            <!-- EVENTS GRID -->
            <div id="events-grid" class="events-grid">
                <p id="events-loading" style="color:#888;">Loading events…</p>
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

    <script>
        const csrf = document.querySelector('meta[name=csrf-token]').content;

        // ── Helpers ──────────────────────────────────────────────
        function formatDate(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            return d.toLocaleDateString('en-AU', { day: 'numeric', month: 'long', year: 'numeric' });
        }

        function formatPrice(price) {
            return parseFloat(price) === 0 ? 'Free' : '$' + parseFloat(price).toFixed(2);
        }

        // ── Auth state ───────────────────────────────────────────
        let currentUser = null;

        async function loadMe() {
            try {
                const res  = await fetch("{{ url('/me') }}", {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const json = await res.json();
                currentUser = json.authenticated ? json.user : null;
            } catch (e) {
                currentUser = null;
            }
        }

        // ── Categories ───────────────────────────────────────────
        async function loadCategories() {
            try {
                const res  = await fetch("{{ url('/api/categories') }}", {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const json = await res.json();
                const sel  = document.getElementById('category-filter');
                (json.data || []).forEach(cat => {
                    const opt = document.createElement('option');
                    opt.value       = cat.id;
                    opt.textContent = cat.name;
                    sel.appendChild(opt);
                });
            } catch (e) { /* silently ignore */ }
        }

        // ── Event card builder ───────────────────────────────────
        function buildEventCard(event) {


            console.log(JSON.stringify(event, null, 2));

            const imgSrc  = event.image_path ? event.image_path : '{{ asset("images/event1.png") }}';
            const badge   = event.category ? event.category.name : 'Event';
            const seats   = event.available_seats;
            const isFull  = seats === 0;

            let btnHtml;
            if (currentUser && (currentUser.role === 'organiser' || currentUser.role === 'admin')) {
                // Organisers / admins cannot book tickets
                btnHtml = `<button class="btn btn-primary btn-block" disabled style="opacity:0.5;cursor:not-allowed;">Only for atteendee</button>`;
            } else if (isFull) {
                btnHtml = `<button class="btn btn-primary btn-block" disabled style="opacity:0.5;cursor:not-allowed;">Sold Out</button>`;
            } else {
                btnHtml = `<a href="{{ url('/events') }}/${event.id}"  class="btn btn-primary btn-block details-ticket-btn" data-event-id="${event.id}">View Details</a>`;
            }

            return `
                <div class="event-card">
                    <div class="event-image">
                        <img src="${imgSrc}" alt="${event.title}" onerror="this.src='{{ asset('images/event1.png') }}'">
                    </div>
                    <div class="event-badge">${badge}</div>
                    <h3>${event.title}</h3>
                    <div class="event-meta">
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">location_on</span> ${event.location}</span>
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">event</span> ${formatDate(event.start_datetime)}</span>
                        <span class="meta-item price">${formatPrice(event.price)}</span>
                        <span class="meta-item"><span class="material-icons" style="font-size:1em;vertical-align:middle;">confirmation_number</span> ${seats} seat${seats !== 1 ? 's' : ''} left</span>
                    </div>
                    ${btnHtml}
                </div>`;
        }

        // Load events
        async function loadEvents() {
            const grid    = document.getElementById('events-grid');
            grid.innerHTML = '<p id="events-loading" style="color:#888;">Loading events…</p>';

            const categoryId = document.getElementById('category-filter').value;
            const date       = document.getElementById('date-filter').value;
            const location   = document.getElementById('location-filter').value;

            const params = new URLSearchParams();
            if (categoryId) params.set('category_id', categoryId);
            if (date)       params.set('date', date);
            if (location)   params.set('location', location);

            const url = "{{ url('/api/events') }}" + (params.toString() ? '?' + params.toString() : '');

            try {
                const res  = await fetch(url, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const json = await res.json();
                const events = json.data || [];

                if (events.length === 0) {
                    grid.innerHTML = '<p style="color:#888;">No events found matching your filters.</p>';
                    return;
                }

                grid.innerHTML = events.map(buildEventCard).join('');

                // Attach Event Details handlers
                // grid.querySelectorAll('.details-ticket-btn').forEach(btn => {
                //     btn.addEventListener('click', () => handleEventDetails(parseInt(btn.dataset.eventId), btn));
                // });
            } catch (e) {
                grid.innerHTML = '<p style="color:#c0392b;">Failed to load events. Please try again.</p>';
            }
        }

        // ── Filter button ────────────────────────────────────────
        document.getElementById('apply-filter-btn').addEventListener('click', loadEvents);

        // ── Init ─────────────────────────────────────────────────
        (async () => {
            await Promise.all([loadMe(), loadCategories()]);
            await loadEvents();
        })();
    </script>

</body>

</html>