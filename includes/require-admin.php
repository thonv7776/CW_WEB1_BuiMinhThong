<?php
/**
 * require-admin.php — the admin guard.
 *
 * require this as the FIRST line (after db-connect.php) of every admin-*.php
 * page. It's a require, not a function call, so it can't be forgotten the
 * way the lecturer's starter kit forgot to guard its admin pages.
 */
if (empty($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}
