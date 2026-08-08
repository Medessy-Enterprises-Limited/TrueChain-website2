<?php
/**
 * Application bootstrap: paths, config, errors, session, DB, settings.
 */

define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');

// Base path for URLs (supports installs in a subfolder)
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
define('BASE_PATH', $scriptDir === '/' ? '' : rtrim($scriptDir, '/'));

require APP_PATH . '/core/helpers.php';
require APP_PATH . '/core/icons.php';
require APP_PATH . '/core/Database.php';
require APP_PATH . '/core/Settings.php';
require APP_PATH . '/core/Security.php';
require APP_PATH . '/core/Auth.php';

/** Access config sections: config('db'), config('app'). */
function config(?string $section = null): array
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = require APP_PATH . '/config.php';
    }
    return $section === null ? $cfg : ($cfg[$section] ?? []);
}

// ---- Not installed yet? Send visitors to the installer. ----
if (!is_file(APP_PATH . '/config.php')) {
    $self = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($self !== 'install.php') {
        header('Location: ' . (BASE_PATH === '' ? '' : BASE_PATH) . '/install.php');
        exit;
    }
    return; // installer handles everything itself
}

// ---- Error handling ----
$debug = (bool)(config('app')['debug'] ?? false);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
$logDir = APP_PATH . '/storage';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
ini_set('error_log', $logDir . '/error.log');
error_reporting(E_ALL);

set_exception_handler(function (Throwable $e) use ($debug) {
    error_log('[uncaught] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if ($debug) {
        echo '<pre>' . e($e->getMessage() . "\n" . $e->getTraceAsString()) . '</pre>';
    } else {
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Server error</title></head>'
            . '<body style="font-family:sans-serif;text-align:center;padding:60px">'
            . '<h1>Something went wrong</h1><p>Please try again shortly.</p></body></html>';
    }
    exit;
});

// ---- Session ----
$secure = is_https();
session_name(config('app')['session_name'] ?? 'TCICSESS');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => (BASE_PATH === '' ? '/' : BASE_PATH . '/'),
    'secure'   => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();

// ---- Database + settings ----
DB::init(config('db'));
Settings::load();

// ---- Headers ----
Security::sendHeaders();

// ---- Timezone ----
date_default_timezone_set(setting('timezone', 'Africa/Lagos'));
