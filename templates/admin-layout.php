<?php
/**
 * Admin master layout.
 * Only ever included after includes/require-admin.php has passed, so a
 * logged-in admin is assumed. Receives $title (escaped) + $output (page HTML).
 */
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$current = basename($_SERVER['SCRIPT_NAME']);
$active = function ($file) use ($current) { return $current === $file; };
$adminNav = [
    'admin-users.php'     => 'Users',
    'admin-modules.php'   => 'Modules',
    'admin-messages.php'  => 'Messages',
    'admin-activity.php'  => 'Activity',
];
$adminName = $_SESSION['admin_name'] ?? 'Admin';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title) ?> · Admin · Student Q&amp;A</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="admin-bar">
        <div class="wrap header-inner">
            <a class="brand" href="admin-users.php">Student&nbsp;Q<span class="amp">&amp;</span>A<span class="badge">Admin</span></a>

            <nav class="admin-nav" aria-label="Admin">
                <ul>
                    <?php foreach ($adminNav as $file => $label): ?>
                        <li><a href="<?= $file ?>"
                               <?= $active($file) ? 'aria-current="page"' : '' ?>><?= $e($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <span class="admin-user">
                <span><?= $e($adminName) ?></span>
                <a class="btn btn-sm btn-secondary" href="admin-logout.php">Log out</a>
            </span>
        </div>
    </header>

    <main id="main" class="wrap admin-main">
        <?= $output ?>
    </main>

    <footer class="site-footer">
        <div class="wrap">
            <span>Admin area — Student Q&amp;A</span>
            <a href="index.php">View public site</a>
        </div>
    </footer>
</body>
</html>
