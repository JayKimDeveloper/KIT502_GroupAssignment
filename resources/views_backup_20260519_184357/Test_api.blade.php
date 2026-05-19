<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>API Test Page</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: monospace; padding: 20px; max-width: 900px; }
        button { padding: 8px 16px; margin: 5px 0; cursor: pointer; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; max-height: 350px; }
        h2 { margin-top: 30px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        h3 { margin-top: 20px; color: #555; }
        input, select, textarea { padding: 6px; margin: 3px 0; display: block; width: 100%; box-sizing: border-box; }
        .row { display: flex; gap: 10px; }
        .row > * { flex: 1; }
        .section { background: #fff; padding: 15px; border: 1px solid #eee; border-radius: 6px; margin-bottom: 20px; }
        .badge { display: inline-block; padding: 2px 8px; background: #e0f0ff; border-radius: 3px; font-size: 12px; }
    </style>
</head>
<body>

<h1>API Test Page</h1>
<p>Logged in as: <strong>{{ auth()->user()?->name ?? 'Not logged in' }}</strong>
   <span class="badge">role: {{ auth()->user()?->role ?? 'guest' }}</span></p>

@guest
    <p>⚠️ Log in first at <a href="/login">/login</a> to test authenticated endpoints.</p>
@endguest

{{-- =================================================== --}}
<h2>📅 Events</h2>

<div class="section">
    <h3>POST /api/events — Create event</h3>
    <form id="createEventForm">
        <input name="title" placeholder="Title" value="Test Event" required>
        <textarea name="description" placeholder="Description" required>Test description</textarea>
        <div class="row">
            <input name="start_datetime" type="datetime-local" value="2026-12-01T18:00" required>
            <input name="end_datetime"   type="datetime-local" value="2026-12-01T21:00" required>
        </div>
        <input name="location" placeholder="Location" value="Test Venue" required>
        <div class="row">
            <input name="capacity" type="number" value="50" required>
            <input name="price"    type="number" step="0.01" value="0" required>
        </div>
        <select name="status" required>
            <option value="draft">Draft</option>
            <option value="published" selected>Published</option>
            <option value="cancelled">Cancelled</option>
        </select>
        <input name="category_id" type="number" placeholder="Category ID (1-5)" value="1">
        <input name="image" type="file" accept="image/*">
        <button type="submit">Create Event</button>
    </form>
    <pre id="createEventOutput">Response will appear here...</pre>
</div>

<div class="section">
    <h3>PUT /api/events/{id} — Update event</h3>
    <form id="updateEventForm">
        <input name="id" type="number" placeholder="Event ID" required>
        <input name="title" placeholder="New title (optional)">
        <input name="capacity" type="number" placeholder="New capacity (optional)">
        <button type="submit">Update</button>
    </form>
    <pre id="updateEventOutput">Response will appear here...</pre>
</div>

<div class="section">
    <h3>DELETE /api/events/{id} — Delete event</h3>
    <form id="deleteEventForm">
        <input name="id" type="number" placeholder="Event ID to delete" required>
        <button type="submit">Delete</button>
    </form>
    <pre id="deleteEventOutput">Response will appear here...</pre>
</div>

{{-- =================================================== --}}
<h2>🎟️ Bookings</h2>

<div class="section">
    <h3>POST /api/bookings — Buy ticket (attendee only)</h3>
    <form id="createBookingForm">
        <input name="event_id" type="number" placeholder="Event ID" required>
        <button type="submit">Book Ticket</button>
    </form>
    <pre id="createBookingOutput">Response will appear here...</pre>
</div>

<div class="section">
    <h3>GET /api/bookings/mine — My bookings</h3>
    <button id="getMyBookings">Fetch My Bookings</button>
    <pre id="myBookingsOutput">Response will appear here...</pre>
</div>

<div class="section">
    <h3>DELETE /api/bookings/{id} — Cancel booking</h3>
    <form id="deleteBookingForm">
        <input name="id" type="number" placeholder="Booking ID to cancel" required>
        <button type="submit">Cancel</button>
    </form>
    <pre id="deleteBookingOutput">Response will appear here...</pre>
</div>

<div class="section">
    <h3>GET /api/events/{id}/bookings — Attendees for an event (organiser/admin)</h3>
    <form id="getEventBookingsForm">
        <input name="event_id" type="number" placeholder="Event ID" required>
        <button type="submit">Get Attendees</button>
    </form>
    <pre id="eventBookingsOutput">Response will appear here...</pre>
</div>

<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function apiCall(url, options, outputEl) {
        const out = document.getElementById(outputEl);
        out.textContent = 'Loading...';
        return fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                ...(options.headers || {}),
            },
            ...options,
        })
        .then(async res => {
            const text = await res.text();
            let parsed;
            try { parsed = JSON.parse(text); } catch { parsed = text; }
            out.textContent = `Status: ${res.status}\n\n${
                typeof parsed === 'string' ? parsed : JSON.stringify(parsed, null, 2)
            }`;
        })
        .catch(err => out.textContent = 'Error: ' + err.message);
    }

    /* ---------- Events ---------- */

    document.getElementById('createEventForm').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        if (!formData.get('image')?.size) formData.delete('image');
        apiCall('/api/events', { method: 'POST', body: formData }, 'createEventOutput');
    });

    document.getElementById('updateEventForm').addEventListener('submit', e => {
        e.preventDefault();
        const data = Object.fromEntries(new FormData(e.target));
        const id = data.id; delete data.id;
        for (const k in data) if (!data[k]) delete data[k];
        apiCall(`/api/events/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        }, 'updateEventOutput');
    });

    document.getElementById('deleteEventForm').addEventListener('submit', e => {
        e.preventDefault();
        const id = new FormData(e.target).get('id');
        if (!confirm(`Really delete event ${id}?`)) return;
        apiCall(`/api/events/${id}`, { method: 'DELETE' }, 'deleteEventOutput');
    });

    /* ---------- Bookings ---------- */

    document.getElementById('createBookingForm').addEventListener('submit', e => {
        e.preventDefault();
        const eventId = new FormData(e.target).get('event_id');
        apiCall('/api/bookings', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ event_id: Number(eventId) }),
        }, 'createBookingOutput');
    });

    document.getElementById('getMyBookings').addEventListener('click', () => {
        apiCall('/api/bookings/mine', { method: 'GET' }, 'myBookingsOutput');
    });

    document.getElementById('deleteBookingForm').addEventListener('submit', e => {
        e.preventDefault();
        const id = new FormData(e.target).get('id');
        if (!confirm(`Really cancel booking ${id}?`)) return;
        apiCall(`/api/bookings/${id}`, { method: 'DELETE' }, 'deleteBookingOutput');
    });

    document.getElementById('getEventBookingsForm').addEventListener('submit', e => {
        e.preventDefault();
        const eventId = new FormData(e.target).get('event_id');
        apiCall(`/api/events/${eventId}/bookings`, { method: 'GET' }, 'eventBookingsOutput');
    });
</script>

</body>
</html>