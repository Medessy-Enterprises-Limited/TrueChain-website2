<?php
/**
 * Local development router for PHP's built-in server.
 * Not used on GoDaddy (Apache reads .htaccess instead).
 *
 * To preview the site on your own computer:
 *   php -S localhost:8080 dev-router.php
 * then open http://localhost:8080
 */
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    if (preg_match('#^/app(/|$)#', $uri)) {
        http_response_code(403);
        exit('Forbidden');
    }
    return false; // serve the static file as-is
}
if ($uri === '/install.php') {
    require __DIR__ . '/install.php';
    return true;
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require __DIR__ . '/index.php';
