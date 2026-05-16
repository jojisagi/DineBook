<?php
// modify_search.php — Guests: Show editable form pre-filled
require_once __DIR__ . '/../auth/guard.php';
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['full_name'])) {
    header('Location: modify.php');
    exit;
}
$full_name = $_POST['full_name'];
try {
    $record = $guests->findOne(['full_name' => $full_name]);
} catch (Exception $e) {
    $record = null;
    $dbError = $e->getMessage();
}

$dietary = [];
if ($record && isset($record['dietary_restrictions'])) {
    $dietary = $record['dietary_restrictions'];
    if ($dietary instanceof MongoDB\Model\BSONArray) $dietary = $dietary->getArrayCopy();
}
$dietaryOptions = ['Vegetarian','Vegan','Gluten-free','Nut allergy','Lactose intolerant','Shellfish allergy'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DineBook — Modify Guest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/dinebook/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"><div class="container-fluid"><a class="navbar-brand" href="../index.php">DineBook</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav"><li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li><li class="nav-item"><a class="nav-link" href="../reservations/report.php">Reservations</a></li><li class="nav-item"><a class="nav-link" href="../tables/report.php">Tables</a></li><li class="nav-item"><a class="nav-link active" href="report.php">Guests</a></li><li class="nav-item"><a class="nav-link" href="../bookings/report.php">Bookings</a></li><li class="nav-item"><a class="nav-link" href="../noshows/report.php">No-Shows</a></li><li class="nav-item"><a class="nav-link" href="../auth/logout.php">Logout</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <nav aria-label="breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../index.php">Home</a></li><li class="breadcrumb-item"><a href="report.php">Guests</a></li><li class="breadcrumb-item active">Modify</li></ol></nav>
        <h2 class="page-header">Modify Guest</h2>
        <?php if (isset($dbError)): ?>
            <div class="alert alert-danger">Database error: <?php echo htmlspecialchars($dbError); ?></div>
        <?php elseif ($record): ?>
        <div class="card"><div class="card-body form-section">
            <form method="post" action="modify_process.php">
                <input type="hidden" name="id" value="<?php echo (string)$record['_id']; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($record['full_name']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($record['email']); ?>" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($record['phone']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="birth_date" class="form-label">Birth Date</label>
                        <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($record['birth_date'] ?? ''); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="preferred_zone" class="form-label">Preferred Zone</label>
                        <select class="form-select" id="preferred_zone" name="preferred_zone">
                            <option value="">-- Select --</option>
                            <?php foreach (['terrace'=>'Terrace','indoors'=>'Indoors','bar'=>'Bar'] as $v => $l): ?>
                                <option value="<?php echo $v; ?>" <?php echo (($record['preferred_zone'] ?? '') == $v) ? 'selected' : ''; ?>><?php echo $l; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="visit_count" class="form-label">Visit Count</label>
                        <input type="number" class="form-control" id="visit_count" name="visit_count" min="0" value="<?php echo htmlspecialchars($record['visit_count'] ?? 0); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Dietary Restrictions</label>
                    <div>
                        <?php foreach ($dietaryOptions as $opt): ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="dietary_restrictions[]" value="<?php echo $opt; ?>" <?php echo in_array($opt, (array)$dietary) ? 'checked' : ''; ?>>
                                <label class="form-check-label"><?php echo $opt; ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Loyalty Tier</label>
                        <div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="loyalty_tier" value="standard" <?php echo (($record['loyalty_tier'] ?? 'standard') == 'standard') ? 'checked' : ''; ?> required><label class="form-check-label">Standard</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="loyalty_tier" value="silver" <?php echo (($record['loyalty_tier'] ?? '') == 'silver') ? 'checked' : ''; ?>><label class="form-check-label">Silver</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="loyalty_tier" value="gold" <?php echo (($record['loyalty_tier'] ?? '') == 'gold') ? 'checked' : ''; ?>><label class="form-check-label">Gold</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="loyalty_tier" value="VIP" <?php echo (($record['loyalty_tier'] ?? '') == 'VIP') ? 'checked' : ''; ?>><label class="form-check-label">VIP</label></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Preference</label>
                        <div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="contact_preference" value="email" <?php echo (($record['contact_preference'] ?? '') == 'email') ? 'checked' : ''; ?> required><label class="form-check-label">Email</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="contact_preference" value="SMS" <?php echo (($record['contact_preference'] ?? '') == 'SMS') ? 'checked' : ''; ?>><label class="form-check-label">SMS</label></div>
                            <div class="form-check form-check-inline"><input class="form-check-input" type="radio" name="contact_preference" value="whatsapp" <?php echo (($record['contact_preference'] ?? '') == 'whatsapp') ? 'checked' : ''; ?>><label class="form-check-label">WhatsApp</label></div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="last_visit" class="form-label">Last Visit</label>
                        <input type="date" class="form-control" id="last_visit" name="last_visit" value="<?php echo htmlspecialchars($record['last_visit'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="marketing_opt_in" value="1" id="marketing_opt_in" <?php echo (!empty($record['marketing_opt_in'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="marketing_opt_in">Marketing Opt-In</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="notes" name="notes" maxlength="300"><?php echo htmlspecialchars($record['notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Guest</button>
                <a href="modify.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div></div>
        <?php else: ?>
            <div class="alert alert-warning">No guest found for "<?php echo htmlspecialchars($full_name); ?>".</div>
        <?php endif; ?>
        <a href="../index.php" class="btn btn-link mt-3">&larr; Back to Menu</a>
    </div>
    <div class="footer container"><p>DineBook &copy; 2026</p></div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/dinebook/js/app.js"></script>
</body>
</html>
