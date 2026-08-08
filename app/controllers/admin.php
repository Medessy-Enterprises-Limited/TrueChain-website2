<?php
/**
 * Admin panel dispatcher. Routes via ?r=<route>, all mutations via POST + CSRF.
 */

$route = $_GET['r'] ?? 'dashboard';
$isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';

/* =====================================================================
 | Authentication
 * ===================================================================*/
if ($route === 'login') {
    if (Auth::check()) {
        redirect('admin');
    }
    $error = '';
    if ($isPost) {
        csrf_require();
        if (!Security::rateAllow('login', client_ip(), 10, 900)) {
            $error = 'Too many login attempts from this connection. Please wait 15 minutes.';
        } else {
            [$ok, $error] = Auth::attempt(post_str('email'), post_str('password'));
            if ($ok) {
                redirect('admin');
            }
        }
    }
    include APP_PATH . '/views/admin/login.php';
    exit;
}

if ($route === 'logout') {
    if ($isPost) {
        csrf_require();
    }
    Auth::logout();
    redirect('admin?r=login');
}

Auth::requireLogin();
$adminUser = Auth::user();

/* =====================================================================
 | Dashboard
 * ===================================================================*/
if ($route === 'dashboard') {
    $stats = [
        'pages'     => (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('pages')),
        'companies' => (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('companies') . ' WHERE active = 1'),
        'sliders'   => (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('sliders') . ' WHERE active = 1'),
        'media'     => (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('media')),
        'unread'    => (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('messages') . ' WHERE is_read = 0'),
        'messages'  => (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('messages')),
    ];
    $recentMessages = DB::all('SELECT * FROM ' . DB::table('messages') . ' ORDER BY id DESC LIMIT 6');
    admin_render('dashboard', [
        'title' => 'Dashboard', 'stats' => $stats, 'recentMessages' => $recentMessages,
    ]);
    exit;
}

/* =====================================================================
 | Pages
 * ===================================================================*/
if ($route === 'pages') {
    $pages = DB::all('SELECT * FROM ' . DB::table('pages') . ' ORDER BY nav_order ASC, title ASC');
    admin_render('pages', ['title' => 'Pages', 'pages' => $pages]);
    exit;
}

if ($route === 'page-edit') {
    $id = (int)($_GET['id'] ?? 0);
    $page = $id ? DB::get('SELECT * FROM ' . DB::table('pages') . ' WHERE id = ?', [$id]) : null;
    if ($id && !$page) {
        flash_set('error', 'Page not found.');
        redirect('admin?r=pages');
    }
    admin_render('page-edit', ['title' => $page ? 'Edit page' : 'New page', 'page' => $page]);
    exit;
}

if ($route === 'page-save' && $isPost) {
    csrf_require();
    $id = post_int('id');
    $existing = $id ? DB::get('SELECT * FROM ' . DB::table('pages') . ' WHERE id = ?', [$id]) : null;

    $title = post_str('title');
    $slug = slugify(post_str('slug') ?: $title);
    if ($existing && (int)$existing['is_system'] === 1) {
        $slug = $existing['slug']; // system slugs are locked
    }
    // Reserved slugs (built-in routes)
    $reserved = ['admin', 'contact', 'companies', 'leadership', 'assets', 'uploads', 'install', 'sitemap.xml'];
    if (in_array($slug, $reserved, true)) {
        $slug .= '-page';
    }

    if ($title === '') {
        flash_set('error', 'The page needs a title.');
        redirect('admin?r=page-edit' . ($id ? '&id=' . $id : ''));
    }

    // Unique slug
    $clash = DB::get('SELECT id FROM ' . DB::table('pages') . ' WHERE slug = ? AND id != ?', [$slug, $id]);
    if ($clash) {
        $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
    }

    $data = [
        'slug'             => $slug,
        'title'            => $title,
        'nav_label'        => post_str('nav_label'),
        'content'          => (string)($_POST['content'] ?? ''),
        'meta_title'       => post_str('meta_title'),
        'meta_description' => post_str('meta_description'),
        'status'           => post_str('status') === 'draft' ? 'draft' : 'published',
        'show_in_nav'      => post_int('show_in_nav') ? 1 : 0,
        'nav_order'        => post_int('nav_order'),
        'updated_at'       => date('Y-m-d H:i:s'),
    ];

    if ($existing) {
        DB::update('pages', $data, 'id = ?', [$id]);
        flash_set('success', 'Page updated.');
    } else {
        $data['is_system'] = 0;
        $data['created_at'] = date('Y-m-d H:i:s');
        $id = DB::insert('pages', $data);
        flash_set('success', 'Page created.');
    }
    redirect('admin?r=page-edit&id=' . $id);
}

