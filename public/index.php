<?php
/** Home page. Static content only — no DB access needed. */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';

$title = 'Home';
ob_start();
require TEMPLATES_PATH . '/home.php';
$output = ob_get_clean();
require TEMPLATES_PATH . '/layout.php';
