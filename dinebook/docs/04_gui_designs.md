# DineBook — GUI Designs

## 1. Reservations

### R01 — Create Form
Fields: guest_name (text), email (email), phone (tel), reservation_date (date), arrival_time (select), party_size (select 1-20), zone (radio: terrace/indoors/bar), dietary_restrictions (checkboxes x6), occasion (select), special_requests (textarea), guest_type (radio: new/returning/VIP), confirmation_sent (checkbox)

```
+-----------------------------------------------+
| DineBook Navbar                               |
+-----------------------------------------------+
| Home > Reservations > New Reservation          |
+-----------------------------------------------+
| [Card: New Reservation Form]                   |
|  +-------------------+  +-------------------+  |
|  | Guest Name [____] |  | Email [_________] |  |
|  +-------------------+  +-------------------+  |
|  | Phone [________]  |  | Date [__/__/____] |  |
|  +-------------------+  +-------------------+  |
|  | Time [v Select v] |  | Party [v Select v]|  |
|  +-------------------+  +-------------------+  |
|  | Zone: (o)Terrace (o)Indoors (o)Bar        |  |
|  | Guest: (o)New (o)Returning (o)VIP         |  |
|  | Dietary: []Veg []Vegan []GF []Nut []Lac   |  |
|  | Occasion: [v Select v]                     |  |
|  | Requests: [________________]               |  |
|  | [x] Confirmation Sent                      |  |
|  | [Create Reservation] [Reset]               |  |
+-----------------------------------------------+
```

### R02 — Report (All Reservations)
Columns: Guest Name, Email, Phone, Date, Time, Party, Zone, Dietary, Occasion, Guest Type (badge), Status (badge), Confirmed

### R03/R04 — Search Forms
Single-field search (name) or dropdown field selector + value input.

### R06 — Delete Confirmation
Displays record in read-only table + "Confirm Delete" button.

### R07 — Modify Form
Same layout as Create but all fields pre-filled with current values.

---

## 2. Tables

### T01 — Create Form
Fields: table_number (text), capacity (select 1-20), zone (radio), shape (select: round/rectangular/booth), has_power_outlet (checkbox), is_accessible (checkbox), status (select), notes (textarea), floor (select 1-3), label (text)

### T02 — Report
Columns: Table #, Capacity, Zone, Shape, Floor, Status (badge), Accessible, Power, Label

---

## 3. Guests

### G01 — Create Form
Fields: full_name, email, phone, birth_date, preferred_zone (select), dietary_restrictions (checkboxes), loyalty_tier (radio), contact_preference (radio), marketing_opt_in (checkbox), notes (textarea), visit_count (number), last_visit (date)

### G02 — Report
Columns: Name, Email, Phone, Zone, Tier (badge), Contact Pref, Visits, Last Visit
Includes "Export as JSON" button.

---

## 4. Bookings

### B01 — Create Form
Fields: reservation_id, table_id, guest_id, booking_date, check_in_time, check_out_time, actual_party_size (select), assigned_by, booking_status (radio x6), payment_status (radio x3), special_setup (checkboxes x5), hostess_notes (textarea)

### B02 — Report (with application-level joins)
Columns: Guest Name (from reservation), Table # (from table), Date, Check-in, Check-out, Party, Assigned By, Status (badge), Payment, Setup

---

## 5. No-Shows

### N01 — Create Form
Fields: booking_id, guest_name, email, phone, reservation_date, party_size (select), zone (select), reason_category (select), was_reminded (radio), reminder_channel (checkboxes), impact_assessment (radio), follow_up_action (select), notes (textarea), reported_by

### N02 — Report
Columns: Guest, Email, Date, Party, Zone, Reason, Reminded, Impact (badge), Follow-up, Reported By
