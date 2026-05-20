<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Careers Fair 2025 | TechEvents UTAS</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css')}}">
    <link rel="stylesheet" href="{{ asset('css/events.css')}}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
</head>
<body>
    @include('partials.navbar')

    <div class="page">
        <div class="container">
            <a href="{{ url('/api/events') }}" style="color: var(--primary); font-size: 1rem; display: inline-block; margin: 32px 0 0 0; text-decoration: none;">← Back to Events</a>
            <div class="event-detail-container">
                <div class="event-detail-main">
                    <img src="{{ asset($data->image_path) }}" alt="{{ $data -> title }}" class="event-detail-image">
                    <div class="event-badge" style="background: #7A1F2B;">Career Event</div>
                    <div class="event-title">{{ $data->title }}</div>
                    <div class="event-meta-row">
                        <span class="meta-item"><span class="material-icons">event</span>{{$data->start_datetime}}</span>
                        <span class="meta-item"><span class="material-icons">schedule</span>{{$data->start_datetime}} - {{$data->end_datetime}}</span>
                        <span class="meta-item"><span class="material-icons">location_on</span>{{$data->location}}</span>
                        <span class="meta-item price"><span class="material-icons">attach_money</span>{{$data->price}}</span>
                        <span class="meta-item"><span class="material-icons">groups</span>{{$data->available_seats}} / {{$data->capacity}}</span>
                    </div>
                    <div class="event-progress">
                        {{-- <div style="display: flex; justify-content: space-between; font-size: 0.97rem; color: #5A4A4A;">
                            <span>Tickets Sold</span>
                            <span>20% {{ 100 - $data->available_seats / $data->capacity }} </span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-bar-fill" style="width: 20%;"></div>
                        </div> --}}
                    </div>
                    <div class="about-section">
                        <h4>About This Event</h4>
                        <p> {{$data->description}}</p>
                        {{-- <b>Event Agenda</b>
                        <ul class="event-agenda">
                            <li>11:00 AM – Doors Open</li>
                            <li>11:30 AM – Career Tips Presentation</li>
                            <li>12:00 PM – Company Expo Begins</li>
                            <li>2:30 PM – Final Networking</li>
                        </ul> --}}
                    </div>
                </div>
                <div class="event-detail-side">
                    <div style="font-size: 1.5rem; color: var(--primary); font-weight: 700; margin-bottom: 2px;">{{$data->price}}</div>
                    <div style="font-size: 0.98rem; color: #5A4A4A; margin-bottom: 16px;">per ticket</div>
                    <a href="{{url('api/bookings')}}" class="btn btn-primary" style="width: 100%; margin-bottom: 18px;">Buy Tickets</a>
                    <div style="font-size: 1.08rem; font-weight: 600; margin-bottom: 8px;">Event Details</div>
                    <div style="font-size: 0.97rem; color: #5A4A4A;">
                        <b>Category</b><br>{{$data->category->name}}<br><br>
                        <b>Location</b><br>{{$data->location}}<br><br>
                        <b>Date & Time</b><br>{{ $data->start_datetime }}<br>{{ $data->start_datetime }}
                    </div>
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
                © 2026 TechEvents UTAS
            </div>
        </div>
    </footer>



    <script>
        const csrf = document.querySelector('meta[name=csrf-token]').content;
    

        async function handleBuyTicket(eventId, btn) {
            // Visitor: redirect to login
            if (!currentUser) {
                window.location.href = '/login';
                return;
            }

            // Only attendees may book
            if (currentUser.role !== 'attendee') {
                alert('Only attendees can buy tickets.');
                return;
            }

            btn.disabled     = true;
            btn.textContent  = 'Booking";

            try {
                const res  = await fetch('/api/bookings', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({ event_id: eventId }),
                });

                const json = await res.json();

                if (res.ok) {
                    alert(json.message || 'Booking confirmed!');
                    // Refresh events to reflect updated seat count
                    await loadEvents();
                } else {
                    const msg = json.message || 'Booking failed. Please try again.';
                    alert(msg);
                    btn.disabled    = false;
                    btn.textContent = 'Buy Ticket';
                }
            } catch (e) {
                alert('Network error. Please try again.');
                btn.disabled    = false;
                btn.textContent = 'Buy Ticket';
            }
        }


        function loadEvents(event) {

            const capacity = {{ $data->capacity }};
            const availableSeats = {{ $data->available_seats }};
            const soldSeats = capacity - availableSeats;
            const soldPercent = capacity > 0 ? Math.round((soldSeats / capacity) * 100) : 0;

            const progress_conatiner = document.querySelector('.event-progress')
            progress_container.innerHTML = `
                <div style="display: flex; justify-content: space-between; font-size: 0.97rem; color: #5A4A4A;">
                    <span>Tickets Sold</span>
                    <span>${soldPercent}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-bar-fill" style="width: ${soldPercent}%;"></div>
                </div>
            `;
        }

        loadEvents();

    </script>
</body>
</html>
