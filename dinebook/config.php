<?php
// config.php — MongoDB connection for DineBook
//
// COLLECTION DESIGN (MongoDB document model):
// -------------------------------------------------
// reservations: standalone catalog — stores reservation details,
//               references guest by email (denormalized for speed)
// tables:       standalone catalog — physical table inventory
// guests:       standalone catalog — CRM guest profiles
// bookings:     TRANSACTION — stores reservation_id, table_id, guest_id
//               as string references (application-level joins via findOne)
// noshows:      TRANSACTION — stores booking_id as string reference
// users:        system — authentication credentials (bcrypt hashed)
// -------------------------------------------------

require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->dinebook;

$reservations = $db->reservations;
$tables       = $db->tables;
$guests       = $db->guests;
$bookings     = $db->bookings;
$noshows      = $db->noshows;
?>
