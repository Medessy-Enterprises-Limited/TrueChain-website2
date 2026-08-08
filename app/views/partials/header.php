<?php
/** @var string $pageTitle */
$siteName = setting('site_name', 'True Chain Infrastructure Company');
$metaDesc = $metaDescription ?? setting('meta_description');
$isBrandedTitle = !isset($pageTitle) || $pageTitle === '' || $pageTitle === $siteName
    || $pageTitle === setting('meta_title')
    || str_contains($pageTitle, setting('site_short', 'True Chain'));
$fullTitle = $isBrandedTitle
    ? ($pageTitle ?? '' ?: $siteName)
    : $pageTitle . ' · ' . setting('site_short', 'True Chain');
$currentPath = request_path();

// Build navigation: built-in routes + CMS pages flagged for nav
$navItems = [
    ['order' => 20, 'slug' => 'companies',  'label' => 'Our Companies'],
    ['order' => 30, 'slug' => 'leadership', 'label' => 'Leadership'],
];
foreach (nav_pages() as $np) {
    $navItems[] = ['order' => 0, 'slug' => $np['slug'], 'label' => $np['nav_label'] ?: $np['title']];
}
// pull real nav_order for pages
foreach ($navItems as &$ni) {
    if ($ni['order'] === 0) {
        $row = DB::get('SELECT nav_order FROM ' . DB::table('pages') . ' WHERE slug = ?', [$ni['slug']]);
        $ni['order'] = (int)($row['nav_order'] ?? 50);
    }
}
unset($ni);
usort($navItems, fn($a, $b) => $a['order'] <=> $b['order']);
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<?php if ($metaDesc): ?><meta name="description" content="<?= e($metaDesc) ?>"><?php endif; ?>
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<?php if ($metaDesc): ?><meta property="og:description" content="<?= e($metaDesc) ?>"><?php endif; ?>
<meta property="og:image" content="<?= e(site_url(ltrim(upload_url(setting('logo', 'assets/img/logo.png')), '/'))) ?>">
<link rel="icon" type="image/png" href="<?= e(upload_url(setting('favicon', 'assets/img/favicon.png'))) ?>">
<link rel="apple-touch-icon" href="<?= e(asset('img/apple-touch-icon.png')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/site.css')) ?>?v=1.0">
<?= setting('analytics_code') /* admin-managed, trusted */ ?>
</head>
<body class="<?= e($bodyClass ?? '') ?>">
<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header" id="siteHeader">
  <div class="container header-inner">
    <a class="brand" href="<?= e(url('')) ?>" aria-label="<?= e($siteName) ?> home">
      <img src="<?= e(upload_url(setting('logo', 'assets/img/logo.png'))) ?>" alt="<?= e($siteName) ?>" class="brand-logo">
    </a>

    <nav class="main-nav" id="mainNav" aria-label="Primary">
      <ul>
        <li><a href="<?= e(url('')) ?>" class="<?= $currentPath === '' ? 'active' : '' ?>">Home</a></li>
        <?php foreach ($navItems as $item): ?>
          <li><a href="<?= e(url($item['slug'])) ?>" class="<?= $currentPath === $item['slug'] || str_starts_with($currentPath, $item['slug'] . '/') ? 'active' : '' ?>"><?= e($item['label']) ?></a></li>
        <?php endforeach; ?>
        <li class="nav-cta-mobile"><a href="<?= e(url('contact')) ?>" class="<?= $currentPath === 'contact' ? 'active' : '' ?>">Contact</a></li>
      </ul>
    </nav>

    <div class="header-actions">
      <a class="btn btn-primary btn-sm header-cta" href="<?= e(url('contact')) ?>">Contact us</a>
      <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="mainNav" aria-label="Toggle menu">
        <?= tc_icon('menu', 'icon-menu') ?><?= tc_icon('close', 'icon-close') ?>
      </button>
    </div>
  </div>
</header>

<main id="main">
