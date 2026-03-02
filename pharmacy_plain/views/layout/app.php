<?php
/** @var string $contentView */
$user = App\Core\Auth::user();
ob_start();
include $contentView;
$pageContent = ob_get_clean();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Management System</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
<?php if ($user): ?>
<div class="topbar">
    <div class="topbar-inner">
        <div class="brand">Pharmacy Management System</div>
        <div class="nav">
            <a href="/dashboard">Dashboard</a>
            <a href="/module/inventory">Inventory</a>
            <a href="/module/pos">POS & Billing</a>
            <a href="/module/referrals">Doctors & Rx</a>
            <a href="/module/patients">Patients & CRM</a>
            <a href="/module/safety">Safety</a>
            <a href="/module/operations">Operations</a>
            <a href="/module/analytics">Analytics</a>
            <?php if ($user['role'] === 'admin'): ?><a href="/module/admin">Admin Panel</a><?php endif; ?>
        </div>
        <form method="post" action="/logout" style="width:auto;">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <button class="secondary" style="width:auto;">Logout (<?= e($user['username']) ?>)</button>
        </form>
    </div>
</div>
<?php endif; ?>
<?= $pageContent ?>
</body>
</html>