if ($route === 'page-delete' && $isPost) {
    csrf_require();
    $id = post_int('id');
    $page = DB::get('SELECT * FROM ' . DB::table('pages') . ' WHERE id = ?', [$id]);
    if ($page && (int)$page['is_system'] === 0) {
        DB::delete('pages', 'id = ?', [$id]);
        flash_set('success', 'Page deleted.');
    } else {
        flash_set('error', 'System pages cannot be deleted (you can edit their content instead).');
    }
    redirect('admin?r=pages');
}

/* =====================================================================
 | Sliders
 * ===================================================================*/
if ($route === 'sliders') {
    $sliders = DB::all('SELECT * FROM ' . DB::table('sliders') . ' ORDER BY sort_order ASC, id ASC');
    admin_render('sliders', ['title' => 'Hero Slider', 'sliders' => $sliders]);
    exit;
}

if ($route === 'slider-edit') {
    $id = (int)($_GET['id'] ?? 0);
    $slider = $id ? DB::get('SELECT * FROM ' . DB::table('sliders') . ' WHERE id = ?', [$id]) : null;
    admin_render('slider-edit', ['title' => $slider ? 'Edit slide' : 'New slide', 'slider' => $slider]);
    exit;
}

if ($route === 'slider-save' && $isPost) {
    csrf_require();
    $id = post_int('id');
    $data = [
        'title'      => post_str('title'),
        'subtitle'   => post_str('subtitle'),
        'image'      => post_str('image'),
        'cta_text'   => post_str('cta_text'),
        'cta_url'    => post_str('cta_url'),
        'cta2_text'  => post_str('cta2_text'),
        'cta2_url'   => post_str('cta2_url'),
        'sort_order' => post_int('sort_order'),
        'active'     => post_int('active') ? 1 : 0,
    ];
    if ($data['title'] === '') {
        flash_set('error', 'The slide needs a headline.');
        redirect('admin?r=slider-edit' . ($id ? '&id=' . $id : ''));
    }
    if ($id) {
        DB::update('sliders', $data, 'id = ?', [$id]);
        flash_set('success', 'Slide updated.');
    } else {
        $id = DB::insert('sliders', $data);
        flash_set('success', 'Slide created.');
    }
    redirect('admin?r=sliders');
}

if ($route === 'slider-delete' && $isPost) {
    csrf_require();
    DB::delete('sliders', 'id = ?', [post_int('id')]);
    flash_set('success', 'Slide deleted.');
    redirect('admin?r=sliders');
}

/* =====================================================================
 | Static blocks
 * ===================================================================*/
if ($route === 'blocks') {
    $blocks = DB::all('SELECT * FROM ' . DB::table('blocks') . ' ORDER BY identifier ASC');
    admin_render('blocks', ['title' => 'Static Blocks', 'blocks' => $blocks]);
    exit;
}

if ($route === 'block-edit') {
    $id = (int)($_GET['id'] ?? 0);
    $blockRow = $id ? DB::get('SELECT * FROM ' . DB::table('blocks') . ' WHERE id = ?', [$id]) : null;
    admin_render('block-edit', ['title' => $blockRow ? 'Edit block' : 'New block', 'blockRow' => $blockRow]);
    exit;
}

if ($route === 'block-save' && $isPost) {
    csrf_require();
    $id = post_int('id');
    $identifier = slugify(post_str('identifier'));
    if ($identifier === '' || $identifier === 'item') {
        flash_set('error', 'The block needs an identifier (letters, numbers, dashes).');
        redirect('admin?r=block-edit' . ($id ? '&id=' . $id : ''));
    }
    $clash = DB::get('SELECT id FROM ' . DB::table('blocks') . ' WHERE identifier = ? AND id != ?', [$identifier, $id]);
    if ($clash) {
        flash_set('error', 'Another block already uses that identifier.');
        redirect('admin?r=block-edit' . ($id ? '&id=' . $id : ''));
    }
    $data = [
        'identifier' => $identifier,
        'title'      => post_str('title'),
        'content'    => (string)($_POST['content'] ?? ''),
        'note'       => post_str('note'),
        'active'     => post_int('active') ? 1 : 0,
    ];
    if ($id) {
        DB::update('blocks', $data, 'id = ?', [$id]);
        flash_set('success', 'Block updated.');
    } else {
        $id = DB::insert('blocks', $data);
        flash_set('success', 'Block created.');
    }
    redirect('admin?r=block-edit&id=' . $id);
}

