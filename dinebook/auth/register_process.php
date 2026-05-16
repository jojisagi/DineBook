<?php
// auth/register_process.php — Handles new account creation with full hardening
require __DIR__ . '/security.php';
require __DIR__ . '/../config.php';

// 1) Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// 2) CSRF verification (blocks cross-site forged requests)
csrf_verify();

// 3) NoSQL-injection guard — reject any non-scalar payloads
//    (e.g. username[$ne]=null tricks used to bypass auth)
assert_scalar_post(['username', 'email', 'role', 'password', 'password_confirm']);

// 4) Sanitize every field to a safe scalar
$username = clean_string($_POST['username'] ?? '', 30);
$email    = clean_email($_POST['email'] ?? '');
$role     = clean_string($_POST['role'] ?? '', 10);
$password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
$confirm  = is_string($_POST['password_confirm'] ?? null) ? $_POST['password_confirm'] : '';

// 5) Validation rules
if (!preg_match('/^[A-Za-z0-9._\-]{3,30}$/', $username)) {
    header('Location: register.php?error=invalid');
    exit;
}
if ($email === '') {
    header('Location: register.php?error=email&u=' . urlencode($username));
    exit;
}
if (!in_array($role, ['host', 'staff'], true)) {
    // Prevents privilege escalation — no way to self-assign "admin"
    $role = 'staff';
}
if (strlen($password) < 8 || strlen($password) > 128
    || !preg_match('/[A-Za-z]/', $password)
    || !preg_match('/[0-9]/', $password)) {
    header('Location: register.php?error=weak&u=' . urlencode($username));
    exit;
}
if (!hash_equals($password, $confirm)) {
    header('Location: register.php?error=mismatch&u=' . urlencode($username));
    exit;
}

try {
    // 6) Uniqueness check — pass an explicit STRING, never the raw $_POST
    //    This is the core NoSQL-injection defense on reads.
    $existing = $db->users->findOne(['username' => (string)$username]);
    if ($existing) {
        header('Location: register.php?error=exists');
        exit;
    }

    // 7) Store password only as a bcrypt hash (never plain text)
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $db->users->insertOne([
        'username'      => (string)$username,
        'email'         => (string)$email,
        'password_hash' => (string)$hash,
        'role'          => (string)$role,
        'created_at'    => new MongoDB\BSON\UTCDateTime(),
    ]);

    // 8) Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    $_SESSION['user'] = (string)$username;
    $_SESSION['role'] = (string)$role;

    header('Location: /dinebook/index.php');
    exit;

} catch (Exception $e) {
    // Never leak raw error messages to the client
    error_log('Register error: ' . $e->getMessage());
    header('Location: register.php?error=invalid');
    exit;
}
