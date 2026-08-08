<?php
/**
 * Security utilities: HTTP headers, upload validation, simple rate limiting.
 */
class Security
{
    /** Send baseline security headers (also set in .htaccess; PHP covers non-Apache setups). */
    public static function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header_remove('X-Powered-By');
    }

    /** Allowed upload types: extension => [allowed mime types]. */
    public static function allowedTypes(): array
    {
        return [
            'jpg'  => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png'  => ['image/png'],
            'gif'  => ['image/gif'],
            'webp' => ['image/webp'],
            'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon', 'image/ico'],
            'svg'  => ['image/svg+xml'],
            'pdf'  => ['application/pdf'],
        ];
    }

    /**
     * Validate and store an uploaded file into /uploads/Y/m/.
     * Returns [ok(bool), 'relative/path or error message', mime, size].
     */
    public static function handleUpload(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return [false, 'Upload failed (error code ' . ($file['error'] ?? '-') . ').', '', 0];
        }
        $size = (int)$file['size'];
        if ($size <= 0 || $size > 8 * 1024 * 1024) {
            return [false, 'File must be between 1 byte and 8 MB.', '', 0];
        }

        $original = (string)($file['name'] ?? 'file');
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        $allowed = self::allowedTypes();
        if (!isset($allowed[$ext])) {
            return [false, 'File type .' . $ext . ' is not allowed.', '', 0];
        }

        // Real MIME sniffing
        $mime = '';
        if (function_exists('finfo_open')) {
            $f = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $f ? (string)finfo_file($f, $file['tmp_name']) : '';
        }
        if ($mime === '' || $mime === 'application/octet-stream') {
            // Fall back to image probing for images
            $info = @getimagesize($file['tmp_name']);
            $mime = $info['mime'] ?? $mime;
        }
        if (!in_array($mime, $allowed[$ext], true)) {
            return [false, 'File contents (' . e($mime) . ') do not match the .' . $ext . ' extension.', '', 0];
        }

        // Extra hardening for SVG: strip scripts/foreign objects/event handlers
        if ($ext === 'svg') {
            $svg = (string)file_get_contents($file['tmp_name']);
            $clean = self::sanitizeSvg($svg);
            if ($clean === null) {
                return [false, 'SVG file rejected for safety (scripts or event handlers found).', '', 0];
            }
            file_put_contents($file['tmp_name'], $clean);
            $size = strlen($clean);
        }

        // Images: confirm they parse
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) && @getimagesize($file['tmp_name']) === false) {
            return [false, 'File is not a valid image.', '', 0];
        }

        $sub = date('Y') . '/' . date('m');
        $dir = ROOT_PATH . '/uploads/' . $sub;
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            return [false, 'Could not create the upload directory. Check folder permissions.', '', 0];
        }

        $name = bin2hex(random_bytes(8)) . '-' . substr(slugify(pathinfo($original, PATHINFO_FILENAME)), 0, 40) . '.' . $ext;
        $dest = $dir . '/' . $name;

        $moved = is_uploaded_file($file['tmp_name'])
            ? move_uploaded_file($file['tmp_name'], $dest)
            : rename($file['tmp_name'], $dest); // CLI/test fallback

        if (!$moved) {
            return [false, 'Could not save the uploaded file.', '', 0];
        }
        @chmod($dest, 0644);

        return [true, $sub . '/' . $name, $mime, $size];
    }

    /** Remove scripting vectors from an SVG; null when it cannot be made safe. */
    public static function sanitizeSvg(string $svg): ?string
    {
        if (stripos($svg, '<!ENTITY') !== false || stripos($svg, '<!DOCTYPE') !== false) {
            $svg = preg_replace('/<!DOCTYPE[^>]*>/i', '', $svg) ?? $svg;
            if (stripos($svg, '<!ENTITY') !== false) {
                return null;
            }
        }
        $svg = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $svg) ?? $svg;
        $svg = preg_replace('#<foreignObject\b[^>]*>.*?</foreignObject>#is', '', $svg) ?? $svg;
        $svg = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $svg) ?? $svg;
        $svg = preg_replace('/(xlink:href|href)\s*=\s*("|\')\s*javascript:[^"\']*\2/i', '$1=$2#$2', $svg) ?? $svg;
        if (preg_match('/<script|onload|onerror|javascript:/i', $svg)) {
            return null;
        }
        return $svg;
    }

    /**
     * Simple DB-backed rate limiter.
     * Returns true when the action is allowed for this key within the window.
     */
    public static function rateAllow(string $bucket, string $key, int $max, int $windowSeconds): bool
    {
        $t = DB::table('rate_limits');
        $now = time();
        DB::run("DELETE FROM {$t} WHERE window_start < ?", [$now - $windowSeconds]);
        $row = DB::get("SELECT * FROM {$t} WHERE bucket = ? AND rkey = ?", [$bucket, $key]);
        if (!$row) {
            DB::insert('rate_limits', [
                'bucket' => $bucket, 'rkey' => $key, 'hits' => 1, 'window_start' => $now,
            ]);
            return true;
        }
        if ((int)$row['hits'] >= $max) {
            return false;
        }
        DB::run("UPDATE {$t} SET hits = hits + 1 WHERE id = ?", [$row['id']]);
        return true;
    }
}
