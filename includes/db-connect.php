<?php
/**
 * db-connect.php — the database connection.
 *
 * require'd once by every page. Also starts the session here so every page
 * gets $_SESSION for free, whether it needs the admin guard (includes/require-admin.php)
 * or just the nav's Admin/Dashboard link swap.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Sorry — the site cannot reach its database right now.');
}
