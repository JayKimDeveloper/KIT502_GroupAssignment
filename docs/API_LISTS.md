# KIT502 Group Assignment — API Contract

This document specifies the JSON request/response shapes for every backend endpoint. Use it to write frontend `fetch()` calls without waiting for the backend to be live.

## General conventions

**Authentication**: session-based (cookie). Login once, then the browser carries the session cookie automatically.

**CSRF**: every POST/PUT/PATCH/DELETE needs the CSRF token. Two ways:

1. Blade form — just add `@csrf` inside the `<form>` and submit normally.
2. `fetch()` / AJAX — read the token from `<meta name="csrf-token">` and send it as the `X-CSRF-TOKEN` header.

```html
<!-- in <head> of every page -->
<meta name="csrf-token" content="{{ csrf_token() }}">
```

```js
const csrf = document.querySelector('meta[name=csrf-token]').content;

fetch('/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',       // makes Laravel return JSON
        'X-CSRF-TOKEN': csrf,
    },
    credentials: 'same-origin',             // include session cookie
    body: JSON.stringify({ email, password }),
});
```

**`Accept: application/json` is required** — without it Laravel returns redirect HTML and the fetch breaks.

**Error format** (validation): Laravel's default

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field_name": ["Error message 1", "Error message 2"]
    }
}
```

**Status codes used**

| Code | Meaning |
|---|---|
| 200 | OK |
| 201 | Created (after register / new resource) |
| 401 | Not logged in |
| 403 | Logged in but wrong role |
| 404 | Resource not found |
| 409 | Conflict (e.g. event full) |
| 422 | Validation error |

---

## 1. Authentication

### POST `/register`

Register a new user. Only `attendee` and `organiser` roles are allowed via this endpoint; `admin` is seeded only.

**Request**
```json
{
    "role": "attendee",
    "name": "Anna Kendrick",
    "email": "anna@example.com",
    "password": "Pass@123",
    "password_confirmation": "Pass@123"
}
```

**Password policy**: min 6 chars, must include at least one uppercase, one lowercase, one special character.

**Success — 201 Created**
```json
{
    "message": "Registration successful.",
    "user": {
        "id": 4,
        "name": "Anna Kendrick",
        "email": "anna@example.com",
        "role": "attendee"
    }
}
```

After this the user is automatically logged in (session cookie set).

**Failure — 422 Unprocessable**
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["This email is already registered. Please use a different email."],
        "password": ["Password must include uppercase, lowercase, and a special character."]
    }
}
```

---

### POST `/login`

**Request**
```json
{
    "email": "anna@example.com",
    "password": "Pass@123",
    "remember": true
}
```

`remember` is optional. If `true`, the session persists across browser restarts (handles the "Remember Me" feature from Tutorial 5).

**Success — 200 OK**
```json
{
    "message": "Login successful.",
    "user": {
        "id": 4,
        "name": "Anna Kendrick",
        "email": "anna@example.com",
        "role": "attendee"
    }
}
```

**Failure — 422** (two distinct messages, as required by Tutorial 5)
```json
{
    "errors": { "email": ["Email not registered."] }
}
```
```json
{
    "errors": { "password": ["Incorrect password."] }
}
```

---

### POST `/logout`

Requires being logged in.

**Request**: empty body, just the CSRF token in the header.

**Success — 200 OK**
```json
{
    "message": "Logged out."
}
```

---

### GET `/me`

Used by the navbar to know whether to show "Login / Register" or "Welcome, {name} / Logout". Returns 200 in both cases.

**Logged in**
```json
{
    "authenticated": true,
    "user": {
        "id": 4,
        "name": "Anna Kendrick",
        "email": "anna@example.com",
        "role": "attendee"
    }
}
```

**Not logged in**
```json
{
    "authenticated": false,
    "user": null
}
```

---

## 2. Events

### GET `/api/events`

Public — anyone can call. Returns all `published` events, with filtering.

**Query params (all optional)**:
- `category_id` — integer
- `date` — `YYYY-MM-DD` (events on or after this date)
- `location` — substring match

Example: `/api/events?category_id=1&location=Hobart`

**Success — 200**
```json
{
    "data": [
        {
            "id": 3,
            "title": "UTAS Hackathon 2026",
            "description": "24-hour hackathon — build something that helps the community...",
            "start_datetime": "2026-06-03T18:00:00.000000Z",
            "end_datetime": "2026-06-03T21:00:00.000000Z",
            "location": "IT Building, Sandy Bay",
            "capacity": 50,
            "available_seats": 47,
            "price": "10.00",
            "status": "published",
            "image_url": "/storage/events/hackathon.jpg",
            "category": { "id": 3, "name": "Hackathon" },
            "organiser": { "id": 2, "name": "Test Organiser" }
        }
    ]
}
```

