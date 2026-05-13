# 🎓 UTAS Student Tech Events

> **KIT502 Web Development** | University of Tasmania  
> **Repository:** https://github.com/JayKimDeveloper/KIT502_GroupAssignment

UTAS Student Tech Events is a web application that gives UTAS students one place to find, organise, and join technology-related events, including workshops, tech talks, hackathons, and networking sessions.

The project was first developed as a static frontend and database design project for Assignment 1. It has now been migrated into a Laravel-based project structure for Assignment 2 full-stack implementation.

---

## Project Overview

UTAS Student Tech Events is designed for different types of users:

- **Visitors** — Browse available events
- **Students / Attendees** — Register, log in, and book tickets
- **Organisers** — Create, update, and manage events
- **Administrators** — Oversee users, events, and platform-level information

The system aims to provide a simple event management platform where university-related technology events can be published, browsed, and booked through a role-based web application.

---

## Development Stages

| Stage | Focus | Status |
|-------|-------|--------|
| Assignment 1 | Frontend design and database planning | Completed |
| Assignment 2 | Full-stack implementation using Laravel | In progress |

### Current Assignment 2 Progress

- Static frontend migrated into Laravel project structure
- HTML pages converted into Laravel Blade templates
- CSS, JavaScript, image, and data assets moved into the Laravel `public` directory
- Laravel web routes configured
- Local development server tested with `php artisan serve`
- Backend authentication, event CRUD, booking, and admin logic are planned for the next implementation stage

---

## Technology Stack

### Frontend

- HTML
- CSS
- JavaScript
- jQuery v3.7.1
- Google Fonts: Poppins
- Google Material Icons

### Backend / Full-stack

- Laravel 10
- PHP 8.2
- Blade templates
- SQLite for local development
- MySQL for school server deployment
- Composer
- Git / GitHub

---

## Project Structure

```text
KIT502_GroupAssignment/
├── app/
├── bootstrap/
├── config/
├── database/
├── docs/
├── event_details/
├── public/
│   ├── css/
│   ├── data/
│   ├── images/
│   └── js/
├── resources/
│   └── views/
├── routes/
│   └── web.php
├── storage/
├── tests/
├── README.md
├── composer.json
├── composer.lock
├── package.json
├── artisan
└── vite.config.js
```

---

## Current Routes

The following pages are currently connected through Laravel routes:

| Route | Page |
|-------|------|
| `/` | Landing Page |
| `/login` | Login Page |
| `/register` | Registration Page |
| `/events` | Events Page |
| `/booking` | Booking Page |
| `/create-event` | Create Event Page |
| `/manage-events` | Event Management Page |
| `/admin-dashboard` | Admin Dashboard |

---

## API GUIDE - ASSIGNMENT02, FINAL UPDATE - 13th/May
(docs/API_LISTS.md)

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

### 5. Configure SQLite for Local Development

Create the SQLite database file:

```bash
touch database/database.sqlite
```

Update `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/KIT502_GroupAssignment/database/database.sqlite
```

Example for macOS:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/Users/your-username/Documents/UTAS/KIT502/groupAssignment/KIT502_GroupAssignment/database/database.sqlite
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Start Local Development Server

```bash
php artisan serve
```

Open the project in a browser:

```text
http://127.0.0.1:8000
```

---

## School Server Deployment Plan

The project will be deployed to the university-provided internal server for final testing and submission.

Recommended deployment workflow:

```bash
git pull
composer install
php artisan migrate
php artisan config:clear
php artisan cache:clear
```

For the school server, update `.env` to use MySQL credentials provided by the university.

