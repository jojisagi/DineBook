<?php
// auth/login_process.php — Hardened authentication entry point
require __DIR__ . '/security.php';
require __DIR__ . '/../config.php';

// 1) Only POST allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// 2) CSRF token check — blocks forged cross-site login attempts
csrf_verify();

// 3) Brute-force rate limiting — max 5 attempts per 5 minutes per session
if (!rate_limit('login', 5, 300)) {
    header('Location: login.php?error=rate');
    exit;
}

// 4) NoSQL-injection guard — reject payloads that are not plain scalars.
//    Without this, an attacker could POST username[$ne]=null and bypass auth.
assert_scalar_post(['username', 'password']);

// 5) Sanitize inputs to known-safe scalar types
$username = clean_string($_POST['username'] ?? '', 30);
$password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

// 6) Whitelist format — rejects weird characters before even touching DB
if (!preg_match('/^[A-Za-z0-9._\-]{3,30}$/', $username) || $password === '') {
    header('Location: login.php?error=1');
    exit;
}

try {
    // 7) Query with an explicitly-cast string — never pass $_POST directly
    $user = $db->users->findOne(['username' => (string)$username]);

    if ($user && isset($user['password_hash']) && password_verify($password, (string)$user['password_hash'])) {
        // 8) Session fixation defense — regenerate ID on successful login
        session_regenerate_id(true);
        $_SESSION['user']  = (string)$username;
        $_SESSION['role']  = (string)($user['role'] ?? 'staff');
        $_SESSION['email'] = (string)($user['email'] ?? '');

        // Reset rate-limit counter on success
        unset($_SESSION['rl_login']);

        // Route by role: guests go to guest dashboard, staff/host/admin to main dashboard
        if ($_SESSION['role'] === 'guest') {
            header('Location: /dinebook/guest/dashboard.php');
        } else {
            header('Location: /dinebook/index.php');
        }
        exit;
    }

    header('Location: login.php?error=1');
    exit;

} catch (Exception $e) {
    // Never expose raw exception messages (info-disclosure)
    error_log('Login error: ' . $e->getMessage());
    header('Location: login.php?error=1');
    exit;
}
