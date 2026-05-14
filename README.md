# 🎓 UTAS Student Tech Events

> **KIT502 Web Development** | University of Tasmania
> **Repository:** https://github.com/JayKimDeveloper/KIT502_GroupAssignment

UTAS Student Tech Events is a web application that gives UTAS students one place to find, organise, and join technology-related events — workshops, tech talks, hackathons, and networking sessions.

The project was first developed as a static frontend and database design project for Assignment 1. It has since been migrated to a Laravel-based full-stack application for Assignment 2.

---

## Project Overview

UTAS Student Tech Events is designed for four types of users:

- **Visitors** — Browse available events
- **Attendees** — Register, log in, book tickets
- **Organisers** — Create, update, and manage their own events
- **Administrators** — Manage all users and events, view platform statistics

The system provides a role-based event management platform where university-related technology events can be published, browsed, and booked.

---

## Development Stages

| Stage | Focus | Status |
|-------|-------|--------|
| Assignment 1 | Frontend design and database planning | ✅ Completed |
| Assignment 2 | Full-stack Laravel implementation | 🧑‍🚒 In progress |

### Current Assignment 2 Progress

**Backend (complete):**

- Database migrations, Eloquent models, seeders
- Session-based authentication (register / login / logout / me)
- Events CRUD API + image upload + organiser ownership checks
- Bookings API (ticket purchase, capacity check, cancellation cutoff)
- Admin API (statistics, user management, role promotion/demotion)
- Categories endpoint for filter and create-event dropdowns

**Frontend (in progress):**

- Static HTML pages migrated into Blade templates
- Shared navbar partial with role-based menu visibility
- Login / Register pages wired to backend
- Event listing, create event, manage events, admin dashboard — being wired to API

**Infrastructure:**

- SQLite for local development
- MySQL planned for school server deployment
- API contract documented in `docs/API_LISTS.md`

---

## Technology Stack

### Frontend

- HTML, CSS, JavaScript
- jQuery v3.7.1
- Google Fonts: Poppins
- Google Material Icons

### Backend / Full-stack

- Laravel 10
- PHP 8.2
- Blade templates
- SQLite (local), MySQL (school server)
- Composer, Git/GitHub

---

## Local Setup Guide

### 1. Clone Repository

```bash
git clone https://github.com/JayKimDeveloper/KIT502_GroupAssignment.git
cd KIT502_GroupAssignment
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Create Environment File

```bash
cp .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Configure SQLite

Create an empty SQLite database file:

```bash
touch database/database.sqlite
```

