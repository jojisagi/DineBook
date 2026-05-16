<?php
// auth/seed_user.php — Run once to create the admin user
require __DIR__ . '/../config.php';

$existing = $db->users->findOne(['username' => 'admin']);
if ($existing) {
    echo "Admin user already exists.";
    exit;
}

$hash = password_hash('admin123', PASSWORD_BCRYPT);
$db->users->insertOne([
    'username'      => 'admin',
    'password_hash' => $hash,
    'role'          => 'admin',
    'created_at'    => new MongoDB\BSON\UTCDateTime()
]);
echo "Admin user created successfully. Username: admin / Password: admin123";
?>
