
@php
    $user = auth()->user();
@endphp

{{-- Comm Nav bar --}}
<nav class="navbar">

    <div class="container">
        <a href="{{ url('/') }}" class="nav-brand">Tech<span>Events</span></a>

        <ul class="nav-links">
            <li>
                <a href="{{ url('/') }}"
                   class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
            </li>
            <li>
                <a href="{{ url('/events') }}"
                   class="{{ request()->is('events*') ? 'active' : '' }}">Events</a>
            </li>

            {{-- Organiser, Admin --}}
            @if ($user && ($user->isOrganiser() || $user->isAdmin()))
                <li>
                    <a href="{{ url('/create_event') }}"
                       class="{{ request()->is('create_event*') ? 'active' : '' }}">Create Event</a>
                </li>
                <li>
                    <a href="{{ url('/manage_events') }}"
                       class="{{ request()->is('manage_events*') ? 'active' : '' }}">Manage Events</a>
                </li>
            @endif

            {{-- Attendees -> bookings --}}
            @if ($user && $user->isAttendee())
                <li>
                    <a href="{{ url('/my_bookings') }}"
                       class="{{ request()->is('my_bookings*') ? 'active' : '' }}">My Bookings</a>
                </li>
            @endif

            {{-- Admin -> Admin dashboard --}}
            @if ($user && $user->isAdmin())
                <li>
                    <a href="{{ url('/admin_dashboard') }}"
                       class="{{ request()->is('admin_dashboard*') ? 'active' : '' }}">Admin</a>
                </li>
            @endif
        </ul>

        <div class="nav-actions">
            @guest
                {{-- For Not login --}}
                <a href="{{ url('/login') }}" class="btn btn-outline">Log in</a>
                <a href="{{ url('/register') }}" class="btn btn-primary">Sign up</a>
            @else
                {{-- For login --}}
                <span class="nav_welcome">Welcome, {{ $user->name }}</span>

                {{-- Logout --}}
                <form method="POST" action="{{ url('/logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="btn btn_outline">Logout</button>
                </form>
            @endguest
        </div>
    </div>
</nav>