Update `.env` so SQLite is used (the path is relative to Laravel's `database/` folder, so this works on every machine without editing):

```env
DB_CONNECTION=sqlite
DB_DATABASE=database.sqlite
```

> If Laravel can't find the file with the relative path on your machine, use an absolute path instead:
>
> ```env
> DB_DATABASE=/absolute/path/to/KIT502_GroupAssignment/database/database.sqlite
> ```

### 6. Run Migrations + Seed Test Data

```bash
php artisan migrate:fresh --seed
```

This creates all tables and populates them with:

- 1 admin, 1 organiser, 1 attendee (see [Test Accounts](#test-accounts) below)
- 5 categories
- 4 published events
- 1 sample booking

### 7. Link Storage (for event image uploads)

```bash
php artisan storage:link
```

This makes uploaded event images at `storage/app/public/events/` accessible via `/storage/events/...`.

### 8. Start Local Development Server

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

### 9. Clear Caches When Things Look Stale

After pulling new changes or editing `.env`:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Test Accounts

After running `php artisan migrate:fresh --seed`, the following accounts are available for local testing:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@test` | `admin123!@` |
| Organiser | `organiser@kit502.test` | `Organiser@123` |
| Attendee | `attendee@kit502.test` | `Attendee@123` |

> The admin account credentials must also be included in the final Assignment 2 submission README so markers can evaluate admin functionality.

---

## API Authentication (for frontend / Postman)

The backend uses **session-based authentication**, not tokens.

### From Blade pages (fetch calls)

Already handled by the shared layout. Just use `apiFetch()` if available, or include the CSRF token manually:

```js
fetch('/api/events', {
    method: 'POST',
    credentials: 'same-origin',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
    },
    body: JSON.stringify({ /* ... */ }),
});
```

### From Postman

CSRF is disabled on `/api/*` routes for development convenience, so you only need to log in once:

1. **POST** `http://localhost:8000/login`
   - Headers: `Accept: application/json`
   - Body (raw JSON): `{"email": "attendee@kit502.test", "password": "Attendee@123"}`
2. Postman saves the session cookie automatically.
3. All subsequent `/api/*` calls work without further setup.

> See `docs/API_LISTS.md` for the full request/response contract.

---

## Current Routes

### Page Routes (Blade views)

| Route | Page | View File |
|-------|------|-----------|
| `/` | Landing Page | `index.blade.php` |
| `/login` | Login | `login.blade.php` |
| `/register` | Registration | `register.blade.php` |
| `/events` | Events list | `events.blade.php` |
| `/create_event` | Create Event | `create_event.blade.php` |
| `/manage_events` | Event Management | `manage_events.blade.php` |
| `/my_bookings` | My Bookings (attendee) | `my_bookings.blade.php` |
| `/admin_dashboard` | Admin Dashboard | `admin_dashboard.blade.php` |

### Auth Actions (POST)

| Method | Route | Purpose |
|---|---|---|
| POST | `/register` | Submit registration |
| POST | `/login` | Submit login |
| POST | `/logout` | Log out |
| GET | `/me` | Current user info (used by navbar JS) |

### API Routes

Full request/response specs in [`docs/API_LISTS.md`](docs/API_LISTS.md).

| Method | Route | Who |
|---|---|---|
| GET | `/api/events` | Anyone |
| GET | `/api/events/recent` | Anyone |
| GET | `/api/events/{id}` | Anyone |
| GET | `/api/events/mine` | Organiser, Admin |
| POST | `/api/events` | Organiser, Admin |
| PUT | `/api/events/{id}` | Owner organiser, Admin |
| DELETE | `/api/events/{id}` | Owner organiser, Admin |
| GET | `/api/events/{id}/bookings` | Owner organiser, Admin |
| POST | `/api/bookings` | Attendee |
| GET | `/api/bookings/mine` | Attendee |
| DELETE | `/api/bookings/{id}` | Owner attendee |
| GET | `/api/categories` | Anyone |
| GET | `/api/admin/*` | Admin |

---

## API Documentation

Full request/response contract: [`docs/API_LISTS.md`](docs/API_LISTS.md) (last updated 13 May)

---

## Project Structure

```text
KIT502_GroupAssignment/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Auth, Event, Booking, Category, Admin controllers
│   │   └── Middleware/       # EnsureUserHasRole, VerifyCsrfToken (api/* excluded)
│   └── Models/               # User, Event, Booking, Category, Notification
├── bootstrap/
├── config/
├── database/
│   ├── migrations/           # users, categories, events, bookings, notifications
│   └── seeders/              # DatabaseSeeder (admin + sample data)
├── docs/
│   └── API_LISTS.md          # Full API contract
├── public/
│   ├── css/                  # variables.css, login_style.css, etc.
│   ├── data/
│   ├── images/
│   └── js/
├── resources/
│   └── views/
│       ├── partials/         # navbar.blade.php (shared across pages)
│       ├── layouts/          # app.blade.php (master layout)
│       └── *.blade.php       # page templates
├── routes/
│   └── web.php               # all routes (pages + API)
├── storage/
├── tests/
├── README.md                 # This file
├── composer.json
├── composer.lock
├── package.json
├── artisan
└── vite.config.js
```

---

## School Server Deployment Plan

The project will be deployed to the university-provided internal server (`usermin`) for final testing and submission.

Recommended workflow:

```bash
git pull
composer install --no-dev
php artisan migrate
php artisan config:clear
php artisan cache:clear
php artisan storage:link
```

For the school server, update `.env` to use MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=kit502-group-17
DB_USERNAME=<from kit502-group-17-mysql-pass.txt>
DB_PASSWORD=<from kit502-group-17-mysql-pass.txt>
```

Storage and bootstrap/cache permissions may need adjusting on the school server. Refer to deployment notes in the project notion / chat.

---

## Color Palette

> Source: [colorhunt.co](https://colorhunt.co)

| Role | Color Name | HEX |
|------|------------|-----|
| Primary | Warm Orange | `#E76F51` |
| Secondary | Burgundy | `#7A1F2B` |
| Accent | Golden | `#F4A261` |
| Background | Cream | `#FFF7ED` |
| Surface | Ivory | `#FFE8D6` |
| Text | Dark Brown | `#3A1F1F` |

---

## System Roles

| Role | Description |
|------|-------------|
| **Visitor** | Browse landing page and view events. Cannot book or modify anything. |
| **Attendee** | View events, book tickets, view registered events, cancel bookings before the cutoff. |
| **Organiser** | Create, update, delete their own events. View attendee lists for their events. |
| **Admin** | Manage all users (create/update/delete/promote/demote). Manage all events. View system statistics. |

---

## Features

### Core Features

- User registration and login (session-based)
- Role-based access control (admin / organiser / attendee / visitor)
- Event CRUD with image upload
- Event filtering (by category, date, location)
- Ticket booking with capacity validation
- Booking cancellation (until 1 day before the event)
- Admin dashboard with system statistics
- Admin user management (create/update/delete + role changes)

### Advanced Features *(optional)*

- Notification system (database schema ready)
- Payment simulation
- Waitlist when events are full
- Monthly calendar view

---

## Pages

| Page | Purpose |
|------|---------|
| Landing Page | Website intro + 2 most recent events (dynamic) |
| Registration | Create attendee or organiser account |
| Login | Sign in (with Remember Me) |
| Events | List of published events with filters |
| Create Event | Organiser form to create an event |
| Event Management | Organiser sees their events; attendee sees their bookings |
| Admin Dashboard | Stats + user/event management for admins |

---

## Team Responsibilities

### Team Progress Timeline

<img src="./docs/TeamProgress.png">

The project is divided among the team. Sidd's original auth work was not completed and has been absorbed by Jay.

---

### YoungHyun (Jay) Kim (`JayKimyh`) — Backend / Database / Auth

- Project setup, README management
- ERD and schema design
- Laravel project structure migration
- Database migrations, models, seeders
- Authentication API (register / login / logout / me)
- Events CRUD API + image upload
- Bookings API (ticket purchase, capacity check, cancellation)
- Admin API (stats, user management, role changes)
- API contract documentation
- Navigation bar (shared partial)
- Login + Register pages wired to backend (originally Sidd's work)

### Guneet (`guneet0526kaur-lang`) — Public Pages

- Landing page
- Events page (filtering UI + buy ticket flow)
- CSS for landing, events, variables
- Image assets
- Wiring the public pages to backend API (`/api/events`, `/api/events/recent`)

### Pragun (`pragun11`) — Event & Admin Interfaces

- Page design (Figma)
- Create Event form
- Event Management page
- Admin Dashboard layout
- Wiring create/manage/admin pages to backend API

### Siddharth (Sidd) — *Not completed*

Originally assigned to Register and Login pages; work not delivered. Reassigned to Jay.

---

## Team Naming Rules

To keep the Laravel project consistent and maintainable, all team members should follow the naming rules below.

### General Naming Convention

| Target | Rule | Example |
|---|---|---|
| Database tables | Plural `snake_case` | `users`, `events`, `bookings` |
| Database columns | `snake_case` | `start_datetime`, `booking_reference` |
| Form input names | `snake_case` | `password_confirmation`, `category_id` |
| HTML IDs | `camelCase` | `createEventForm`, `bookingQuantity` |
| JavaScript variables | `camelCase` | `eventTitle`, `bookingQuantity` |
| CSS classes | `kebab-case` | `event-card`, `stats-card` |
| Route names | Dot notation | `events.store`, `admin.dashboard` |
| URLs | `snake_case` | `/create_event`, `/manage_events` |

### Fixed Value Naming

#### User Roles
```
admin, organiser, attendee
```

#### Event Statuses
```
draft, published, cancelled
```

#### Booking Statuses
```
confirmed, cancelled
```

#### Payment Statuses
```
free, unpaid, paid
```

### Form Input Naming

#### Register Form
```
role, name, email, password, password_confirmation
```

#### Login Form
```
email, password, remember
```

#### Create Event Form
```
title, description, category_id, start_datetime, end_datetime,
location, capacity, price, status, image
```

### Notes

- Database values should use lowercase names.
- Form `name` attributes must match controller validation keys.
- Use Laravel's `route()` helper or `url()` instead of hardcoding `.html` links.
- Uploaded event images use `image` as the form input name; stored in DB as `image_path`.

---

## Team Expectations and Work Quality Standards

- **Communication:** WhatsApp group chat.
- **Internal Deadline:** Aim to complete assigned work at least 3 days before the final submission deadline.
- **Work Quality:** Code should be checked before commit and reviewed after commit where possible.
- **Task Distribution:** Each member is responsible for at least one screen and related functionality.
- **Mutual Support:** Ask questions, provide help, and review each other's work when needed.

---

## Database Design

### ERD

<img src="./docs/KIT502_GrupAssERD.drawio.png">

> The ERD above was the original Assignment 1 design. The Laravel migrations have been refactored to follow Laravel conventions (plural `snake_case` tables, `id` BIGINT auto-increment primary keys, FK constraints, timestamps). See actual schema below.

### Current Schema (Laravel migrations)

```
users
  id, name, email (unique), password (hashed),
  role enum(admin|organiser|attendee), timestamps

categories
  id, name (unique), timestamps

events
  id, organiser_id → users.id, category_id → categories.id (nullable),
  title, description, start_datetime, end_datetime, location,
  capacity, price, status enum(draft|published|cancelled),
  image_path, timestamps

bookings
  id, event_id → events.id, attendee_id → users.id,
  booking_reference (unique, BK-XXXXXXXX format),
  status enum(confirmed|cancelled),
  payment_status enum(free|unpaid|paid),
  timestamps
  UNIQUE (event_id, attendee_id)

notifications
  id, user_id → users.id, type, message, related_id,
  is_read, timestamps
```

### Original Assignment 1 Schema (deprecated)

> Kept for reference only — does not reflect the current Laravel migrations.

```sql
/**
 * Author: YoungHyun Kim
 * Version: 0.1
 * NOTE: Replaced by Laravel migrations in Assignment 2.
 */

-- User Table (supports admin, organiser, attendee roles)
CREATE TABLE user_TB (
    login_id      VARCHAR(100) NOT NULL,
    password      VARCHAR(255) NOT NULL,
    first_name    VARCHAR(30)  NOT NULL,
    last_name     VARCHAR(30)  NOT NULL,
    location      VARCHAR(100) NOT NULL,
    role          ENUM('admin', 'organiser', 'attendee') NOT NULL,
    email         VARCHAR(100) NOT NULL,
    update_date   DATE         NOT NULL,
    register_date DATE         NOT NULL,
    PRIMARY KEY (login_id)
);

CREATE TABLE category_TB (
    category_id   INT          NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    PRIMARY KEY (category_id)
);

CREATE TABLE event_TB (
    event_id     CHAR(12)     NOT NULL,
    title        VARCHAR(100) NOT NULL,
    description  TEXT         NOT NULL,
    login_id     VARCHAR(100) NOT NULL,
    img_url      VARCHAR(100),
    ticket_price INT          NOT NULL,
    capacity     INT          NOT NULL,
    status       ENUM('Draft', 'Confirmed', 'Cancelled') NOT NULL,
    category_ID  INT          NOT NULL,
    start_date   DATE         NOT NULL,
    end_date     DATE         NOT NULL,
    update_date  DATE         NOT NULL,
    PRIMARY KEY (event_id),
    FOREIGN KEY (login_id)    REFERENCES user_TB(login_id),
    FOREIGN KEY (category_ID) REFERENCES category_TB(category_id)
);

CREATE TABLE booking_TB (
    booking_id   CHAR(12)     NOT NULL,
    booking_date DATE         NOT NULL,
    member_cnt   INT          NOT NULL,
    event_id     CHAR(12)     NOT NULL,
    login_id     VARCHAR(100) NOT NULL,
    PRIMARY KEY (booking_id),
    FOREIGN KEY (event_id)  REFERENCES event_TB(event_id),
    FOREIGN KEY (login_id)  REFERENCES user_TB(login_id)
);

CREATE TABLE notification_TB (
    notification_id VARCHAR(12)  NOT NULL,
    login_id        VARCHAR(100) NOT NULL,
    booking_id      CHAR(12)     NOT NULL,
    type            VARCHAR(100) NOT NULL,
    message         TEXT,
    is_read         TINYINT(1)   NOT NULL,
    update_date     DATE         NOT NULL,
    PRIMARY KEY (notification_id),
    FOREIGN KEY (login_id)   REFERENCES user_TB(login_id),
    FOREIGN KEY (booking_id) REFERENCES booking_TB(booking_id)
);

CREATE TABLE activies_logs_TB (
    log_id          VARCHAR(25)  NOT NULL,
    login_id        VARCHAR(100) NOT NULL,
    action_type     VARCHAR(50)  NOT NULL,
    action_category VARCHAR(50)  NOT NULL,
    new_value       VARCHAR(255),
    create_date     DATE         NOT NULL,
    PRIMARY KEY (log_id),
    FOREIGN KEY (login_id) REFERENCES user_TB(login_id)
);
```

---

## Git and Security Notes

The following files and folders should not be committed:

- `.env`
- `vendor/`
- `node_modules/`
- SQLite database files (`database/database.sqlite`)
- Log files (`storage/logs/*.log`)
- Local tool metadata such as `.claude/`
- Local editor settings such as `.vscode/`

Before pushing, check for sensitive files:

```bash
git ls-files | grep -E "\.env$|sqlite$|pass|secret|key$|\.log$"
```

Only `.env.example` and standard Laravel config files should appear in the output.

---

## Useful Laravel Commands

```bash
# Development
php artisan serve              # Start local dev server
php artisan route:list         # See all routes
php artisan tinker             # Interactive shell

# Database
php artisan migrate            # Run new migrations
php artisan migrate:fresh      # Drop all tables and re-run
php artisan migrate:fresh --seed  # Drop, re-create, and seed test data
php artisan db:seed            # Re-run seeders

# Caches (run after pulling new changes or editing .env)
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## Future Development Plan

Next stage focuses on frontend integration:

- Wire registration / login UI to backend (done — Jay)
- Wire landing page and events page to `/api/events` (Guneet)
- Wire create event / manage events to backend (Pragun)
- Wire admin dashboard to `/api/admin/*` (Pragun)
- Demo video recording
- Final testing on school server
- Optional: advanced features (notifications, payment simulation, waitlist, calendar)

---

*UTAS Student Tech Events — KIT502 Web Development, University of Tasmania*