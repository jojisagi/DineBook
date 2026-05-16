# DineBook — Architecture Document

## 3-Tier Architecture

```
+---------------------------+
|   PRESENTATION LAYER      |
|   HTML / PHP Views        |
|   Bootstrap 5 + jQuery    |
|   AngularJS (create form) |
+---------------------------+
            |
+---------------------------+
|   LOGIC LAYER             |
|   PHP Process Files       |
|   config.php (connection) |
|   auth/guard.php (session)|
|   api/*.php (JSON)        |
+---------------------------+
            |
+---------------------------+
|   DATA LAYER              |
|   MongoDB 6.x             |
|   Database: dinebook      |
|   Collections: 6          |
+---------------------------+
```

## Technology Stack

| Component | Technology | Version |
|-----------|-----------|---------|
| Server-side language | PHP | 8.x |
| Database | MongoDB Community | 6.x+ |
| PHP MongoDB Library | mongodb/mongodb | 2.x |
| CSS Framework | Bootstrap | 5.3.2 |
| JavaScript Library | jQuery | 3.7.1 |
| MV* Framework | AngularJS | 1.8.2 |
| Package Manager | Composer | 2.x |
| Web Server | Apache / PHP built-in | — |

## Folder Structure

```
dinebook/
├── index.php                  ← Dashboard (PHP)
├── config.php                 ← MongoDB connection
├── auth/
│   ├── guard.php              ← Session guard
│   ├── login.php              ← Login form
│   ├── login_process.php      ← Auth logic
│   ├── logout.php             ← Session destroy
│   └── seed_user.php          ← Admin user seeder
├── css/style.css              ← Custom CSS (after Bootstrap)
├── js/app.js                  ← jQuery interactions (10 uses)
├── reservations/ (16 files)   ← Reservation CRUD + JSON report
├── tables/ (15 files)         ← Table CRUD
├── guests/ (15 files)         ← Guest CRUD
├── bookings/ (15 files)       ← Booking CRUD (with joins)
├── noshows/ (15 files)        ← No-Show CRUD
├── api/                       ← JSON API endpoints
│   ├── reservations.php
│   ├── tables.php
│   ├── guests.php
│   └── availability.php
├── docs/ (9 files)            ← Documentation
└── vendor/                    ← Composer dependencies
```

## Data Flow

```
Browser (HTML Form)
   → HTTP POST/GET
      → PHP Process File
         → require config.php (MongoDB connection)
         → require guard.php (session check)
         → Validate input
         → MongoDB operation (insertOne/findOne/updateOne/deleteOne)
         → Build HTML response
      → HTTP Response
   → Browser renders result
```

## JSON API Flow

```
Browser (fetch() / direct URL)
   → HTTP GET
      → api/*.php
         → require config.php
         → MongoDB find()
         → json_encode()
      → JSON Response (Content-Type: application/json)
   → JavaScript parses and renders
```
