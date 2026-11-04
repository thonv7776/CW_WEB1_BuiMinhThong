<?php



define('DB_HOST', 'localhost');
define('DB_NAME', 'cw_web1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');


define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/images');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$folder = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
define('BASE_URL', $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $folder . '/');


define('OTP_TTL_MINUTES', 10);

$envPath = ROOT_PATH . '/.env';
if (is_file($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv($key . '=' . $value);
        }
    }
}

define('SMTP_HOST', $_ENV['SMTP_HOST'] ?? '');
define('SMTP_PORT', (int) ($_ENV['SMTP_PORT'] ?? 587));
define('SMTP_USERNAME', $_ENV['SMTP_USERNAME'] ?? '');
define('SMTP_PASSWORD', $_ENV['SMTP_PASSWORD'] ?? '');
define('SMTP_FROM', $_ENV['SMTP_FROM'] ?? '');
define('SMTP_FROM_NAME', $_ENV['SMTP_FROM_NAME'] ?? 'Student Q&A');
