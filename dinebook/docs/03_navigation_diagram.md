# DineBook — Navigation Diagram

## Screen IDs

| ID | Screen | File |
|----|--------|------|
| L01 | Login | auth/login.php |
| D01 | Dashboard | index.php |
| R01 | Reservation Create | reservations/create.html |
| R02 | Reservation Report | reservations/report.php |
| R03 | Reservation Search (Name) | reservations/search.php |
| R04 | Reservation Search (Field) | reservations/search_v2.php |
| R05 | Reservation Search (GET) | reservations/search_v3.php |
| R06 | Reservation Delete | reservations/delete.php |
| R07 | Reservation Modify | reservations/modify.php |
| R08 | Reservation JSON Report | reservations/report_json.php |
| T01–T07 | Table CRUD screens | tables/*.php |
| G01–G07 | Guest CRUD screens | guests/*.php |
| B01–B07 | Booking CRUD screens | bookings/*.php |
| N01–N07 | No-Show CRUD screens | noshows/*.php |

## Navigation Flow (Mermaid)

```mermaid
flowchart TD
    L01[Login] --> D01[Dashboard]
    D01 --> R01[New Reservation]
    D01 --> R02[All Reservations]
    D01 --> R03[Search by Name]
    D01 --> R04[Search by Field]
    D01 --> R06[Delete Reservation]
    D01 --> R07[Modify Reservation]
    D01 --> R08[JSON Report]
    D01 --> T01[New Table]
    D01 --> T02[All Tables]
    D01 --> G01[New Guest]
    D01 --> G02[All Guests]
    D01 --> B01[New Booking]
    D01 --> B02[All Bookings]
    D01 --> N01[New No-Show]
    D01 --> N02[All No-Shows]

    R01 --> R01P[create_process] --> D01
    R06 --> R06S[delete_search] --> R06P[delete_process] --> D01
    R07 --> R07S[modify_search] --> R07P[modify_process] --> D01

    D01 --> LOGOUT[Logout] --> L01
```

Every page includes the Bootstrap navbar allowing direct navigation to any entity's report page, plus a "Back to Menu" link returning to D01.
