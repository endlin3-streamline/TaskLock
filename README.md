# SOFDEV / TaskLock

A PHP + MySQL web application for managing personal productivity workflows, tasks, milestones, focus sessions, notifications, and user/account administration.

This project is split into two main sections:

- `admin/` — admin dashboard and CRUD management interface
- `frontend/` — user-facing portal for login, registration, batches, tasks, milestones, settings, and profile features

---

## 1. Project Overview

The system is designed to help a user organize work into batches and tasks, track milestones and deliverables, monitor focus sessions, and collect points for completed milestones. The application supports both:

- end-user task management
- administrative maintenance of records

In practice, the app behaves like a lightweight productivity platform similar to a personal task tracker with an admin panel.

---

## 2. Main Features

### Frontend features

- User registration and login
- Task dashboard with task grouping by batch
- Batch creation and management
- Task creation, priority assignment, deadline tracking
- Milestone creation and completion flow
- Screenshot upload for milestone proof
- Automatic points award when a milestone is completed
- Focus session tracking and timer-style workflow
- Notifications and settings management
- User profile and account context
- Calendar-related access and view pages

### Admin features

- Admin login page
- CRUD pages for core entities:
  - users
  - batches
  - tasks
  - milestones
  - notifications
  - points
  - settings
  - focus sessions
  - commit times
  - external calendars
- Dashboard with navigation panels and data management pages
- Charts and tables views for summaries and reporting

---

## 3. Technology Stack

- PHP
- MySQL / MariaDB
- HTML / CSS / JavaScript
- Bootstrap-based UI
- XAMPP local server setup

Database connection is configured in:

- `admin/includes/config.php`
- `frontend/includes/config.php`

Both point to the same local database name:

- database: `sofdev`
- host: `localhost`
- user: `root`
- password: empty string

---

## 4. Folder Structure

```text
SOFDEV/
├── admin/
│   ├── bagiankode/
│   ├── css/
│   ├── images/
│   ├── includes/
│   ├── js/
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── charts.html
│   ├── tables.html
│   ├── input*.php
│   ├── edit*.php
│   ├── delete*.php
│   └── ...
│
├── frontend/
│   ├── audio/
│   ├── css/
│   ├── images/
│   ├── includes/
│   ├── uploads/
│   ├── auth1.php
│   ├── calendar.php
│   ├── frontend.php
│   ├── profile.php
│   ├── settings.php
│   ├── save_notification.php
│   ├── logout.php
│   └── ...
│
├── README.md
└── ...
```

---

## 5. Observed Data Model

From the application code, the project uses a set of tables that are central to the workflow. The main ones are:

- `admin` — admin credentials for the control panel
- `users` — application users
- `batches` — grouping of tasks
- `tasks` — user task records
- `milestones` — task sub-goals or checkpoints
- `points` — reward/achievement points
- `notifications` — task/alert messages
- `focus_sessions` — tracked focus work sessions
- `settings` — user preferences such as alarm and notification settings
- `external_calendars` — external date/calendar records
- `commit_times` — time commitment tracking

The code suggests the main relationships are:

- one user has many batches
- one batch has many tasks
- one task has many milestones
- one user can have many notifications, points, and focus sessions

---

## 6. User Workflow

### User-side flow

1. User visits `frontend/auth1.php`
2. They register or log in
3. They create one or more batches
4. They create tasks under each batch
5. They create milestones for each task
6. They start a focus session when working on a task
7. They upload milestone evidence (image) when complete
8. Points are generated for completed work
9. Notifications and settings appear in the user dashboard

### Admin-side flow

1. Admin visits `admin/login.php`
2. Admin logs in using `admin` table credentials
3. Admin manages all records through management pages
4. Admin can edit, delete, and review user/task/milestone-related information

---

## 7. Entry Points

### Frontend access

- Login/register page: `frontend/auth1.php`
- Main user dashboard: `frontend/frontend.php`
- Profile: `frontend/profile.php`
- Settings: `frontend/settings.php`
- Calendar: `frontend/calendar.php`

### Admin access

- Admin login: `admin/login.php`
- Admin dashboard: `admin/dashboard.php`

---

## 8. Installation and Setup

### Requirements

- XAMPP or equivalent local PHP + MySQL environment
- Apache running
- MySQL running

### Steps

1. Place the project in your local web root, for example:

   ```text
   C:\xampp\htdocs\SOFDEV
   ```

2. Start Apache and MySQL in XAMPP
3. Create a MySQL database named `sofdev`
4. Make sure the database tables exist for the app to work
5. Open the app in a browser:

   ```text
   http://localhost/SOFDEV/frontend/auth1.php
   ```

   Admin panel:

   ```text
   http://localhost/SOFDEV/admin/login.php
   ```

### Database note

This project appears to rely on direct SQL queries and table names that are expected in the `sofdev` database. There is no SQL import file included in the repo snapshot, so the database must be created manually or restored from a local dump if available.

---

## 9. Important Configuration Files

- `admin/includes/config.php`
- `frontend/includes/config.php`

These files currently connect with:

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sofdev";
```

If your local MySQL configuration differs, update these values accordingly.

---

## 10. Uploads

The frontend includes an upload workflow for milestone evidence:

- upload folder: `frontend/uploads/`
- files are stored with timestamps and original filenames sanitized
- default placeholder file used in code: `nofile.png`

Make sure the `uploads/` directory is writable by the web server.

---

## 11. Security and Production Notes

This version of the project is a local educational/demo-style application and still contains patterns that are not production-safe, including:

- direct raw SQL queries
- plain-text password handling in parts of the code
- no prepared statements
- no password hashing
- session-based auth without hardened security practices

For production use, it is recommended to:

- switch to prepared statements / parameterized queries
- hash passwords with `password_hash()` and verify with `password_verify()`
- validate and sanitize all user input
- add role-based access control for admin vs user
- add environment-based configuration
- secure file uploads and restrict allowed file types

---

## 12. Known Project Intent

The app is best understood as a student/individual productivity system with a strong emphasis on:

- organization by batch and task
- milestone-based progress
- focus tracking
- reward/point-based motivation
- admin maintenance of domain data

---

## 13. Suggested Next Improvements

- add a proper SQL schema file (`.sql` dump)
- add database migration scripts
- add password hashing and secure authentication
- add validation and error handling
- add responsive UI polishing
- add backup and export features
- add admin audit logs

---

## 14. Summary

SOFDEV / TaskLock is a PHP-based task management and tracking application with a front-end user portal and an admin control panel. It is structured around users, batches, tasks, milestones, focus sessions, notifications, and points, and it is intended for local deployment via XAMPP.

If you want to continue development, the most important files to review first are:

- `frontend/auth1.php`
- `frontend/frontend.php`
- `frontend/settings.php`
- `admin/login.php`
- `admin/dashboard.php`
- `admin/includes/config.php`
- `frontend/includes/config.php`

---

## 15. License

No explicit license file is provided in this project snapshot. If you plan to distribute it publicly, add a license file such as MIT or GPL before publishing.
