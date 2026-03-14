# UTAS Student Tech Event

**UTAS Student Tech Events** is a web application developed for **KIT502 Web Development** at the **University of Tasmania**.

The platform allows UTAS students to **discover, organise, and participate in technology-related events** such as workshops, tech talks, hackathons, and career networking sessions.

Users can register as attendees or organisers, browse events, book tickets, and administrators can manage the entire system.

---

# Project Overview

This system is an **Event Management and Ticket Booking Platform** designed specifically for university students.

The platform provides the following capabilities:

- Visitors can browse available events.
- Students can register as attendees and book tickets.
- Organisers can create and manage events.
- Administrators can manage users, events, and platform activity.

The project is implemented in **two stages**:

| Stage | Description |
|------|-------------|
| **Assignment 1** | Frontend design and database planning |
| **Assignment 2** | Full-stack implementation using Laravel |

---

## Color Palette
| Role | Color | HEX | Preview |
|------|-------|-----|---------|
| Primary | Warm Orange | `#E76F51` | ![](https://placehold.co/30x30/E76F51/E76F51.png) |
| Secondary | Burgundy | `#7A1F2B` | ![](https://placehold.co/30x30/7A1F2B/7A1F2B.png) |
| Accent | Golden | `#F4A261` | ![](https://placehold.co/30x30/F4A261/F4A261.png) |
| Background | Cream | `#FFF7ED` | ![](https://placehold.co/30x30/FFF7ED/FFF7ED.png) |
| Surface | Ivory | `#FFE8D6` | ![](https://placehold.co/30x30/FFE8D6/FFE8D6.png) |
| Text | Dark Brown | `#3A1F1F` | ![](https://placehold.co/30x30/3A1F1F/3A1F1F.png) |


# System Roles

The system supports **four types of users**.

### Visitor
- Can browse the landing page and event listings
- Cannot purchase tickets

### Attendee
- Can register an account
- Can browse and book events
- Can view registered events
- Can cancel bookings before the cutoff date

### Organiser
- Can create new events
- Can edit and delete their events
- Can view attendees registered for their events

### Admin
- Can manage all users
- Can manage all events
- Can view system statistics

---

# Core Features

The system includes the following **core functionalities**:

- **User Registration and Login**
- **Role-based Access Control**
- **Event Creation and Management**
- **Ticket Booking System**
- **Event Filtering and Browsing**
- **Admin Dashboard with Statistics**

### Advanced Features (Optional)

- Notification system
- Payment simulation
- Waitlist system when events are full
- Monthly event calendar

---

# System Pages

The web application contains the following main pages:

| Page | Description |
|-----|-------------|
| **Landing Page** | Displays website introduction and latest events |
| **Registration Page** | Allows users to create an account |
| **Login Page** | Allows registered users to log in |
| **Event Page** | Displays available events |
| **Create Event Page** | Allows organisers to create events |
| **Event Management Page** | Allows organisers to manage events |
| **Admin Dashboard** | Displays statistics and system controls |

---

---

# Documentation

| File | Description |
|------|-------------|
| [Shared CSS Guide](docs/shared-css-guide.md) | How to use shared.css — layout, navbar, buttons, footer |

---


# Team Responsibilities

## Backend + Database + Managing (YoungHyun Kim)

Responsible for backend development and database design.

### Assignment 1
- Design **database schema**
- Create **ER Diagram**
- Define system tables
- Design site navigation
- Make Shared CSS

Main tables include:

- `users`
- `events`
- `bookings`
- `waitlist`
- `notifications`

Also responsible for:

- Project documentation
- README preparation
- Backend architecture planning

### Assignment 2

Responsibilities include:

- Laravel project setup
- Database connection
- Authentication system
- Role-based access control
- Event CRUD functionality
- Ticket booking system
- Admin dashboard implementation
- Final documentation and demo preparation

---

## Frontend Developer A  (Gunneet)
### Public Pages & Layout

Responsible for **public-facing pages and UI layout**.

#### Assignment 1
- Implement landing page
- Create event list layout

#### Assignment 2
- Connect landing page with database
- Display latest events dynamically
- Implement event filtering
- Connect ticket purchase button

---

## Frontend Developer B  (Sidd)
### Authentication Pages

Responsible for **user authentication interfaces**.

#### Assignment 1

Implement:

- Registration form
- Login form
- Form validation using **JavaScript / jQuery**

Validation includes:

- Empty field validation
- Email format validation
- Password confirmation
- Password policy validation

#### Assignment 2

Connect forms with backend:

- Database registration
- Login authentication
- Error message display
- Logout functionality

---

## Frontend Developer C  (Pragun)
### Event & Admin Interfaces

Responsible for **event management and admin pages**.

Prototype Site design [link](https://www.figma.com/make/mbiRK198FnSLMnX3Euy4T8/TechEvents-UTAS-Website-Design?t=8Ns6RzjFmbkbWmbQ-1)

#### Assignment 1

Create:

- Event creation form
- Event management page with dummy events
- Admin dashboard layout

#### Assignment 2

Implement:

- Event creation linked to database
- Organiser event management
- Attendee registration management
- Admin dashboard statistics

---

# Database Design

The system database includes the following **core entities**.

| Table | Purpose |
|------|--------|
| **users** | Stores user accounts and roles |
| **events** | Stores event details |
| **bookings** | Stores ticket registrations |
| **waitlist** | Stores waiting users for full events |
| **notifications** | Stores system notifications |

An **ER Diagram** and **Database Schema** will be submitted as part of **Assignment 1**.

---

# Technology Stack

### Frontend
- HTML
- CSS
- JavaScript
- jQuery

### Backend
- Laravel 10
- PHP 8.2

### Database
- MySQL
- SQLite

### Development Environment
- Usermin

---

# Installation (Assignment 2)

The Laravel project will be installed inside the **group workspace**.

Example setup:

```bash
cd ~/public_html
laravel-install
mv myApp /groupwork/kit502-group-XX/
