<?php
/**
 * Admin authentication: login throttling, session hardening, idle timeout.
 */
class Auth
{
    private const MAX_ATTEMPTS = 5;     // failed logins before lockout
    private const LOCK_MINUTES = 15;    // lockout duration

    /** Attempt a login. Returns [ok(bool), error(string)]. */
    public static function attempt(string $email, string $password): array
    {
        $email = strtolower(trim($email));
        if ($email === '' || $password === '') {
            return [false, 'Please enter your email and password.'];
        }

        $user = DB::get(
            'SELECT * FROM ' . DB::table('users') . ' WHERE email = ? AND active = 1',
            [$email]
        );

        // Constant-ish time: always run a hash check.
        $hash = $user['password_hash'] ?? '$2y$10$invalidinvalidinvalidinvalidinvaliduuuuuu';
        $valid = password_verify($password, $hash);

        if (!$user) {
            usleep(random_int(150000, 400000));
            return [false, 'Invalid email or password.'];
        }

        // Lockout check
        if (!empty($user['locked_until']) && strtotime($user['locked_until']) > time()) {
            $mins = (int)ceil((strtotime($user['locked_until']) - time()) / 60);
            return [false, 'Account temporarily locked. Try again in about ' . $mins . ' minute(s).'];
        }

        if (!$valid) {
            $fails = (int)$user['failed_attempts'] + 1;
            $data = ['failed_attempts' => $fails];
            if ($fails >= self::MAX_ATTEMPTS) {
                $data['locked_until'] = date('Y-m-d H:i:s', time() + self::LOCK_MINUTES * 60);
                $data['failed_attempts'] = 0;
            }
            DB::update('users', $data, 'id = ?', [$user['id']]);
            usleep(random_int(150000, 400000));
            return [false, 'Invalid email or password.'];
        }

        // Success: reset counters, harden session
        DB::update('users', [
            'failed_attempts' => 0,
            'locked_until'    => null,
            'last_login'      => date('Y-m-d H:i:s'),
        ], 'id = ?', [$user['id']]);

        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int)$user['id'];
        $_SESSION['admin_name'] = $user['name'];
        $_SESSION['admin_fingerprint'] = self::fingerprint();
        $_SESSION['admin_last_seen'] = time();
        unset($_SESSION['_csrf']); // fresh token post-login

        return [true, ''];
    }

    public static function check(): bool
    {
        if (empty($_SESSION['admin_id'])) {
            return false;
        }
        // Fingerprint binding (user agent) to make cookie theft harder
        if (($_SESSION['admin_fingerprint'] ?? '') !== self::fingerprint()) {
            self::logout();
            return false;
        }
        // Idle timeout
        $idle = (int)(config('app')['session_idle'] ?? 3600);
        if ($idle > 0 && (time() - (int)($_SESSION['admin_last_seen'] ?? 0)) > $idle) {
            self::logout();
            return false;
        }
        $_SESSION['admin_last_seen'] = time();
        return true;
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        return DB::get('SELECT * FROM ' . DB::table('users') . ' WHERE id = ?', [$_SESSION['admin_id']]);
    }

    public static function id(): int
    {
        return (int)($_SESSION['admin_id'] ?? 0);
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /** Redirect to login when unauthenticated. */
    public static function requireLogin(): void
    {
        if (!self::check()) {
            redirect('admin?r=login');
        }
    }

    private static function fingerprint(): string
    {
        return hash('sha256', ($_SERVER['HTTP_USER_AGENT'] ?? '') . '|' . (config('app')['key'] ?? ''));
    }
}
