<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Login</title>
    <link rel="stylesheet" href="/styles.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card">
        <h2>Company Login</h2>
        <p class="muted">Sign in to Pharmacy Management System</p>
        <?php if (!empty($error)): ?><div class="tag danger" style="margin-bottom:10px;"><?= e((string)$error) ?></div><?php endif; ?>
        <form method="post" action="/login">
            <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
            <label>Username or Email</label>
            <input name="login" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button>Login</button>
        </form>
        <p class="small" style="margin-top:10px;">Main Admin: Vrushab / Fx993ms@vru</p>
    </div>
</div>
</body>
</html>