---

### GET `/api/events/recent`

Used by the landing page. Returns the 2 most recently created published events.

**Success — 200**
```json
{
    "data": [
        { "id": 4, "title": "Tech Networking Night", "start_datetime": "...", "price": "0.00", "image_url": null },
        { "id": 3, "title": "UTAS Hackathon 2026",   "start_datetime": "...", "price": "10.00", "image_url": null }
    ]
}
```

Slim payload — only what the landing page card needs.

---

### GET `/api/events/{id}`

Public. Returns full event details including organiser info.

**Success — 200**
```json
{
    "data": {
        "id": 3,
        "title": "UTAS Hackathon 2026",
        "description": "...",
        "start_datetime": "...",
        "end_datetime": "...",
        "location": "IT Building, Sandy Bay",
        "capacity": 50,
        "available_seats": 47,
        "price": "10.00",
        "status": "published",
        "image_url": "/storage/events/hackathon.jpg",
        "category": { "id": 3, "name": "Hackathon" },
        "organiser": { "id": 2, "name": "Test Organiser" }
    }
}
```

**Not found — 404**
```json
{ "message": "Event not found." }
```

---

### GET `/api/events/mine`

Organiser only. Lists events created by the logged-in organiser (Event Management page for organisers).

**Success — 200**: same shape as `/api/events` but only the organiser's events, in reverse-chronological order.

**Not logged in — 401**
```json
{ "message": "Unauthenticated." }
```

**Wrong role — 403**
```json
{ "message": "Forbidden." }
```

---

### POST `/api/events`

Organiser or admin. Creates a new event.

**Request** — `multipart/form-data` (because of image upload). If no image, can use JSON.

```
title:            "Intro to Vue"
description:      "Hands-on workshop..."
start_datetime:   "2026-06-15 18:00:00"
end_datetime:     "2026-06-15 21:00:00"
location:         "Sandy Bay, Room 202"
capacity:         30
price:            0
status:           "draft"        // or "published"
category_id:      1
image:            <file>          (optional, max 2MB, jpg/png)
```

**Success — 201**
```json
{
    "message": "Event created.",
    "data": { /* same shape as GET /api/events/{id} */ }
}
```

**Validation failure — 422**: standard Laravel error format.

---

### PUT `/api/events/{id}`

Organiser (must own event) or admin. Body shape identical to POST but all fields optional (partial update). Image upload uses `multipart/form-data` with method spoofing: send POST + `_method=PUT` field.

**Success — 200**
```json
{
    "message": "Event updated.",
    "data": { /* updated event */ }
}
```

**Not owner — 403**
```json
{ "message": "You do not own this event." }
```

---

### DELETE `/api/events/{id}`

Organiser (must own event) or admin.

**Success — 200**
```json
{ "message": "Event deleted." }
```

---

## 3. Bookings (Ticketing)

### POST `/api/bookings`

Attendee only. Books one ticket for an event.

**Request**
```json
{ "event_id": 3 }
```

**Success — 201**
```json
{
    "message": "Booking confirmed.",
    "data": {
        "id": 12,
        "booking_reference": "BK-A3F9K2QM",
        "event_id": 3,
        "status": "confirmed",
        "payment_status": "free",
        "event": {
            "id": 3,
            "title": "UTAS Hackathon 2026",
            "start_datetime": "..."
        }
    }
}
```

**Event full — 409**
```json
{ "message": "This event has reached its capacity." }
```

**Already booked — 422**
```json
{
    "errors": { "event_id": ["You have already booked this event."] }
}
```

---

### GET `/api/bookings/mine`

Attendee only. Lists the logged-in user's bookings (Event Management page for attendees).

**Success — 200**
```json
{
    "data": [
        {
            "id": 12,
            "booking_reference": "BK-A3F9K2QM",
            "status": "confirmed",
            "payment_status": "free",
            "can_be_cancelled": true,
            "event": {
                "id": 3,
                "title": "UTAS Hackathon 2026",
                "start_datetime": "2026-06-03T18:00:00.000000Z",
                "location": "IT Building, Sandy Bay"
            }
        }
    ]
}
```

`can_be_cancelled` is a computed flag — `true` only if the event is more than 24 hours away and booking is confirmed.

---

### DELETE `/api/bookings/{id}`

Attendee (must own booking). Cancels a booking — only allowed up to 1 day before the event.

**Success — 200**
```json
{ "message": "Booking cancelled." }
```

