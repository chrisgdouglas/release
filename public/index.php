<?php

declare(strict_types=1);

use App\Config;
use App\Csrf;
use App\FormController;
use App\PhpMailerMailer;
use App\QrController;
use App\RateLimiter;
use App\SessionTokenStorage;
use App\SubmitController;
use App\UserDirectory;
use App\Validator;
use App\View;

require dirname(__DIR__) . '/vendor/autoload.php';

// ---- App settings (edit these for your event) -------------------------------
$orgName = 'Winnipeg Folk Festival';
$active = true; // set false to close submissions

// Secrets and PII live one level above the webroot (public/).
$secretsDir = dirname(__DIR__);
$config = Config::load(
    credsFile: $secretsDir . '/creds.php',
    usersCsvPath: $secretsDir . '/users.csv',
    rateLimitDir: $secretsDir . '/rl',
    orgName: $orgName,
    active: $active,
);

// ---- Session (CSRF) ---------------------------------------------------------
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_set_cookie_params([
    'httponly' => true,
    'secure' => $https,
    'samesite' => 'Lax',
]);
session_start();

// ---- Security headers (defense in depth; may also be set at the web server) -
$nonce = bin2hex(random_bytes(16));
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; "
    . "style-src 'self' 'nonce-{$nonce}'; script-src 'self' 'nonce-{$nonce}'; "
    . "base-uri 'none'; form-action 'self'; frame-ancestors 'none'");
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
if ($https) {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

// ---- Services ---------------------------------------------------------------
$users = UserDirectory::fromCsv($config->usersCsvPath);
$view = new View(dirname(__DIR__) . '/templates');
$csrf = new Csrf(new SessionTokenStorage());

// ---- Routing ----------------------------------------------------------------
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if (isset($_GET['qr'])) {
    $scheme = $https ? 'https' : 'http';
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $path = strtok((string) ($_SERVER['REQUEST_URI'] ?? '/'), '?') ?: '/';
    $baseUrl = $scheme . '://' . $host . $path;
    $controller = new QrController($config, $users, $view);
    $response = $controller->handle((string) $_GET['qr'], $baseUrl, $nonce);
} elseif ($method === 'POST') {
    $controller = new SubmitController(
        $users,
        new Validator(),
        $csrf,
        new RateLimiter($config->rateLimitDir),
        new PhpMailerMailer($config),
    );
    $clientIp = (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    $response = $controller->handle(
        $_POST,
        is_string($csrfHeader) ? $csrfHeader : null,
        $clientIp,
        time(),
    );
} else {
    $controller = new FormController($config, $users, $csrf, $view);
    $username = isset($_GET['u']) ? (string) $_GET['u'] : null;
    $response = $controller->handle($username, $nonce);
}

$response->send();
