<?php
declare(strict_types=1);

session_start();
date_default_timezone_set('UTC');

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '5432');
define('DB_NAME', getenv('DB_NAME') ?: 'pi_pz8');
define('DB_USER', getenv('DB_USER') ?: 'postgres');
define('DB_PASS', getenv('DB_PASS') ?: 'postgres');
define('API_BASE', rtrim(getenv('API_BASE') ?: 'http://localhost', '/'));

const STATUSES = [
    'created' => 'Створено',
    'in_transit' => 'В дорозі',
    'delivered' => 'Доставлено',
    'delayed' => 'Затримка',
];

spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/src/' . $class . '.php';
    if (is_file($file)) {
        require $file;
    }
});

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'pgsql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }
    return $pdo;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function post(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = [$type, $message];
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function status_pill(string $status): string
{
    $label = STATUSES[$status] ?? $status;
    return '<span class="pill ' . e($status) . '">' . e($label) . '</span>';
}

function format_time(?string $value): string
{
    return $value ? date('d.m.Y H:i', strtotime($value)) : 'Ще не оновлювався';
}