**Cutoff passed — 422**
```json
{ "message": "Cancellation cutoff has passed (1 day before the event)." }
```

---

### GET `/api/events/{id}/bookings`

Organiser (must own event) or admin. Lists attendees for an event.

**Success — 200**
```json
{
    "data": [
        {
            "id": 12,
            "booking_reference": "BK-A3F9K2QM",
            "status": "confirmed",
            "attendee": {
                "id": 4,
                "name": "Anna Kendrick",
                "email": "anna@example.com"
            }
        }
    ]
}
```

---

## 4. Categories

### GET `/api/categories`

Public. For the category dropdown on event filter and event creation form.

**Success — 200**
```json
{
    "data": [
        { "id": 1, "name": "Workshop" },
        { "id": 2, "name": "Tech Talk" },
        { "id": 3, "name": "Hackathon" },
        { "id": 4, "name": "Networking" },
        { "id": 5, "name": "Conference" }
    ]
}
```

---

## 5. Admin

All admin endpoints require `role: admin`. Return 403 otherwise.

### GET `/api/admin/stats`

For the admin dashboard's 4 key numbers.

**Success — 200**
```json
{
    "data": {
        "total_users": 17,
        "total_events": 4,
        "upcoming_events": 4,
        "total_registrations": 1
    }
}
```

---

### GET `/api/admin/users`

Lists all users.

**Success — 200**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Admin",
            "email": "admin@kit502.test",
            "role": "admin",
            "created_at": "2026-05-13T10:00:00.000000Z"
        }
    ]
}
```

---

### POST `/api/admin/users`

Creates a user. Admin can assign any role including `admin`.

**Request**
```json
{
    "name": "New User",
    "email": "new@example.com",
    "password": "Pass@123",
    "password_confirmation": "Pass@123",
    "role": "organiser"
}
```

**Success — 201**: returns the created user (same shape as the list above).

---

### PUT `/api/admin/users/{id}`

Update name/email/role. Password change is separate (or use `password` field if present).

**Request** (all fields optional)
```json
{
    "name": "Updated Name",
    "email": "updated@example.com"
}
```

**Success — 200**: returns the updated user.

---

### PATCH `/api/admin/users/{id}/role`

Promote/demote a user.

**Request**
```json
{ "role": "organiser" }
```

**Success — 200**
```json
{
    "message": "Role updated.",
    "data": { "id": 4, "name": "Anna", "role": "organiser" }
}
```

---

### DELETE `/api/admin/users/{id}`

**Success — 200**
```json
{ "message": "User deleted." }
```

**Cannot delete self — 422**
```json
{ "message": "You cannot delete your own admin account." }
```

---

### GET `/api/admin/events`

Same shape as `/api/events`, but includes `draft` and `cancelled` events too (admin sees everything).

---

## 6. Notifications (Advanced — 2.5 pts)

### GET `/api/notifications`

Logged-in users only. Returns unread + recent read notifications.

**Success — 200**
```json
{
    "data": [
        {
            "id": 7,
            "type": "booking_confirmed",
            "message": "Your booking BK-A3F9K2QM for 'UTAS Hackathon 2026' is confirmed.",
            "related_id": 12,
            "is_read": false,
            "created_at": "2026-05-13T11:30:00.000000Z"
        }
    ],
    "unread_count": 1
}
```

### PATCH `/api/notifications/{id}/read`

Mark a single notification as read.

**Success — 200**
```json
{ "message": "Marked as read." }
```

---

## Quick reference: which user can call what

| Endpoint | Visitor | Attendee | Organiser | Admin |
|---|---|---|---|---|
| `POST /register` | ✓ | | | |
| `POST /login` | ✓ | | | |
| `POST /logout` | | ✓ | ✓ | ✓ |
| `GET /me` | ✓ | ✓ | ✓ | ✓ |
| `GET /api/events`, `/recent`, `/{id}` | ✓ | ✓ | ✓ | ✓ |
| `GET /api/categories` | ✓ | ✓ | ✓ | ✓ |
| `POST /api/bookings` | | ✓ | | |
| `GET /api/bookings/mine` | | ✓ | | |
| `DELETE /api/bookings/{id}` | | ✓ (own) | | |
| `GET /api/events/mine` | | | ✓ | |
| `POST /api/events` | | | ✓ | ✓ |
| `PUT /api/events/{id}` | | | ✓ (own) | ✓ |
| `DELETE /api/events/{id}` | | | ✓ (own) | ✓ |
| `GET /api/events/{id}/bookings` | | | ✓ (own) | ✓ |
| `GET /api/admin/*` | | | | ✓ |
| `GET /api/notifications` | | ✓ | ✓ | ✓ |
