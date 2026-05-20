<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data->title }} | TechEvents UTAS</title>

    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('partials.navbar')

    <div class="page">
        <div class="container">

            <a href="{{ url('/events') }}"
               style="color: var(--primary); font-size: 1rem; display: inline-block; margin: 32px 0 0 0; text-decoration: none;">
                ← Back to Events
            </a>

            <div class="event-detail-container">

                <div class="event-detail-main">
                    <img
                        src="{{ $data->image_url ?? $defaultImage }}"
                        alt="{{ $data->title }}"
                        class="event-detail-image"
                        onerror="this.onerror=null; this.src='{{ $defaultImage }}';"
                    >

                    <div class="event-badge" style="background: #7A1F2B;">
                        Career Event
                    </div>

                    <div class="event-title">
                        {{ $data->title }}
                    </div>

                    <div class="event-meta-row">
                        <span class="meta-item">
                            <span class="material-icons">groups</span>
                            {{ $data->available_seats ?? $data->capacity }} / {{ $data->capacity }}
                        </span>

                        <span class="meta-item">
                            <span class="material-icons">schedule</span>
                            {{ $data->start_datetime }} - {{ $data->end_datetime }}
                        </span>

                        <span class="meta-item">
                            <span class="material-icons">location_on</span>
                            {{ $data->location }}
                        </span>

                        <span class="meta-item price">
                            <span class="material-icons">attach_money</span>
                            {{ $data->price }}
                        </span>

                        <span class="meta-item">
                            <span class="material-icons">groups</span>
                            {{ $data->available_seats ?? $data->capacity }} / {{ $data->capacity }}
                        </span>
                    </div>

                    <div
                        class="event-progress"
                        data-capacity="{{ $data->capacity }}"
                        data-available-seats="{{ $data->available_seats ?? $data->capacity }}">
                    </div>

                    <div class="about-section">
                        <h4>About This Event</h4>
                        <p>{{ $data->description }}</p>
                    </div>
                </div>

                <div class="event-detail-side">
                    <div style="font-size: 1.5rem; color: var(--primary); font-weight: 700; margin-bottom: 2px;">
                        {{ $data->price }}
                    </div>

                    <div style="font-size: 0.98rem; color: #5A4A4A; margin-bottom: 16px;">
                        per ticket
                    </div>

                    <a href="{{ route('booking', ['event_id' => $data->id]) }}"
                       class="btn btn-primary"
                       style="width: 100%; margin-bottom: 18px; display: block; text-align: center;">
                        Buy Tickets
                    </a>

                    <div style="font-size: 1.08rem; font-weight: 600; margin-bottom: 8px;">
                        Event Details
                    </div>

                    <div style="font-size: 0.97rem; color: #5A4A4A;">
                        <b>Category</b><br>
                        {{ $data->category->name }}<br><br>

                        <b>Location</b><br>
                        {{ $data->location }}<br><br>

                        <b>Date & Time</b><br>
                        {{ $data->start_datetime }}<br>
                        {{ $data->end_datetime }}
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
        function loadEvents() {
            const progressContainer = document.querySelector('.event-progress');

            if (!progressContainer) {
                return;
            }

            const capacity = Number(progressContainer.dataset.capacity || 0);
            const availableSeats = Number(progressContainer.dataset.availableSeats || 0);

            const soldSeats = capacity - availableSeats;
            const soldPercent = capacity > 0 ? Math.round((soldSeats / capacity) * 100) : 0;

            progressContainer.innerHTML = `
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