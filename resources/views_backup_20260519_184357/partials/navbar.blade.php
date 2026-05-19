{{--
    Navbar partial — uses the team's existing classes from create_event.html /
    login.html etc.: navbar, container, nav-brand, nav-links, nav-actions, btn.
    Drop this in to replace the inline <nav> block on every page.

    Usage:    @include('partials.navbar')

    Login state and role visibility (assignment spec §2):
      - Visitor:    Home, Events, Login, Sign up
      - Attendee:   Home, Events, My Bookings, Welcome, Logout
      - Organiser:  Home, Events, Create Event, Manage Events, Welcome, Logout
      - Admin:      Home, Events, Manage Events, Admin, Welcome, Logout

    Active link uses request()->is(). URL patterns match README routes.
--}}

@php
    $user = auth()->user();
@endphp

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

            {{-- Organisers & admins can create / manage events --}}
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

            {{-- Attendees see their bookings --}}
            @if ($user && $user->isAttendee())
                <li>
                    <a href="{{ url('/my_bookings') }}"
                       class="{{ request()->is('my_bookings*') ? 'active' : '' }}">My Bookings</a>
                </li>
            @endif

            {{-- Admin dashboard --}}
            @if ($user && $user->isAdmin())
                <li>
                    <a href="{{ url('/admin_dashboard') }}"
                       class="{{ request()->is('admin_dashboard*') ? 'active' : '' }}">Admin</a>
                </li>
            @endif
        </ul>

        <div class="nav-actions">
            @guest
                {{-- Not logged in --}}
                <a href="{{ url('/login') }}" class="btn btn-outline">Log in</a>
                <a href="{{ url('/register') }}" class="btn btn-primary">Sign up</a>
            @else
                {{-- Logged in --}}
                <span class="nav_welcome">Welcome, {{ $user->name }}</span>

                {{-- Logout must be POST for CSRF + session invalidation --}}
                <form method="POST" action="{{ url('/logout') }}" class="nav-logout-form">
                    @csrf
                    <button type="submit" class="btn btn_outline">Logout</button>
                </form>
            @endguest
        </div>
    </div>
</nav>