<?php
// auth/login.php
require __DIR__ . '/security.php';
send_security_headers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center" style="min-height:100vh;">
    <div class="card shadow" style="width:400px;">
        <div class="card-body p-4">
            <h2 class="text-center mb-4" style="color:var(--dinebook-red);">DineBook</h2>
            <p class="text-center text-muted">Restaurant Reservation Management</p>

            <?php if (isset($_GET['error'])): ?>
                <?php $err = $_GET['error']; ?>
                <?php if ($err === 'rate'): ?>
                    <div class="alert alert-danger">Too many login attempts. Please wait a few minutes and try again.</div>
                <?php else: ?>
                    <div class="alert alert-danger">Invalid username or password.</div>
                <?php endif; ?>
            <?php endif; ?>

            <form method="post" action="login_process.php" autocomplete="off">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username"
                           required maxlength="30" pattern="[A-Za-z0-9._\-]{3,30}">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password"
                           required maxlength="128">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <hr>
            <p class="text-center mb-0">
                Don't have an account?
                <a href="/dinebook/auth/register.php">Create one as host</a>
            </p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
