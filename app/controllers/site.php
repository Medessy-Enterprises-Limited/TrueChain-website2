<?php
/**
 * Public site routes.
 */

$path = request_path();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/* ---------------- Home ---------------- */
if ($path === '') {
    $sliders = DB::all('SELECT * FROM ' . DB::table('sliders') . ' WHERE active = 1 ORDER BY sort_order ASC, id ASC');
    $companies = DB::all('SELECT * FROM ' . DB::table('companies') . ' WHERE active = 1 ORDER BY sort_order ASC, id ASC');
    render('home', [
        'pageTitle'       => setting('meta_title', setting('site_name')),
        'metaDescription' => setting('meta_description'),
        'sliders'         => $sliders,
        'companies'       => $companies,
        'bodyClass'       => 'page-home',
    ]);
    exit;
}

/* ---------------- Companies ---------------- */
if ($path === 'companies') {
    $companies = DB::all('SELECT * FROM ' . DB::table('companies') . ' WHERE active = 1 ORDER BY sort_order ASC, id ASC');
    render('companies', [
        'pageTitle'       => 'Our Companies',
        'metaDescription' => 'The operating companies of the True Chain group: technology, training and corridor infrastructure for African road freight.',
        'companies'       => $companies,
    ]);
    exit;
}

if (preg_match('#^companies/([a-z0-9\-]+)$#', $path, $m)) {
    $company = DB::get('SELECT * FROM ' . DB::table('companies') . ' WHERE slug = ? AND active = 1', [$m[1]]);
    if ($company) {
        $others = DB::all(
            'SELECT * FROM ' . DB::table('companies') . ' WHERE active = 1 AND id != ? ORDER BY sort_order ASC LIMIT 3',
            [$company['id']]
        );
        render('company', [
            'pageTitle'       => $company['name'],
            'metaDescription' => excerpt($company['summary'] ?: $company['tagline'], 160),
            'company'         => $company,
            'others'          => $others,
        ]);
        exit;
    }
}

/* ---------------- Leadership ---------------- */
if ($path === 'leadership') {
    $leaders = DB::all('SELECT * FROM ' . DB::table('leaders') . ' WHERE active = 1 ORDER BY sort_order ASC, id ASC');
    render('leadership', [
        'pageTitle'       => 'Leadership',
        'metaDescription' => 'The leadership of True Chain Infrastructure Company.',
        'leaders'         => $leaders,
    ]);
    exit;
}

/* ---------------- Contact ---------------- */
if ($path === 'contact') {
    $errors = [];
    $sent = false;
    $old = ['name' => '', 'email' => '', 'phone' => '', 'company' => '', 'subject' => '', 'message' => ''];

    if ($method === 'POST') {
        csrf_require();

        // Honeypot + minimum fill time (bots)
        $trap = post_str('website_url_hp');
        $startedAt = (int)post_int('form_ts');
        $tooFast = $startedAt > 0 && (time() - $startedAt) < 3;

        foreach (array_keys($old) as $k) {
            $old[$k] = post_str($k);
        }

        if ($old['name'] === '' || mb_strlen($old['name']) > 190) {
            $errors['name'] = 'Please enter your full name.';
        }
        if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if (mb_strlen($old['message']) < 10) {
            $errors['message'] = 'Please tell us a little more (at least 10 characters).';
        }
        if (mb_strlen($old['message']) > 5000) {
            $errors['message'] = 'Message is too long (5,000 characters max).';
        }

        if (!Security::rateAllow('contact', client_ip(), 5, 3600)) {
            $errors['rate'] = 'Too many messages from this connection. Please try again later.';
        }

        if (!$errors && $trap === '' && !$tooFast) {
            DB::insert('messages', [
                'name'       => $old['name'],
                'email'      => $old['email'],
                'phone'      => $old['phone'],
                'company'    => $old['company'],
                'subject'    => $old['subject'] ?: 'General enquiry',
                'message'    => $old['message'],
                'ip'         => client_ip(),
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Optional email notification
            if (setting('notify_on_contact') === '1' && filter_var(setting('notify_email', setting('contact_email')), FILTER_VALIDATE_EMAIL)) {
                $to = setting('notify_email', setting('contact_email'));
                $sub = '[' . setting('site_name', 'TCIC') . '] New contact message: ' . ($old['subject'] ?: 'General enquiry');
                $body = "New message received via the website contact form.\n\n"
                    . 'Name:    ' . $old['name'] . "\n"
                    . 'Email:   ' . $old['email'] . "\n"
                    . 'Phone:   ' . $old['phone'] . "\n"
                    . 'Company: ' . $old['company'] . "\n"
                    . 'Subject: ' . $old['subject'] . "\n\n"
                    . $old['message'] . "\n\n--\nSent " . date('Y-m-d H:i:s') . ' from IP ' . client_ip();
                $headers = 'From: ' . setting('site_name', 'Website') . ' <no-reply@' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'localhost') . ">\r\n"
                    . 'Reply-To: ' . $old['email'] . "\r\n"
                    . "X-Mailer: PHP/" . PHP_VERSION;
                @mail($to, $sub, $body, $headers);
            }

            $sent = true;
            $old = array_map(fn() => '', $old);
        } elseif ($trap !== '' || $tooFast) {
            // Pretend success to bots
            $sent = true;
        }
    }

    render('contact', [
        'pageTitle'       => 'Contact Us',
        'metaDescription' => 'Get in touch with True Chain Infrastructure Company.',
        'errors'          => $errors,
        'sent'            => $sent,
        'old'             => $old,
    ]);
    exit;
}

/* ---------------- Sitemap ---------------- */
if ($path === 'sitemap.xml') {
    header('Content-Type: application/xml; charset=utf-8');
    $urls = [site_url(''), site_url('companies'), site_url('leadership'), site_url('contact')];
    foreach (DB::all('SELECT slug FROM ' . DB::table('pages') . " WHERE status = 'published'") as $pg) {
        if ($pg['slug'] !== 'home') {
            $urls[] = site_url($pg['slug']);
        }
    }
    foreach (DB::all('SELECT slug FROM ' . DB::table('companies') . ' WHERE active = 1') as $c) {
        $urls[] = site_url('companies/' . $c['slug']);
    }
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach (array_unique($urls) as $u) {
        echo "  <url><loc>" . e($u) . "</loc></url>\n";
    }
    echo '</urlset>';
    exit;
}

/* ---------------- CMS pages (about, privacy-policy, terms-of-use, custom) ---------------- */
if (preg_match('#^[a-z0-9\-/]+$#', $path)) {
    $page = DB::get(
        'SELECT * FROM ' . DB::table('pages') . " WHERE slug = ? AND status = 'published'",
        [$path]
    );
    if ($page) {
        render('page', [
            'pageTitle'       => $page['meta_title'] ?: $page['title'],
            'metaDescription' => $page['meta_description'] ?: excerpt($page['content'] ?? '', 160),
            'page'            => $page,
        ]);
        exit;
    }
}

/* ---------------- 404 ---------------- */
http_response_code(404);
render('404', ['pageTitle' => 'Page not found']);