if ($route === 'block-delete' && $isPost) {
    csrf_require();
    DB::delete('blocks', 'id = ?', [post_int('id')]);
    flash_set('success', 'Block deleted.');
    redirect('admin?r=blocks');
}

/* =====================================================================
 | Companies
 * ===================================================================*/
if ($route === 'companies') {
    $companies = DB::all('SELECT * FROM ' . DB::table('companies') . ' ORDER BY sort_order ASC, id ASC');
    admin_render('companies', ['title' => 'Companies', 'companies' => $companies]);
    exit;
}

if ($route === 'company-edit') {
    $id = (int)($_GET['id'] ?? 0);
    $company = $id ? DB::get('SELECT * FROM ' . DB::table('companies') . ' WHERE id = ?', [$id]) : null;
    admin_render('company-edit', ['title' => $company ? 'Edit company' : 'New company', 'company' => $company]);
    exit;
}

if ($route === 'company-save' && $isPost) {
    csrf_require();
    $id = post_int('id');
    $name = post_str('name');
    if ($name === '') {
        flash_set('error', 'The company needs a name.');
        redirect('admin?r=company-edit' . ($id ? '&id=' . $id : ''));
    }
    $slug = slugify(post_str('slug') ?: $name);
    $clash = DB::get('SELECT id FROM ' . DB::table('companies') . ' WHERE slug = ? AND id != ?', [$slug, $id]);
    if ($clash) {
        $slug .= '-' . substr(bin2hex(random_bytes(2)), 0, 4);
    }
    $url = post_str('website_url');
    if ($url !== '' && $url !== '#' && !preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    $data = [
        'slug'        => $slug,
        'name'        => $name,
        'short_name'  => post_str('short_name'),
        'category'    => post_str('category'),
        'tagline'     => post_str('tagline'),
        'summary'     => post_str('summary'),
        'content'     => (string)($_POST['content'] ?? ''),
        'icon'        => post_str('icon') ?: 'group',
        'image'       => post_str('image'),
        'website_url' => $url,
        'site_status' => post_str('site_status') === 'live' ? 'live' : 'coming-soon',
        'sort_order'  => post_int('sort_order'),
        'active'      => post_int('active') ? 1 : 0,
    ];
    if ($id) {
        DB::update('companies', $data, 'id = ?', [$id]);
        flash_set('success', 'Company updated.');
    } else {
        $id = DB::insert('companies', $data);
        flash_set('success', 'Company created.');
    }
    redirect('admin?r=company-edit&id=' . $id);
}

if ($route === 'company-delete' && $isPost) {
    csrf_require();
    DB::delete('companies', 'id = ?', [post_int('id')]);
    flash_set('success', 'Company deleted.');
    redirect('admin?r=companies');
}

/* =====================================================================
 | Leadership
 * ===================================================================*/
if ($route === 'leaders') {
    $leaders = DB::all('SELECT * FROM ' . DB::table('leaders') . ' ORDER BY sort_order ASC, id ASC');
    admin_render('leaders', ['title' => 'Leadership', 'leaders' => $leaders]);
    exit;
}

if ($route === 'leader-edit') {
    $id = (int)($_GET['id'] ?? 0);
    $leader = $id ? DB::get('SELECT * FROM ' . DB::table('leaders') . ' WHERE id = ?', [$id]) : null;
    admin_render('leader-edit', ['title' => $leader ? 'Edit profile' : 'New profile', 'leader' => $leader]);
    exit;
}

if ($route === 'leader-save' && $isPost) {
    csrf_require();
    $id = post_int('id');
    $name = post_str('name');
    if ($name === '') {
        flash_set('error', 'The profile needs a name.');
        redirect('admin?r=leader-edit' . ($id ? '&id=' . $id : ''));
    }
    $data = [
        'name'       => $name,
        'title'      => post_str('job_title'),
        'bio'        => (string)($_POST['bio'] ?? ''),
        'photo'      => post_str('photo'),
        'linkedin'   => post_str('linkedin'),
        'email'      => post_str('email'),
        'sort_order' => post_int('sort_order'),
        'active'     => post_int('active') ? 1 : 0,
    ];
    if ($id) {
        DB::update('leaders', $data, 'id = ?', [$id]);
        flash_set('success', 'Profile updated.');
    } else {
        $id = DB::insert('leaders', $data);
        flash_set('success', 'Profile created.');
    }
    redirect('admin?r=leaders');
}

if ($route === 'leader-delete' && $isPost) {
    csrf_require();
    DB::delete('leaders', 'id = ?', [post_int('id')]);
    flash_set('success', 'Profile deleted.');
    redirect('admin?r=leaders');
}

/* =====================================================================
 | Media library
 * ===================================================================*/
if ($route === 'media') {
    if ($isPost) {
        csrf_require();
        if (!empty($_FILES['file']['name'])) {
            [$ok, $resultPath, $mime, $size] = Security::handleUpload($_FILES['file']);
            if ($ok) {
                DB::insert('media', [
                    'filename'    => substr((string)$_FILES['file']['name'], 0, 255),
                    'path'        => $resultPath,
                    'mime'        => $mime,
                    'size'        => $size,
                    'uploaded_by' => Auth::id(),
                    'created_at'  => date('Y-m-d H:i:s'),
                ]);
                flash_set('success', 'File uploaded.');
            } else {
                flash_set('error', $resultPath);
            }
        } else {
            flash_set('error', 'Choose a file to upload.');
        }
        $pickerQS = isset($_GET['picker'])
            ? '&picker=1&target=' . urlencode(preg_replace('/[^A-Za-z0-9_\-]/', '', $_GET['target'] ?? ''))
            : '';
        redirect('admin?r=media' . $pickerQS);
    }
    $media = DB::all('SELECT * FROM ' . DB::table('media') . ' ORDER BY id DESC LIMIT 300');
    $picker = isset($_GET['picker']);
    if ($picker) {
        include APP_PATH . '/views/admin/media-picker.php';
    } else {
        admin_render('media', ['title' => 'Media Library', 'media' => $media]);
    }
    exit;
}

if ($route === 'media-delete' && $isPost) {
    csrf_require();
    $item = DB::get('SELECT * FROM ' . DB::table('media') . ' WHERE id = ?', [post_int('id')]);
    if ($item) {
        $file = ROOT_PATH . '/uploads/' . $item['path'];
        $real = realpath($file);
        $uploadsReal = realpath(ROOT_PATH . '/uploads');
        if ($real && $uploadsReal && str_starts_with($real, $uploadsReal)) {
            @unlink($real);
        }
        DB::delete('media', 'id = ?', [$item['id']]);
        flash_set('success', 'File deleted.');
    }
    redirect('admin?r=media');
}

/* =====================================================================
 | Messages
 * ===================================================================*/
if ($route === 'messages') {
    $filter = ($_GET['f'] ?? '') === 'unread' ? ' WHERE is_read = 0' : '';
    $messages = DB::all('SELECT * FROM ' . DB::table('messages') . $filter . ' ORDER BY id DESC LIMIT 500');
    admin_render('messages', ['title' => 'Messages', 'messages' => $messages]);
    exit;
}

if ($route === 'message-view') {
    $msg = DB::get('SELECT * FROM ' . DB::table('messages') . ' WHERE id = ?', [(int)($_GET['id'] ?? 0)]);
    if (!$msg) {
        flash_set('error', 'Message not found.');
        redirect('admin?r=messages');
    }
    if (!(int)$msg['is_read']) {
        DB::update('messages', ['is_read' => 1], 'id = ?', [$msg['id']]);
        $msg['is_read'] = 1;
    }
    admin_render('message-view', ['title' => 'Message', 'msg' => $msg]);
    exit;
}

if ($route === 'message-delete' && $isPost) {
    csrf_require();
    DB::delete('messages', 'id = ?', [post_int('id')]);
    flash_set('success', 'Message deleted.');
    redirect('admin?r=messages');
}

if ($route === 'message-unread' && $isPost) {
    csrf_require();
    DB::update('messages', ['is_read' => 0], 'id = ?', [post_int('id')]);
    redirect('admin?r=messages');
}

/* =====================================================================
 | Settings
 * ===================================================================*/
if ($route === 'settings') {
    $tab = $_GET['tab'] ?? 'general';
    $allowedTabs = ['general', 'branding', 'contact', 'social', 'seo', 'advanced'];
    if (!in_array($tab, $allowedTabs, true)) {
        $tab = 'general';
    }

    if ($isPost) {
        csrf_require();
        $fields = [
            'general'  => ['site_name', 'site_short', 'tagline', 'copyright', 'timezone'],
            'branding' => ['logo', 'logo_white', 'favicon'],
            'contact'  => ['contact_email', 'contact_phone', 'contact_address', 'office_hours', 'notify_on_contact', 'notify_email'],
            'social'   => ['social_linkedin', 'social_x', 'social_facebook', 'social_instagram', 'social_youtube'],
            'seo'      => ['meta_title', 'meta_description', 'analytics_code'],
            'advanced' => ['maintenance_mode'],
        ];
        foreach ($fields[$tab] as $key) {
            $val = $_POST[$key] ?? '';
            $val = is_string($val) ? trim($val) : '';
            if (in_array($key, ['notify_on_contact', 'maintenance_mode'], true)) {
                $val = $val === '1' ? '1' : '0';
            }
            if (in_array($key, ['contact_email', 'notify_email'], true) && $val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
                flash_set('error', 'Invalid email address for ' . $key . '; not saved.');
                continue;
            }
            Settings::set($key, $val);
        }
        flash_set('success', 'Settings saved.');
        redirect('admin?r=settings&tab=' . $tab);
    }

    admin_render('settings', ['title' => 'Settings', 'tab' => $tab]);
    exit;
}

/* =====================================================================
 | Users
 * ===================================================================*/
if ($route === 'users') {
    $users = DB::all('SELECT id, name, email, role, active, last_login, created_at FROM ' . DB::table('users') . ' ORDER BY id ASC');
    admin_render('users', ['title' => 'Administrators', 'users' => $users]);
    exit;
}

if ($route === 'user-edit') {
    $id = (int)($_GET['id'] ?? 0);
    $editUser = $id ? DB::get('SELECT * FROM ' . DB::table('users') . ' WHERE id = ?', [$id]) : null;
    admin_render('user-edit', ['title' => $editUser ? 'Edit administrator' : 'New administrator', 'editUser' => $editUser]);
    exit;
}

if ($route === 'user-save' && $isPost) {
    csrf_require();
    $id = post_int('id');
    $name = post_str('name');
    $email = strtolower(post_str('email'));
    $pass = (string)($_POST['password'] ?? '');
    $active = post_int('active') ? 1 : 0;

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash_set('error', 'Name and a valid email are required.');
        redirect('admin?r=user-edit' . ($id ? '&id=' . $id : ''));
    }
    $clash = DB::get('SELECT id FROM ' . DB::table('users') . ' WHERE email = ? AND id != ?', [$email, $id]);
    if ($clash) {
        flash_set('error', 'Another administrator already uses that email.');
        redirect('admin?r=user-edit' . ($id ? '&id=' . $id : ''));
    }
    if ($pass !== '' && strlen($pass) < 10) {
        flash_set('error', 'Password must be at least 10 characters.');
        redirect('admin?r=user-edit' . ($id ? '&id=' . $id : ''));
    }

    // Never deactivate the last active admin (or yourself)
    if ($id && !$active) {
        $activeCount = (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('users') . ' WHERE active = 1 AND id != ?', [$id]);
        if ($activeCount === 0 || $id === Auth::id()) {
            $active = 1;
            flash_set('error', 'You cannot deactivate your own account or the last active administrator.');
        }
    }

    $data = ['name' => $name, 'email' => $email, 'active' => $active];
    if ($pass !== '') {
        $data['password_hash'] = password_hash($pass, PASSWORD_DEFAULT);
    }
    if ($id) {
        DB::update('users', $data, 'id = ?', [$id]);
        flash_set('success', 'Administrator updated.');
    } else {
        if ($pass === '') {
            flash_set('error', 'A password is required for a new administrator.');
            redirect('admin?r=user-edit');
        }
        $data['role'] = 'admin';
        $data['created_at'] = date('Y-m-d H:i:s');
        DB::insert('users', $data);
        flash_set('success', 'Administrator created.');
    }
    redirect('admin?r=users');
}

if ($route === 'user-delete' && $isPost) {
    csrf_require();
    $id = post_int('id');
    if ($id === Auth::id()) {
        flash_set('error', 'You cannot delete your own account.');
    } elseif ((int)DB::val('SELECT COUNT(*) FROM ' . DB::table('users') . ' WHERE active = 1 AND id != ?', [$id]) === 0) {
        flash_set('error', 'You cannot delete the last active administrator.');
    } else {
        DB::delete('users', 'id = ?', [$id]);
        flash_set('success', 'Administrator deleted.');
    }
    redirect('admin?r=users');
}

/* =====================================================================
 | Fallback
 * ===================================================================*/
flash_set('error', 'Unknown admin page.');
redirect('admin');
