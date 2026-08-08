<?php
/**
 * True Chain Infrastructure Company - front controller.
 */
require __DIR__ . '/app/core/bootstrap.php';

$path = request_path();

// ---------------- Admin area ----------------
if ($path === 'admin' || str_starts_with($path, 'admin/')) {
    require APP_PATH . '/controllers/admin.php';
    exit;
}

// ---------------- Maintenance mode ----------------
if (setting('maintenance_mode') === '1' && !Auth::check()) {
    http_response_code(503);
    header('Retry-After: 3600');
    render('maintenance', ['pageTitle' => 'Maintenance']);
    exit;
}

// ---------------- Public routes ----------------
require APP_PATH . '/controllers/site.php';
