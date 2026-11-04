<?php

$h = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$val = function ($field) use ($old) { return array_key_exists($field, $old) ? $old[$field] : ''; };
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login · Student Q&amp;A</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <main id="main" class="wrap" style="max-width:var(--measure); padding-top:var(--space-8)">
        <div class="page-head">
            <div>
                <h1>Admin login</h1>
                <p class="lead">Sign in to manage users, modules and messages.</p>
            </div>
        </div>

        <?php if ($error !== null): ?>
            <div class="alert alert-error" role="alert"><?= $h($error) ?></div>
        <?php endif; ?>

        <form class="form" method="post" action="admin-login.php">
            <div class="field">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus
                       value="<?= $h($val('username')) ?>">
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn btn-primary">Log in</button>
                <a class="btn btn-ghost" href="index.php">Back to site</a>
            </div>
        </form>
    </main>
</body>
</html>
