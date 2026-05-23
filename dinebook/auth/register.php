<?php
// auth/register.php — Account creation form
require __DIR__ . '/security.php';
send_security_headers();
$error = $_GET['error'] ?? '';
$prev  = $_GET['u'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card shadow" style="width:460px;">
        <div class="card-body p-4">
            <h2 class="text-center mb-1" style="color:var(--dinebook-red);">DineBook</h2>
            <p class="text-center text-muted mb-4">Create your account</p>

            <?php if ($error === 'exists'): ?>
                <div class="alert alert-warning">That username is already taken.</div>
            <?php elseif ($error === 'weak'): ?>
                <div class="alert alert-warning">Password must be at least 8 characters and include a letter and a number.</div>
            <?php elseif ($error === 'mismatch'): ?>
                <div class="alert alert-warning">Passwords do not match.</div>
            <?php elseif ($error === 'invalid'): ?>
                <div class="alert alert-danger">Username may only contain letters, numbers, dots, dashes, and underscores (3–30 chars).</div>
            <?php elseif ($error === 'email'): ?>
                <div class="alert alert-warning">Please enter a valid email address.</div>
            <?php endif; ?>

            <form method="post" action="register_process.php" autocomplete="off">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                           value="<?= e($prev) ?>" required
                           pattern="[A-Za-z0-9._\-]{3,30}"
                           maxlength="30">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                           required maxlength="254">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Register as</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="guest">Guest (customer — make reservations)</option>
                        <option value="host">Host (restaurant owner/manager)</option>
                        <option value="staff">Staff (waiter / receptionist)</option>
                    </select>
                    <div class="form-text">Admin accounts can only be created by existing admins.</div>
                </div>

                <div class="mb-3" id="phone-group">
                    <label for="phone" class="form-label">Phone number</label>
                    <input type="tel" class="form-control" id="phone" name="phone"
                           maxlength="20" placeholder="+502 1234-5678">
                    <div class="form-text">Required for reservation confirmations.</div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                           required minlength="8" maxlength="128">
                </div>

                <div class="mb-3">
                    <label for="password_confirm" class="form-label">Confirm password</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                           required minlength="8" maxlength="128">
                </div>

                <button type="submit" class="btn btn-primary w-100">Create account</button>
            </form>

            <hr>
            <p class="text-center mb-0">
                Already have an account?
                <a href="/dinebook/auth/login.php">Log in</a>
            </p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
