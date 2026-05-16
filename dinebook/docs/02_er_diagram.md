# DineBook — Class Diagram (MongoDB Document Model)

Since DineBook uses MongoDB (a NoSQL document database), there is no traditional ER diagram with foreign keys. Instead, we present a **Class Diagram** showing the document structure and application-level references between collections.

## Class Diagram (Mermaid)

```mermaid
classDiagram
    class Reservation {
        +ObjectId _id
        +String guest_name
        +String email
        +String phone
        +String reservation_date
        +String arrival_time
        +Int party_size
        +String zone
        +Array dietary_restrictions
        +String occasion
        +String special_requests
        +String guest_type
        +Boolean confirmation_sent
        +String status
        +UTCDateTime created_at
    }

    class Table {
        +ObjectId _id
        +Int table_number
        +Int capacity
        +String zone
        +String shape
        +Boolean has_power_outlet
        +Boolean is_accessible
        +String status
        +String notes
        +Int floor
        +String label
        +UTCDateTime created_at
    }

    class Guest {
        +ObjectId _id
        +String full_name
        +String email
        +String phone
        +String birth_date
        +String preferred_zone
        +Array dietary_restrictions
        +String loyalty_tier
        +String contact_preference
        +Boolean marketing_opt_in
        +String notes
        +Int visit_count
        +String last_visit
        +UTCDateTime registered_at
    }

    class Booking {
        +ObjectId _id
        +String reservation_id
        +String table_id
        +String guest_id
        +String booking_date
        +String check_in_time
        +String check_out_time
        +Int actual_party_size
        +String assigned_by
        +String booking_status
        +String payment_status
        +Array special_setup
        +String hostess_notes
        +UTCDateTime created_at
    }

    class NoShow {
        +ObjectId _id
        +String booking_id
        +String guest_name
        +String email
        +String phone
        +String reservation_date
        +Int party_size
        +String zone
        +String reason_category
        +String was_reminded
        +Array reminder_channel
        +String impact_assessment
        +String follow_up_action
        +String notes
        +String reported_by
        +UTCDateTime reported_at
    }

    Booking --> Reservation : references reservation_id
    Booking --> Table : references table_id
    Booking --> Guest : references guest_id
    NoShow --> Booking : references booking_id
```

## Collection Relationships

| Collection | Type | References |
|-----------|------|-----------|
| reservations | Catalog | Standalone |
| tables | Catalog | Standalone |
| guests | Catalog | Standalone |
| bookings | Transaction | reservation_id, table_id, guest_id (string refs) |
| noshows | Transaction | booking_id (string ref) |

Application-level joins are performed in PHP using `findOne()` by converting the stored string ID to `MongoDB\BSON\ObjectId`.
