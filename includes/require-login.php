<?php
/**
 * require-login.php — the regular-user guard.
 *
 * require this as the FIRST line (after db-connect.php) of any page that
 * only a logged-in, verified user may reach (posting/editing/deleting their
 * own question). Mirrors require-admin.php.
 */
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
