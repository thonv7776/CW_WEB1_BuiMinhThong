<?php
/** GET /logout.php — clear the regular-user session (leaves any admin session intact). */
require dirname(__DIR__) . '/config/config.php';
require INCLUDES_PATH . '/db-connect.php';

unset($_SESSION['user_id'], $_SESSION['username']);
header('Location: questions.php');
exit;
