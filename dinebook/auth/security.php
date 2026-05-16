<?php
// auth/security.php — Centralized security helpers to prevent injection attacks,
// XSS, CSRF, and other common web vulnerabilities.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ============================================================
   1. CSRF PROTECTION
   Prevents Cross-Site Request Forgery by requiring a token
   tied to the user's session on every state-changing request.
   ============================================================ */

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    $t = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $t . '">';
}

function csrf_verify(): void {
    $sent = $_POST['csrf_token'] ?? '';
    $real = $_SESSION['csrf_token'] ?? '';
    if (!is_string($sent) || !is_string($real) || $real === '' || !hash_equals($real, $sent)) {
        http_response_code(403);
        die('Security error: invalid CSRF token. Please refresh the page and try again.');
    }
}

/* ============================================================
   2. INPUT SANITIZATION
   All user-supplied values must be forced to safe scalar types
   before reaching the database or output layers.
   ============================================================ */

/**
 * Forces a value to be a trimmed string. If the client sends an
 * array or object (a common NoSQL injection trick, e.g. posting
 * username[$ne]=null), the value is rejected and replaced with ''.
 */
function clean_string($value, int $maxLength = 255): string {
    if (!is_scalar($value)) return '';
    $value = trim((string)$value);
    // Strip null bytes (used in some injection attacks)
    $value = str_replace("\0", '', $value);
    if (mb_strlen($value) > $maxLength) {
        $value = mb_substr($value, 0, $maxLength);
    }
    return $value;
}

function clean_int($value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int {
    if (!is_scalar($value)) return 0;
    $n = (int)$value;
    if ($n < $min) $n = $min;
    if ($n > $max) $n = $max;
    return $n;
}

function clean_email($value): string {
    $v = clean_string($value, 254);
    return filter_var($v, FILTER_VALIDATE_EMAIL) ? $v : '';
}

/**
 * Escapes output to prevent XSS. Always use when echoing user data.
 */
function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/* ============================================================
   3. NoSQL INJECTION GUARD
   MongoDB operators start with '$'. If a user manages to submit
   a field whose value is an array like ['$ne' => null], it can
   bypass authentication. This guard rejects any non-scalar input.
   ============================================================ */

function assert_scalar_post(array $fields): void {
    foreach ($fields as $f) {
        if (isset($_POST[$f]) && !is_scalar($_POST[$f])) {
            http_response_code(400);
            die('Security error: invalid input format detected.');
        }
    }
}

/* ============================================================
   4. RATE LIMITING (simple, session-based)
   Slows down brute-force login attempts.
   ============================================================ */

function rate_limit(string $bucket, int $maxAttempts = 5, int $windowSeconds = 300): bool {
    $key = 'rl_' . $bucket;
    $now = time();
    if (!isset($_SESSION[$key]) || $_SESSION[$key]['reset'] < $now) {
        $_SESSION[$key] = ['count' => 0, 'reset' => $now + $windowSeconds];
    }
    $_SESSION[$key]['count']++;
    return $_SESSION[$key]['count'] <= $maxAttempts;
}

/* ============================================================
   5. SECURE SESSION HEADERS
   Sets baseline security HTTP headers on every protected page.
   ============================================================ */

function send_security_headers(): void {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self' https://cdn.jsdelivr.net https://ajax.googleapis.com https://code.jquery.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://ajax.googleapis.com https://code.jquery.com; img-src 'self' data:;");
}