Example:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=school_database_name
DB_USERNAME=school_database_user
DB_PASSWORD=school_database_password
```

Sensitive files such as `.env`, database files, logs, and local tool metadata should not be committed to Git.

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
| **Visitor** | Can browse the landing page and view events. Cannot book tickets or modify anything. |
| **Attendee** | Can view events, book tickets, check registered events, and cancel bookings before the deadline. |
| **Organiser** | Can create, update, and delete events. Can also view attendee lists for their own events. |
| **Admin** | Highest level of access. Can manage users and organisers, review all events, and view system statistics. |

---

## Features

### Core Features

- User registration and login
- Role-based access control
- Event creation and management
- Ticket booking system
- Event filtering and browsing
- Admin dashboard with statistics

### Advanced Features *(Optional)*

- Notification system
- Payment simulation
- Waitlist system when events are full
- Monthly event calendar

---

## Pages

| Page | Purpose |
|------|---------|
| Landing Page | Displays website information and featured events |
| Registration Page | Allows new users to create an account |
| Login Page | Allows existing users to sign in |
| Events Page | Displays the list of available events |
| Booking Page | Allows attendees to book tickets |
| Create Event Page | Allows organisers to create a new event |
| Event Management Page | Allows organisers/admins to update or delete existing events |
| Admin Dashboard | Displays users, events, and system statistics |

---

## Team Responsibilities

### Team Progress Timeline

<img src="./docs/TeamProgress.png">

The project is divided among four team members.

---

### YoungHyun Kim (`JayKimyh`) — Backend + Database + Management

- Project setup
- README management
- ERD and schema design
- Login page
- Navigation bar
- Signup validation
- Merge management
- Laravel project structure migration
- Login, Signup develop 

---

### Guneet (`guneet0526kaur-lang`) — Public Pages

- Landing page
- Events page
- CSS for landing, events, and variables
- Image assets

---

### Siddharth (Sidd) — Authentication

- Register page
- Login page

---

### Pragun (`pragun11`) — Event & Admin Interfaces

Responsible for event management and administrative interfaces.

- Web page design using Figma
- Event creation form
- Event management page with dummy events
- Admin dashboard layout
- Shared JSON data files

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

### Fixed Value Naming

#### User Roles

Use lowercase values in the database and backend logic.

```text
admin
organiser
attendee
```

#### Event Statuses

```text
draft
published
cancelled
```

#### Booking Statuses

```text
confirmed
cancelled
```

#### Payment Statuses

```text
free
unpaid
paid
```

### Form Input Naming

#### Register Form

```text
role
name
email
password
password_confirmation
```

#### Login Form

```text
email
password
```

#### Create Event Form

```text
title
description
category_id
start_datetime
end_datetime
location
capacity
price
status
image
```

### Notes

- Database values should use lowercase names where possible.
- Form `name` attributes should match the controller validation keys.
- Route names should be used with Laravel’s `route()` helper instead of hardcoded `.html` links.
- Uploaded event images should use `image` as the form input name, but should be stored in the database as `image_path`.



## Team Expectations and Work Quality Standards

- **Communication:** Team communication is mainly handled through WhatsApp.
- **Internal Deadline:** Team members aim to complete assigned work at least three days before the final submission deadline.
- **Work Quality:** Code should be checked before commit and reviewed after commit where possible.
- **Task Distribution:** Each member is responsible for at least one screen and related functionality.
- **Mutual Support:** Team members should ask questions, provide help, and review each other’s work when needed.

---

## Database Design

### ERD Design

<img src="./docs/KIT502_GrupAssERD.drawio.png">

### Schema

> Note:  
> The following schema was originally designed during Assignment 1.  
> It may be refactored during the Laravel migration process using Laravel migrations and Eloquent models.

```sql
/**
 * Author: YoungHyun Kim
 * Version: 0.1
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

-- Event Category Table
CREATE TABLE category_TB (
    category_id   INT          NOT NULL,
    category_name VARCHAR(100) NOT NULL,
    PRIMARY KEY (category_id)
);

-- Event Table
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

-- Booking Table
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

-- Notification Table (Beta v0.1)
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

-- Activity Logs Table
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
- SQLite database files
- Log files
- Local tool metadata such as `.claude/`
- Local editor settings such as `.vscode/`

Before pushing, check sensitive files with:

```bash
git ls-files | grep -E "\.env|sqlite|pass|secret|key|log"
```

Only `.env.example` and normal Laravel configuration files should appear.

---

## Useful Laravel Commands

```bash
php artisan serve
php artisan route:list
php artisan migrate
php artisan config:clear
php artisan cache:clear
```

---

## Future Development Plan

The next stage of development will focus on:

- Connecting registration and login pages to Laravel authentication
- Implementing user roles
- Creating event migrations, models, and controllers
- Connecting event creation and event management pages to the database
- Implementing ticket booking with capacity validation
- Displaying dynamic event data from the database
- Implementing admin dashboard statistics

---

*UTAS Student Tech Events — KIT502 Web Development, University of Tasmania*
