<?php
/**
 * Global helper functions.
 */

/** HTML-escape a string for safe output. */
function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Absolute (root-relative) URL for a route path, e.g. url('about'). */
function url(string $path = ''): string
{
    $base = rtrim(BASE_PATH, '/');
    $path = ltrim($path, '/');
    return ($base === '' ? '' : $base) . '/' . $path;
}

/** URL for a static asset, e.g. asset('css/site.css'). */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** URL for an uploaded file path stored in the DB (e.g. "2026/06/abc.jpg" or full http URL). */
function upload_url(?string $stored): string
{
    $stored = (string)$stored;
    if ($stored === '') {
        return '';
    }
    if (preg_match('#^(https?:)?//#i', $stored) || str_starts_with($stored, 'data:')) {
        return $stored;
    }
    if (str_starts_with($stored, 'assets/')) {
        return url($stored);
    }
    return url('uploads/' . ltrim($stored, '/'));
}

/** Redirect and exit. */
function redirect(string $path, int $code = 302): void
{
    $target = preg_match('#^https?://#i', $path) ? $path : url($path);
    header('Location: ' . $target, true, $code);
    exit;
}

/** Current request path (no query string, no base path), e.g. "companies/registry". */
function request_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $base = rtrim(BASE_PATH, '/');
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
    }
    return trim(urldecode($uri), '/');
}

/** Detect HTTPS (handles proxies used by some hosts). */
function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
        return true;
    }
    return ($_SERVER['SERVER_PORT'] ?? null) == 443;
}

/** Best-effort client IP. */
function client_ip(): string
{
    return substr((string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'), 0, 45);
}

/** Site base URL including scheme + host (for emails, sitemap, og tags). */
function site_url(string $path = ''): string
{
    $scheme = is_https() ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . url($path);
}

/* ---------------------------------------------------------------
 | CSRF protection
 * --------------------------------------------------------------*/

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): bool
{
    $sent = $_POST['_token'] ?? '';
    $ok = is_string($sent) && $sent !== '' && hash_equals($_SESSION['_csrf'] ?? '', $sent);
    return $ok;
}

/** Abort with 419 if the CSRF token is missing/invalid. */
function csrf_require(): void
{
    if (!csrf_verify()) {
        http_response_code(419);
        exit('Your session has expired. Please go back, refresh the page and try again.');
    }
}

/* ---------------------------------------------------------------
 | Flash messages
 * --------------------------------------------------------------*/

function flash_set(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

/** @return array<int,array{type:string,message:string}> */
function flash_pull(): array
{
    $all = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $all;
}

/* ---------------------------------------------------------------
 | Misc utilities
 * --------------------------------------------------------------*/

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($converted !== false) {
            $text = $converted;
        }
    }
    $text = preg_replace('~[^a-z0-9]+~', '-', $text) ?? '';
    return trim($text, '-') ?: 'item';
}

/** Plain-text excerpt from HTML. */
function excerpt(string $html, int $length = 160): string
{
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)) ?? '');
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length - 1), " \t.,;:") . '…';
}

function format_date(?string $dt, string $format = 'j M Y'): string
{
    if (!$dt) {
        return '';
    }
    $ts = strtotime($dt);
    return $ts ? date($format, $ts) : '';
}

function format_bytes(int $bytes): string
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return $bytes . ' B';
}

/** Read a posted string field, trimmed. */
function post_str(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function post_int(string $key, int $default = 0): int
{
    return (int)($_POST[$key] ?? $default);
}

/* ---------------------------------------------------------------
 | View rendering
 * --------------------------------------------------------------*/

/** Render a public view inside the public layout. */
function render(string $view, array $data = []): void
{
    extract($data, EXTR_SKIP);
    $viewFile = APP_PATH . '/views/' . $view . '.php';
    include APP_PATH . '/views/partials/header.php';
    include $viewFile;
    include APP_PATH . '/views/partials/footer.php';
}

/** Render a view file to string (no layout). */
function view_raw(string $view, array $data = []): string
{
    extract($data, EXTR_SKIP);
    ob_start();
    include APP_PATH . '/views/' . $view . '.php';
    return (string)ob_get_clean();
}

/** Render an admin view inside the admin layout. */
function admin_render(string $view, array $data = []): void
{
    $data['_view'] = $view;
    extract($data, EXTR_SKIP);
    include APP_PATH . '/views/admin/layout.php';
}

/* ---------------------------------------------------------------
 | Content helpers
 * --------------------------------------------------------------*/

/** Fetch an active static block's HTML by identifier ('' when absent). */
function block(string $identifier): string
{
    $row = DB::get(
        'SELECT content FROM ' . DB::table('blocks') . ' WHERE identifier = ? AND active = 1',
        [$identifier]
    );
    return $row['content'] ?? '';
}

/** Navigation pages (published + flagged for nav). */
function nav_pages(): array
{
    return DB::all(
        'SELECT slug, title, nav_label FROM ' . DB::table('pages') .
        " WHERE status = 'published' AND show_in_nav = 1 ORDER BY nav_order ASC, title ASC"
    );
}
