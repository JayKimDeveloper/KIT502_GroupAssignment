{{--
    Navigation bar — included by layouts/app.blade.php on every page.

    Visibility rules (Assignment spec §2 + §1):
      - Visitor (not logged in):     Home, Events, Login, Register
      - Attendee:                    Home, Events, My Bookings, [Welcome, name], Logout
      - Organiser:                   Home, Events, Manage Events, [Welcome, name], Logout
      - Admin:                       Home, Events, Manage Events, Admin Dashboard,
                                     [Welcome, name], Logout

    The `auth()->user()` helper returns null if no one is logged in.
    Role checks (isAdmin / isOrganiser / isAttendee) come from the User model.

    Active state: uses Laravel's request()->routeIs() helper. Make sure your
    routes have ->name('...') applied (see routes/web.php).
--}}

@php
    $user = auth()->user();
@endphp

<nav class="navbar">
    <div class="navbar__inner">

        {{-- Brand / logo — always links to landing page --}}
        <a href="{{ url('/') }}" class="navbar__brand">
            <span class="material-icons">school</span>
            UTAS Tech Events
        </a>

        {{-- Primary nav links (left side) --}}
        <ul class="navbar__menu">
            <li>
                <a href="{{ url('/') }}"
                   class="navbar__link {{ request()->is('/') ? 'is-active' : '' }}">
                    Home
                </a>
            </li>
            <li>
                <a href="{{ url('/events') }}"
                   class="navbar__link {{ request()->is('events*') ? 'is-active' : '' }}">
                    Events
                </a>
            </li>

            {{-- Attendee-only: My Bookings --}}
            @if ($user && $user->isAttendee())
                <li>
                    <a href="{{ url('/my-bookings') }}"
                       class="navbar__link {{ request()->is('my-bookings*') ? 'is-active' : '' }}">
                        My Bookings
                    </a>
                </li>
            @endif

            {{-- Organiser + Admin: Manage Events --}}
            @if ($user && ($user->isOrganiser() || $user->isAdmin()))
                <li>
                    <a href="{{ url('/manage-events') }}"
                       class="navbar__link {{ request()->is('manage-events*') || request()->is('create-event*') ? 'is-active' : '' }}">
                        Manage Events
                    </a>
                </li>
            @endif

            {{-- Admin only: Dashboard --}}
            @if ($user && $user->isAdmin())
                <li>
                    <a href="{{ url('/admin-dashboard') }}"
                       class="navbar__link {{ request()->is('admin-dashboard*') ? 'is-active' : '' }}">
                        Admin Dashboard
                    </a>
                </li>
            @endif
        </ul>

        {{-- Auth area (right side) --}}
        <div class="navbar__auth">
            @guest
                {{-- Not logged in --}}
                <a href="{{ url('/login') }}"
                   class="navbar__link {{ request()->is('login') ? 'is-active' : '' }}">
                    Login
                </a>
                <a href="{{ url('/register') }}"
                   class="navbar__btn navbar__btn--primary {{ request()->is('register') ? 'is-active' : '' }}">
                    Register
                </a>
            @else
                {{-- Logged in --}}
                <span class="navbar__welcome">Welcome, {{ $user->name }}</span>

                {{-- Logout MUST be a POST form (CSRF + session invalidation).
                     Don't change this to a GET <a> — it'd break CSRF protection. --}}
                <form method="POST" action="{{ url('/logout') }}" class="navbar__logout-form">
                    @csrf
                    <button type="submit" class="navbar__btn navbar__btn--ghost">Logout</button>
                </form>
            @endguest
        </div>

    </div>
</nav>