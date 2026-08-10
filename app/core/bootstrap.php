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
require APP_PATH . '/core/Env.php';

/**
 * Where configuration comes from:
 *   app/config.php  - shared hosting, written once by install.php
 *   environment     - container platforms (Railway, Render, Fly), whose
 *                     filesystem is rebuilt on every deploy
 */
define('CONFIG_FROM_ENV', !is_file(APP_PATH . '/config.php') && Env::active());

/** Access config sections: config('db'), config('app'). */
function config(?string $section = null): array
{
    static $cfg = null;
    if ($cfg === null) {
        $cfg = CONFIG_FROM_ENV ? Env::config() : require APP_PATH . '/config.php';
    }
    return $section === null ? $cfg : ($cfg[$section] ?? []);
}

/**
 * Deployment is misconfigured (bad credentials, missing variables). Show the
 * operator something actionable instead of a blank 500, and log the detail.
 */
function setup_error(string $message): void
{
    error_log('[setup] ' . $message);
    http_response_code(503);
    header('Retry-After: 60');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">'
        . '<meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Setup required</title></head>'
        . '<body style="font-family:system-ui,sans-serif;max-width:640px;margin:60px auto;padding:0 20px;color:#15243B">'
        . '<h1 style="font-size:22px">Setup required</h1>'
        . '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>'
        . '<p style="color:#5B6B82;font-size:14px">This message is shown to visitors as a temporary '
        . 'service notice. It disappears as soon as the configuration is corrected.</p>'
        . '</body></html>';
    exit;
}

// ---- Not configured yet ----
// Redirect to the installer only when there is an installer to redirect to.
// The container image ships without it, and an unconditional redirect there
// loops forever: the rewrite rules send the missing path back to index.php,
// which redirects to it again.
if (!is_file(APP_PATH . '/config.php') && !CONFIG_FROM_ENV) {
    $self = basename($_SERVER['SCRIPT_NAME'] ?? '');
    if ($self === 'install.php') {
        return; // the installer configures everything itself
    }
    if (is_file(ROOT_PATH . '/install.php')) {
        header('Location: ' . (BASE_PATH === '' ? '' : BASE_PATH) . '/install.php');
        exit;
    }
    setup_error(
        'No database configuration was found. Set MYSQL_URL (or DATABASE_URL), plus '
        . 'ADMIN_EMAIL and ADMIN_PASSWORD, as variables on this service and redeploy. '
        . 'On Railway: add a MySQL database to the project, then open this service\'s '
        . 'Variables tab and use "Add Variable Reference" to share MYSQL_URL with it. '
        . 'Database variables detected in this environment: ' . Env::detected() . '.'
    );
}

// ---- Error handling ----
try {
    $debug = (bool)(config('app')['debug'] ?? false);
} catch (Throwable $e) {
    setup_error($e->getMessage());
}
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
try {
    DB::init(config('db'));
} catch (Throwable $e) {
    error_log('[setup] database connection failed: ' . $e->getMessage());
    setup_error(CONFIG_FROM_ENV
        ? 'The website cannot reach its database. Check that a MySQL database is attached to this '
          . 'service and that its connection variables are shared with the web service.'
        : 'The website cannot reach its database. Check the credentials in app/config.php.');
}

Settings::load();

// First boot on a container platform: build the schema and seed the content.
// On a healthy site the settings table is populated, so this never runs twice.
if (CONFIG_FROM_ENV && !Settings::any()) {
    try {
        Env::provision(config());
        Settings::reload();
    } catch (Throwable $e) {
        error_log('[setup] provisioning failed: ' . $e->getMessage());
        setup_error($e->getMessage());
    }
}

// ---- Headers ----
Security::sendHeaders();

// ---- Timezone ----
date_default_timezone_set(setting('timezone', 'Africa/Lagos'));
