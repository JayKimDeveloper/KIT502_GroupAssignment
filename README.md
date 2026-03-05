UTAS Student Tech Events

Event management and ticket booking web application developed for KIT502 Web Development at the University of Tasmania.

This system allows UTAS students to discover technology-related events such as workshops, tech talks, hackathons, and career networking sessions. Users can register, browse events, and book tickets, while organisers can create and manage events and administrators can manage the entire system.

Project Overview

UTAS Student Tech Events is an event organising platform designed for university students.

The system provides the following capabilities:

Visitors can browse available events.
Students can register as attendees and book tickets.
Organisers can create and manage events.
Administrators can monitor platform activity and manage users and events.

The project is implemented in two stages:

Assignment 1 focuses on frontend design and database planning.
Assignment 2 focuses on backend implementation using Laravel and database integration.

System Roles

The system includes four types of users.

Visitor
Visitors can browse the landing page and event listings but cannot book tickets.

Attendee
Attendees can register an account, browse events, purchase tickets, and view their registered events.

Organiser
Organisers can create, edit, and delete events that they manage.

Admin
The administrator manages the entire system, including users, events, and platform statistics.

Core Features

User registration and login system
Role-based access control
Event creation and management
Ticket booking system with capacity control
Event browsing and filtering
Admin dashboard with system statistics

Advanced features may include:

Notification system
Payment simulation
Waitlist system for full events
Monthly event calendar

Project Structure

The system consists of several key pages.

Landing Page
Displays a brief introduction to the platform and the latest events.

Registration Page
Allows users to create an account as an attendee or organiser.

Login Page
Allows registered users to sign in.

Event Page
Displays all published events with filtering options.

Create Event Page
Allows organisers to create new events.

Event Management Page
Allows organisers to manage their events and attendees to view their registrations.

Admin Dashboard
Allows administrators to view system statistics and manage users and events.

Team Responsibilities
Backend + Database + Documentation

Responsibilities include backend development, database design, and documentation.

Assignment 1
Design database schema and entity relationships
Create ER diagram and database schema documentation
Define database tables including users, events, bookings, waitlists, and notifications
Prepare README documentation and project structure

Assignment 2
Setup Laravel environment and database connection
Implement authentication system (registration, login, logout)
Implement role-based access control
Develop event management features (CRUD operations)
Implement ticket booking system with capacity validation
Develop admin dashboard and system statistics
Prepare final documentation and demo video

Frontend Developer A

Public Pages and Layout

Responsible for layout design and public pages.

Assignment 1
Design site navigation and layout
Implement landing page with event cards and introduction
Create event listing page layout

Assignment 2
Connect landing page to database to display latest events
Implement event listing with filtering features
Connect ticket purchase button to booking process

Frontend Developer B

Authentication Pages

Responsible for user authentication interface.

Assignment 1
Implement registration form with validation
Validate email format, password confirmation, and password policy
Implement login form with validation

Assignment 2
Connect registration form to backend database
Display error messages for invalid login or duplicate email
Implement login state display and logout functionality

Frontend Developer C

Event and Admin Interfaces

Responsible for event management and admin pages.

Assignment 1
Create event creation form with validation
Implement event management page with dummy events
Design admin dashboard layout with statistical cards

Assignment 2
Connect event creation form to database
Implement organiser event management interface
Implement attendee registration management
Connect admin dashboard with database statistics

Database Design

The database will include the following main entities:

Users
Stores user account information and roles.

Events
Stores event details including title, location, schedule, capacity, and price.

Bookings
Stores ticket registrations and booking reference codes.

Waitlist
Stores users waiting for available tickets when events are full.

Notifications
Stores system notifications for users.

An ER diagram and database schema will be submitted as part of Assignment 1.

Technology Stack

Frontend
HTML
CSS
JavaScript / jQuery

Backend
Laravel 10
PHP 8.2

Database
MySQL or SQLite

Development Environment
Usermin

Installation and Deployment (Assignment 2)

The Laravel project will be installed in the group workspace.

Example setup commands:

cd ~/public_html
laravel-install
mv myApp /groupwork/kit502-group-XX/

Database credentials can be found in the file:

kit502-group-XX-mysql-pass.txt

Deliverables

Assignment 1

Frontend website interface
Database schema
ER diagram
README documentation

Assignment 2

Fully functional Laravel web application
Source code in the group directory
Demo video (5–10 minutes)
Final README with admin credentials

Academic Integrity

This project follows the academic integrity policies of the University of Tasmania. All work must be original and properly referenced.

Team Members

Student Name – Backend, Database, Documentation
Student Name – Frontend (Public Pages)
Student Name – Frontend (Authentication)
Student Name – Frontend (Event & Admin UI)
