# DineBook — User Manual

## 1. Getting Started

### Login
1. Open your browser and navigate to `http://localhost/dinebook/`
2. You will be redirected to the login page
3. Enter your credentials:
   - **Username**: admin
   - **Password**: admin123
4. Click **Login**
5. You will be redirected to the DineBook Dashboard

[Screenshot: L01 — Login Screen]

### Dashboard
The dashboard shows:
- Quick stats (Total Reservations, Total Guests, Tables Available, Bookings Today)
- Entity cards with quick action buttons (New, View All, Search)
- Navigation bar at the top for all sections

[Screenshot: D01 — Dashboard]

---

## 2. Managing Reservations

### Create a Reservation
1. Click **New** under Reservations on the dashboard, or navigate to Reservations > New Reservation
2. Fill in all required fields: Guest Name, Email, Phone, Date, Time, Party Size, Zone
3. Optionally select dietary restrictions, occasion, guest type
4. Optionally add special requests (max 300 characters — counter shown)
5. Check "Confirmation Sent" if applicable
6. Click **Create Reservation**
7. A green success message confirms creation with the reservation ID

[Screenshot: R01 — Create Reservation]

### View All Reservations
1. Click **View All** under Reservations or navigate to Reservations in the navbar
2. All reservations are displayed in a sortable table
3. Use the **Filter** box at the top to search within results
4. Guest type and status are shown as colored badges

[Screenshot: R02 — Reservations Report]

### Search a Reservation
- **By Name**: Go to Search by Name, enter guest name, click Search
- **By Field**: Go to Search by Field, select a field from the dropdown, enter value, click Search
- **By Link (GET)**: Go to Search by Link, click a predefined name link

### Modify a Reservation
1. Go to Modify Reservation
2. Enter the guest name and click **Search to Modify**
3. The form appears pre-filled with all current values
4. Edit any field you need to change
5. Click **Update Reservation**
6. A success message confirms the update

[Screenshot: R07 — Modify Reservation]

### Delete a Reservation
1. Go to Delete Reservation
2. Enter the guest name and click **Search to Delete**
3. The record is displayed for review
4. Click **Confirm Delete** (a JavaScript confirmation dialog appears)
5. A success message confirms deletion

---

## 3. Managing Tables

Same CRUD operations as Reservations:
- **Create**: Enter table number, capacity, zone, shape, floor, status, accessibility options
- **Report**: View all tables with status badges
- **Search**: By table number or by selectable field
- **Modify**: Pre-filled form with current values (selected options, checked boxes)
- **Delete**: 2-step confirmation

[Screenshot: T01 — Create Table]
[Screenshot: T02 — Tables Report]

---

## 4. Managing Guests

- **Create**: Enter full name, email, phone, birth date, preferences, loyalty tier, visit count
- **Report**: View all guests with loyalty tier badges + **Export as JSON** button
- **Search**: By name, email, phone, zone, tier, or contact preference
- **Modify**: All controls pre-filled including checkboxes for dietary restrictions
- **Delete**: 2-step confirmation

[Screenshot: G01 — Create Guest]
[Screenshot: G02 — Guests Report]

---

## 5. Managing Bookings

- **Create**: Link a reservation, table, and guest by entering their IDs. Set booking date, party size, status, payment status, and special setup items
- **Report**: Shows resolved guest names and table numbers (application-level joins)
- **Search**: By hostess name, booking status, payment status, or date
- **Modify**: Pre-filled form with all radio buttons, checkboxes, and selects correctly set
- **Delete**: 2-step confirmation

[Screenshot: B01 — Create Booking]
[Screenshot: B02 — Bookings Report]

---

## 6. Managing No-Show Reports

- **Create**: Record a no-show with booking reference, guest info, reason, reminder status, impact assessment, and follow-up action
- **Report**: View all no-shows with impact badges (color-coded)
- **Search**: By guest name, reason, impact, or follow-up action
- **Modify**: Pre-filled form with radios, checkboxes, and selects
- **Delete**: 2-step confirmation

[Screenshot: N01 — Create No-Show]
[Screenshot: N02 — No-Show Report]

---

## 7. Logging Out

1. Click **Logout** in the navigation bar (available on every page)
2. Your session is destroyed
3. You are redirected to the login page
