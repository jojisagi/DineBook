# DineBook

Restaurant Table Reservation & Management System

**SDG Alignment:** SDG 9 (Industry, Innovation & Infrastructure) | SDG 12 (Responsible Consumption & Production)

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Architecture](#architecture)
4. [Installation & Setup](#installation--setup)
5. [Database Design (MongoDB)](#database-design-mongodb)
6. [Authentication & Security](#authentication--security)
7. [User Roles & Permissions](#user-roles--permissions)
8. [Module Descriptions](#module-descriptions)
9. [API Endpoints](#api-endpoints)
10. [Guest Portal & Floor Map](#guest-portal--floor-map)
11. [jQuery Interactions](#jquery-interactions)
12. [File Structure](#file-structure)
13. [CRUD Operations Summary](#crud-operations-summary)

---

## Project Overview

DineBook is a full-stack web application for managing restaurant table reservations, guest profiles, bookings, and no-show tracking. It provides two distinct interfaces:

- **Staff Dashboard** (admin/host/staff): Full CRUD management of reservations, tables, guests, bookings, and no-show reports.
- **Guest Portal** (customer): Interactive SVG floor map for browsing available tables by zone, selecting 30-minute time slots, choosing reservation duration, and managing personal reservations.

The system uses a role-based access control model where guests can request reservations (pending approval), and staff can approve, reject, or cancel bookings through the interactive floor map.

---

## Technology Stack

| Layer        | Technology                                      |
|--------------|--------------------------------------------------|
| Backend      | PHP 8.x                                         |
| Database     | MongoDB (via `mongodb/mongodb` PHP driver)       |
| Frontend     | HTML5, CSS3, Bootstrap 5.3.2                     |
| JavaScript   | jQuery 3.7.1 (10 distinct uses), Vanilla JS (SVG)|
| Server       | PHP built-in development server                  |
| Package Mgr  | Composer (autoload via `vendor/autoload.php`)     |

---

## Architecture

DineBook follows a **server-side rendered PHP** architecture with AJAX-powered interactions for the guest portal. Each entity (reservations, tables, guests, bookings, no-shows) has its own directory with dedicated CRUD files.

### Design Pattern

- **Multi-page application (MPA):** Each page is a standalone PHP file that includes shared config and auth guards.
- **Application-level joins:** MongoDB stores references as string IDs. Joins between collections (e.g., booking -> reservation -> guest) are resolved in PHP at query time.
- **Dual-collection writes:** Guest reservations write to both `reservations` (staff reports) and `bookings` (table assignment & availability tracking) collections simultaneously.

### Request Flow

```
Browser -> PHP Built-in Server -> PHP File
                                    |-> auth/guard.php (session check)
                                    |-> config.php (MongoDB connection)
                                    |-> Business logic + HTML rendering
```

For AJAX endpoints (guest portal):
```
Browser JS (jQuery) -> /api/*.php or /guest/*_process.php -> JSON response
```

---

## Installation & Setup

### Prerequisites

- PHP 8.x with `mongodb` extension
- MongoDB server running on `localhost:27017`
- Composer

### Steps

```bash
# 1. Clone the project
cd /path/to/WebFP

# 2. Install PHP dependencies
cd dinebook
composer install

# 3. Seed an admin user (run once)
php auth/seed_user.php

# 4. Start the development server (from WebFP root, NOT dinebook/)
cd ..
php -S localhost:8000

# 5. Open browser
# http://localhost:8000/dinebook/index.php
```

> **Important:** Run the server from the `WebFP/` root directory without `-t dinebook`. All internal paths use the `/dinebook/` prefix.

---

## Database Design (MongoDB)

Database name: `dinebook`

### Collections

#### `users` — Authentication credentials
| Field          | Type     | Description                                |
|----------------|----------|--------------------------------------------|
| `username`     | string   | Unique login name (3-30 chars, alphanumeric)|
| `email`        | string   | Email address                              |
| `phone`        | string   | Phone number (guest accounts only)         |
| `password_hash`| string   | bcrypt hash                                |
| `role`         | string   | `admin`, `host`, `staff`, or `guest`       |
| `created_at`   | UTCDateTime | Account creation timestamp              |

#### `reservations` — Reservation catalog (staff report source)
| Field                 | Type        | Description                          |
|-----------------------|-------------|--------------------------------------|
| `_id`                 | ObjectId    | **Reservation ID** (shown in reports)|
| `guest_name`          | string      | Full name of the guest               |
| `email`               | string      | Guest email                          |
| `phone`               | string      | Guest phone                          |
| `reservation_date`    | string      | Date (YYYY-MM-DD)                    |
| `arrival_time`        | string      | Start time (HH:MM)                   |
| `end_time`            | string      | End time (HH:MM)                     |
| `duration`            | int         | Duration in minutes (30-180)         |
| `party_size`          | int         | Number of guests                     |
| `zone`                | string      | `terrace`, `indoors`, or `bar`       |
| `dietary_restrictions`| array       | e.g., `["Vegetarian", "Nut allergy"]`|
| `occasion`            | string      | `none`, `birthday`, `anniversary`    |
| `guest_type`          | string      | `new`, `returning`, `VIP`            |
| `status`              | string      | `active`, `pending`, `cancelled`     |
| `confirmation_sent`   | boolean     | Whether confirmation was sent        |
| `special_requests`    | string      | Free-text notes                      |
| `guest_id`            | string      | Reference to `guests._id`           |
| `guest_user`          | string      | Username (for guest-portal bookings) |
| `booked_by`           | string      | `guest` or staff name                |
| `created_at`          | UTCDateTime | Record creation timestamp            |

#### `tables` — Physical table inventory
| Field          | Type   | Description                         |
|----------------|--------|-------------------------------------|
| `_id`          | ObjectId | Table document ID                 |
| `table_number` | int    | Display number (e.g., 1, 2, 3)     |
| `label`        | string | Label (e.g., "Table1")              |
| `capacity`     | int    | Maximum seats                       |
| `zone`         | string | `terrace`, `indoors`, or `bar`      |
| `shape`        | string | `rectangular`, `round`, `square`    |
| `floor`        | int    | Floor number                        |
| `status`       | string | `available`, `occupied`, `maintenance`|
| `has_power`    | boolean| Power outlet available              |
| `is_accessible`| boolean| Wheelchair accessible               |
| `notes`        | string | Additional notes                    |

#### `guests` — Guest CRM profiles
| Field               | Type        | Description                    |
|---------------------|-------------|--------------------------------|
| `_id`               | ObjectId    | Guest profile ID               |
| `full_name`         | string      | Guest full name                |
| `email`             | string      | Email (unique per guest)       |
| `phone`             | string      | Phone number                   |
| `loyalty_tier`      | string      | `standard`, `silver`, `gold`, `VIP`|
| `contact_preference`| string      | `email`, `SMS`, `whatsapp`, `call`|
| `dietary`           | array       | Dietary restrictions           |
| `notes`             | string      | CRM notes                     |
| `visit_count`       | int         | Total visits                   |
| `last_visit`        | string      | Date of last visit             |
| `registered_at`     | UTCDateTime | Profile creation timestamp     |

#### `bookings` — Reservation + table assignments (transaction records)
| Field              | Type        | Description                              |
|--------------------|-------------|------------------------------------------|
| `_id`              | ObjectId    | **Booking ID** (used in no-show reports) |
| `reservation_id`   | string      | Reference to `reservations._id`          |
| `table_id`         | string      | Reference to `tables._id`               |
| `guest_id`         | string      | Reference to `guests._id`               |
| `booking_date`     | string      | Date (YYYY-MM-DD)                        |
| `time_slot`        | string      | 30-min slot this row occupies (HH:MM)    |
| `check_in_time`    | string      | Reservation start time                   |
| `check_out_time`   | string      | Reservation end time                     |
| `duration`         | int         | Total duration in minutes                |
| `actual_party_size`| int         | Party size                               |
| `assigned_by`      | string      | Staff name or `"Online (guest)"`         |
| `booking_status`   | string      | `pending`, `confirmed`, `seated`, `completed`, `no-show`, `cancelled`|
| `status`           | string      | Mirrors `booking_status` for compatibility|
| `payment_status`   | string      | `unpaid`, `paid`, `comp`                 |
| `special_setup`    | array       | e.g., `["candles", "flowers"]`           |
| `hostess_notes`    | string      | Staff notes                              |
| `guest_user`       | string      | Username (guest-portal bookings)         |
| `guest_email`      | string      | Guest email                              |
| `booked_by`        | string      | `guest` or staff name                    |
| `created_at`       | UTCDateTime | Record creation timestamp                |

> **Multi-slot bookings:** A 2-hour reservation creates 4 booking rows (one per 30-min slot), all sharing the same `reservation_id`. Reports deduplicate by `reservation_id`.

#### `noshows` — No-show tracking reports
| Field              | Type   | Description                              |
|--------------------|--------|------------------------------------------|
| `booking_id`       | string | Reference to `bookings._id`              |
| `guest_name`       | string | Guest name                               |
| `email`            | string | Guest email                              |
| `phone`            | string | Guest phone                              |
| `reservation_date` | string | Original reservation date                |
| `party_size`       | int    | Party size                               |
| `zone`             | string | Zone of the reserved table               |
| `reason_category`  | string | `forgot`, `emergency`, `weather`, `no_reason`, `other`|
| `was_reminded`     | string | `yes`, `no`, `unknown`                   |
| `impact_assessment`| string | `low`, `medium`, `high`                  |
| `reminder_channel` | array  | e.g., `["email", "SMS"]`                 |
| `follow_up_action` | string | `none`, `email_sent`, `blacklisted`, `offered_voucher`|
| `reported_by`      | string | Staff member who reported                |
| `notes`            | string | Additional notes                         |

### Collection Relationships

```
users (authentication)
  |
  |-- role: guest --> guests collection (CRM profile auto-created)
  |-- role: staff/host/admin --> staff dashboard access

reservations (standalone catalog)
  |-- guest_id --> guests._id
  |-- _id <-- bookings.reservation_id

tables (standalone catalog)
  |-- _id <-- bookings.table_id

bookings (transaction: links reservation + table + guest)
  |-- reservation_id --> reservations._id
  |-- table_id --> tables._id
  |-- guest_id --> guests._id
  |-- _id <-- noshows.booking_id

noshows (transaction: links to booking)
  |-- booking_id --> bookings._id
```

---

## Authentication & Security

### Session Management
- `auth/guard.php` — Included at the top of every protected page. Starts session, redirects to login if `$_SESSION['user']` is not set.
- `auth/security.php` — Centralized security helpers, auto-starts session.

### Security Features

| Feature                  | Implementation                                      |
|--------------------------|------------------------------------------------------|
| **Password hashing**     | `password_hash()` with `PASSWORD_BCRYPT`             |
| **CSRF protection**      | Token per session, verified on every POST via `csrf_verify()`|
| **NoSQL injection guard**| `assert_scalar_post()` rejects array/object payloads |
| **Input sanitization**   | `clean_string()`, `clean_int()`, `clean_email()` force safe types|
| **Whitelist validation**  | Regex patterns reject unexpected characters          |
| **Explicit string cast** | `(string)$value` on all MongoDB queries              |
| **Session fixation**     | `session_regenerate_id(true)` on login               |
| **Rate limiting**        | Max 5 login attempts per 5 minutes (session-based)   |
| **Security headers**     | CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy|
| **XSS prevention**       | `htmlspecialchars()` / `e()` on all output           |

### Auth Files
| File                       | Purpose                                           |
|----------------------------|---------------------------------------------------|
| `auth/guard.php`           | Session guard — include on protected pages        |
| `auth/security.php`        | Security helper functions (CSRF, sanitization)    |
| `auth/login.php`           | Login form                                        |
| `auth/login_process.php`   | Login handler (validates, sets session, routes by role)|
| `auth/register.php`        | Registration form (guest/host/staff roles + phone)|
| `auth/register_process.php`| Registration handler (creates user + routes by role)|
| `auth/logout.php`          | Clears session, redirects to login                |
| `auth/seed_user.php`       | One-time admin account seeder                     |

---

## User Roles & Permissions

| Role    | Dashboard          | Can Manage CRUD | Floor Map    | Approve/Reject | My Reservations |
|---------|--------------------|-----------------|--------------|-----------------|-----------------| 
| `admin` | Staff (`index.php`)| All entities    | View + info  | Yes             | N/A             |
| `host`  | Staff (`index.php`)| All entities    | View + info  | Yes             | N/A             |
| `staff` | Staff (`index.php`)| All entities    | View + info  | Yes             | N/A             |
| `guest` | Guest portal       | None            | Reserve only | No              | View + cancel   |

### Role-based routing
- **Login:** Guests are redirected to `/guest/dashboard.php`; staff/host/admin to `/index.php`.
- **Guard:** If a guest visits `/index.php`, they are auto-redirected to the guest portal.
- **Floor Map:** Both roles can access `/guest/floormap.php`. Guests see reservation form; staff see booking details + approve/reject buttons on occupied slots.

---

## Module Descriptions

### Staff Dashboard (`index.php`)
- Protected by `auth/guard.php` (redirects guests to guest portal)
- Displays quick stats: Total Reservations, Total Guests, Available Tables, Bookings Today
- 6 entity cards with New/View All/Search buttons
- JSON Reports card with API links
- Navbar with links to all entity reports + Floor Map + Logout

### Entity Modules (5 entities)
Each entity follows the same CRUD pattern with these files:

| File               | Operation | Description                           |
|--------------------|-----------|---------------------------------------|
| `create.html/.php` | Create    | Form to create a new record           |
| `create_process.php`| Create   | Processes POST, inserts into MongoDB  |
| `report.php`       | Read      | Lists all records with filter         |
| `search.php`       | Read      | Search form                           |
| `search_process.php`| Read     | Search by one field (exact match)     |
| `search_v2.php` / `search_process2.php`| Read | Search with two criteria    |
| `search_v3.php` / `search_process3.php`| Read | Search with three criteria  |
| `modify_search.php`| Update    | Find record to modify                 |
| `modify.php`       | Update    | Edit form (pre-filled)                |
| `modify_process.php`| Update   | Processes update                      |
| `delete_search.php`| Delete    | Find record to delete                 |
| `delete.php`       | Delete    | Confirmation page                     |
| `delete_process.php`| Delete   | Processes deletion                    |

#### 1. Reservations (`/reservations/`)
Manages guest reservation records. Fields: guest name, email, phone, date, time, party size, zone, dietary restrictions, occasion, guest type, status, confirmation, special requests.

#### 2. Tables (`/tables/`)
Manages physical table inventory. Fields: table number, label, capacity, floor, zone (terrace/indoors/bar), shape (rectangular/round/square), status, power outlet, accessibility, notes.

#### 3. Guests (`/guests/`)
CRM-style guest profiles. Fields: full name, email, phone, loyalty tier, contact preference, dietary, notes, visit count, last visit.

#### 4. Bookings (`/bookings/`)
Transaction records linking reservations to tables. Fields: reservation ID, table ID, guest ID, booking date, check-in/out time, party size, assigned by, booking status, payment status, special setup, hostess notes. **Auto-generated Booking ID** shown on the creation form for use in no-show reports.

#### 5. No-Shows (`/noshows/`)
Tracks missed reservations. Fields: booking ID (links to bookings), guest name, email, phone, reservation date, party size, zone, reason category, was reminded, impact assessment, reminder channel, follow-up action, reported by, notes.

---

## API Endpoints

| Endpoint                          | Method | Description                                    |
|-----------------------------------|--------|------------------------------------------------|
| `/api/tables.php`                 | GET    | All tables as JSON, sorted by table number     |
| `/api/guests.php`                 | GET    | All guests as JSON                             |
| `/api/reservations.php`           | GET    | All reservations as JSON                       |
| `/api/availability.php`           | GET    | Available tables by date + zone (simple)       |
| `/api/availability_slots.php`     | GET    | Tables with 30-min slot availability + booking details|

### `availability_slots.php` Parameters
| Param  | Required | Example      | Description                      |
|--------|----------|--------------|----------------------------------|
| `date` | Yes      | `2026-05-23` | Date to check (YYYY-MM-DD)       |
| `zone` | Yes      | `indoors`    | Zone filter: `indoors`, `terrace`, `bar`|

**Response:** Array of table objects, each with a `slots` array containing `{time, available, booking?}` for every 30-min block from 10:00 to 21:30. Cancelled bookings are excluded (slots freed). Booking details (guest, party size, status, duration, booking ID) are included for staff use.

---

## Guest Portal & Floor Map

### Guest Portal (`/guest/`)

| File                      | Purpose                                            |
|---------------------------|----------------------------------------------------|
| `dashboard.php`           | Guest homepage with reservation count + action cards|
| `floormap.php`            | Interactive SVG floor map with zone filter          |
| `reserve_process.php`     | AJAX: Creates reservation + booking records         |
| `my_reservations.php`     | View own reservations with cancel option            |
| `cancel_process.php`      | AJAX: Cancels own reservation (frees slots)         |
| `admin_booking_action.php`| AJAX: Staff approve/reject/cancel bookings          |

### Floor Map Features

1. **Zone selector:** Dropdown to filter tables by zone (Indoors, Terrace, Bar). Only tables in the selected zone render on the SVG.

2. **Interactive SVG grid:** Tables rendered as rectangles or circles scaled by capacity. Color-coded:
   - Green = available (has free slots)
   - Red = fully occupied
   - Yellow/Gold = selected
   - Grey = maintenance

3. **30-minute time slots:** Clicking a table shows all slots from 10:00 to 21:30. Each slot is a clickable button:
   - Green = free (click to reserve)
   - Red = occupied (staff can click to see booking details)
   - Yellow = pending approval

4. **Duration selector:** Guest chooses how long they will stay (30 min to 3 hours). Consecutive slots are highlighted in gold. If any needed slot is occupied, the submit button is disabled with a warning message.

5. **Arrival window notice:** Info box tells guests: "Reservations have an estimated arrival window of +/-15 minutes."

6. **Reservation flow (Guest):**
   - Select date + zone -> Click "Load Map"
   - Click a green table -> Slot grid appears
   - Click a free slot -> Choose duration + party size + notes
   - Click "Request Reservation" -> Status = `pending`
   - Staff must approve before it becomes `confirmed`

7. **Staff view on occupied slots:**
   - Booking ID (monospace, copyable)
   - Guest name, email, party size
   - Duration with start-end times
   - Status badge (Pending/Confirmed)
   - Action buttons: Approve / Reject (for pending) or Cancel Booking (for confirmed)

8. **Multi-slot booking:** A 2-hour reservation creates 4 booking rows in MongoDB (one per 30-min slot), all linked by `reservation_id`. Approve/reject/cancel operations update ALL linked slots atomically.

9. **Cancel = free slots:** When a booking is cancelled (by guest or staff), the availability API excludes it, so the slots turn green again on the map.

### Dual-Collection Write Strategy

When a guest makes a reservation through the floor map:

```
reserve_process.php
  |
  |-- 1. Find/create guest in `guests` collection
  |-- 2. Insert into `reservations` (staff report fields)
  |-- 3. Insert N rows into `bookings` (one per 30-min slot)
  |       - Fields match both staff booking form schema AND floor map API
  |       - Includes: reservation_id, table_id, guest_id, check_in_time,
  |         check_out_time, booking_status, payment_status, etc.
```

---

## jQuery Interactions

The file `js/app.js` implements 10 distinct jQuery uses:

| #  | Feature                        | Selector / Event                    | Description                                                  |
|----|--------------------------------|--------------------------------------|--------------------------------------------------------------|
| 1  | Field highlight on focus       | `input, select, textarea` / focus    | Adds `.field-active` class with red border glow              |
| 2  | Delete confirmation            | `#delete-confirm-form` / submit      | `confirm()` dialog before record deletion                    |
| 3  | Character counter              | `textarea` / input                   | Shows "X characters remaining" below each textarea           |
| 4  | Party size warning             | `#party_size` / change               | Shows warning when party > 8                                 |
| 5  | Fade-in animation              | `.container` / ready                 | Page content fades in over 400ms                             |
| 6  | Row hover highlight            | `table tbody tr` / mouseenter        | Adds yellow highlight class on hover                         |
| 7  | Auto-hide alerts               | `.success-msg, .alert-success`       | Success messages fade out after 4 seconds                    |
| 8  | Toggle booking details         | `#toggle-details` / click            | Slides open/close extra booking details section              |
| 9  | Live search filter             | `#table-filter` / keyup             | Client-side text filter on report tables                     |
| 10 | Reset confirmation             | `.btn-reset` / click                 | `confirm()` before clearing form fields                      |

Additionally, `guest/floormap.php` contains inline JavaScript (jQuery + vanilla JS) for:
- AJAX calls to `availability_slots.php`, `reserve_process.php`, `cancel_process.php`, `admin_booking_action.php`
- SVG DOM manipulation for rendering the floor map
- Slot grid rendering with role-aware behavior
- Duration highlighting and validation
- Real-time booking detail panel updates

---

## File Structure

```
WebFP/
  dinebook/
    config.php                    # MongoDB connection (localhost:27017/dinebook)
    index.php                     # Staff dashboard (admin/host/staff)
    css/style.css                 # Custom theme (DineBook red/gold palette)
    js/app.js                     # jQuery interactions (10 uses)
    vendor/                       # Composer dependencies (MongoDB driver)
    
    auth/
      guard.php                   # Session guard (include on protected pages)
      security.php                # CSRF, sanitization, rate limiting, headers
      login.php                   # Login form
      login_process.php           # Login handler (role-based routing)
      register.php                # Registration form (guest/host/staff + phone)
      register_process.php        # Registration handler (role-based routing)
      logout.php                  # Session destroy + redirect
      seed_user.php               # One-time admin seeder
    
    api/
      tables.php                  # GET: All tables JSON
      guests.php                  # GET: All guests JSON
      reservations.php            # GET: All reservations JSON
      availability.php            # GET: Available tables by date+zone (simple)
      availability_slots.php      # GET: Tables with 30-min slot availability
    
    guest/
      dashboard.php               # Guest portal homepage
      floormap.php                # Interactive SVG floor map + reservation form
      reserve_process.php         # AJAX: Create reservation (dual-collection write)
      my_reservations.php         # View + cancel own reservations
      cancel_process.php          # AJAX: Cancel own reservation (multi-slot)
      admin_booking_action.php    # AJAX: Staff approve/reject/cancel bookings
    
    reservations/
      create.html                 # New reservation form
      create_process.php          # Insert reservation
      report.php                  # All reservations list (with Reservation ID column)
      search.php                  # Search (1 criterion)
      search_v2.php               # Search (2 criteria)
      search_v3.php               # Search (3 criteria)
      search_process[1-3].php     # Search handlers
      modify_search.php           # Find to edit
      modify.php                  # Edit form
      modify_process.php          # Update reservation
      delete_search.php           # Find to delete
      delete.php                  # Confirm deletion
      delete_process.php          # Delete reservation
      report_json.php             # JSON report view
    
    tables/                       # Same CRUD pattern as reservations
    guests/                       # Same CRUD pattern as reservations
    bookings/                     # Same CRUD pattern (with auto-generated Booking ID)
    noshows/                      # Same CRUD pattern (references Booking ID)
```

---

## CRUD Operations Summary

| Entity       | Collection     | Create | Read (Report) | Read (Search x3) | Update | Delete |
|--------------|----------------|--------|---------------|-------------------|--------|--------|
| Reservations | `reservations` | Yes    | Yes + ID col  | 1, 2, 3 criteria  | Yes    | Yes    |
| Tables       | `tables`       | Yes    | Yes           | 1, 2, 3 criteria  | Yes    | Yes    |
| Guests       | `guests`       | Yes    | Yes           | 1, 2, 3 criteria  | Yes    | Yes    |
| Bookings     | `bookings`     | Yes + auto-ID | Yes + ID col + dedup | 1, 2, 3 criteria | Yes | Yes |
| No-Shows     | `noshows`      | Yes    | Yes           | 1, 2, 3 criteria  | Yes    | Yes    |
| Guest Portal | `bookings` + `reservations` + `guests` | Yes (AJAX) | Yes (My Reservations) | N/A | Cancel only | N/A |

---

## Running the Application

```bash
# From WebFP/ root directory:
php -S localhost:8000

# Access points:
# Staff login:  http://localhost:8000/dinebook/index.php
# Guest login:  http://localhost:8000/dinebook/auth/login.php
# Register:     http://localhost:8000/dinebook/auth/register.php
# Floor Map:    http://localhost:8000/dinebook/guest/floormap.php
```
