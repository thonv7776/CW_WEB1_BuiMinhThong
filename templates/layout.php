<?php
/**
 * Public master layout.
 * Receives $title (escaped) and $output (already-built HTML from the page).
 * Every dynamic value is escaped here or in the template it came from.
 */
$e = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
$current = basename($_SERVER['SCRIPT_NAME']);
$active = function ($file) use ($current) { return $current === $file; };
$nav = [
    'index.php'     => 'Home',
    'questions.php' => 'Questions',
    'contact.php'   => 'Contact',
];
$isAdmin = !empty($_SESSION['admin_id']);
$isUser = !empty($_SESSION['user_id']);
$userName = $_SESSION['username'] ?? '';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $e($title) ?> · Student Q&amp;A</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/style.css">
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="site-header">
        <div class="wrap header-inner">
            <a class="brand" href="index.php">Student&nbsp;Question and Answer</a>

            <nav class="primary-nav" aria-label="Primary" id="site-nav">
                <button class="nav-toggle" type="button"
                        aria-expanded="false" aria-controls="primary-nav-list">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                    Menu
                </button>
                <ul id="primary-nav-list">
                    <?php foreach ($nav as $file => $label): ?>
                        <li><a href="<?= $file ?>"
                               <?= $active($file) ? 'aria-current="page"' : '' ?>><?= $e($label) ?></a></li>
                    <?php endforeach; ?>
                    <li><a class="nav-cta" href="questions-add.php">Ask a question</a></li>
                    <?php if ($isUser): ?>
                        <li><span class="nav-user"><?= $e($userName) ?></span></li>
                        <li><a href="logout.php">Log out</a></li>
                    <?php else: ?>
                        <li><a href="register.php">Register</a></li>
                        <li><a href="login.php">Log in</a></li>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <li><a href="admin-users.php">Admin</a></li>
                    <?php else: ?>
                        <li><a href="admin-login.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main" class="wrap">
        <?= $output ?>
    </main>

    <footer class="site-footer">
        <div class="wrap">
            <span>Student Question and Answer — COMP1841 coursework</span>
            <span>Built with PHP, PDO and MySQL</span>
        </div>
    </footer>

    <script>
        (function () {
            var nav = document.getElementById('site-nav');
            var btn = nav && nav.querySelector('.nav-toggle');
            if (!btn) return;
            btn.addEventListener('click', function () {
                var open = nav.classList.toggle('open');
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            });
        })();
    </script>
</body>
</html>
