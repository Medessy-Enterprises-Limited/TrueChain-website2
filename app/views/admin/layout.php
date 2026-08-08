<?php
/** Admin shell. Expects: $title, $_view (content view name), plus view data. */
$adminUser = Auth::user() ?? ['name' => '', 'email' => ''];
$unread = (int)DB::val('SELECT COUNT(*) FROM ' . DB::table('messages') . ' WHERE is_read = 0');
$navItems = [
    ['dashboard', 'Dashboard', 'target'],
    ['pages', 'Pages', 'doc'],
    ['sliders', 'Hero Slider', 'spark'],
    ['blocks', 'Static Blocks', 'group'],
    ['companies', 'Companies', 'cln'],
    ['leaders', 'Leadership', 'users'],
    ['media', 'Media Library', 'eye'],
    ['messages', 'Messages', 'mail'],
    ['users', 'Administrators', 'lock'],
    ['settings', 'Settings', 'shield'],
];
$current = $_GET['r'] ?? 'dashboard';
$currentBase = preg_replace('/-(edit|view|save|delete|unread)$/', 's', $current);
$aliases = ['pages' => 'pages', 'pagess' => 'pages', 'sliders' => 'sliders', 'sliderss' => 'sliders',
    'blockss' => 'blocks', 'companys' => 'companies', 'companiess' => 'companies', 'leaderss' => 'leaders',
    'messagess' => 'messages', 'userss' => 'users'];
$currentBase = $aliases[$currentBase] ?? $currentBase;
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= e($title ?? 'Admin') ?> · <?= e(setting('site_short', 'True Chain')) ?> Admin</title>
<link rel="icon" type="image/png" href="<?= e(upload_url(setting('favicon', 'assets/img/favicon.png'))) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>?v=1.0">
</head>
<body data-base="<?= e(BASE_PATH) ?>">
<div class="admin-shell">

  <aside class="admin-side" id="adminSide">
    <a class="admin-brand" href="<?= e(url('admin')) ?>">
      <img src="<?= e(asset('img/favicon.png')) ?>" alt="" class="admin-brand-mark">
      <span>True Chain <em>Admin</em></span>
    </a>
    <nav class="admin-nav">
      <?php foreach ($navItems as [$r, $label, $icon]): ?>
        <a href="<?= e(url('admin?r=' . $r)) ?>" class="<?= $currentBase === $r ? 'active' : '' ?>">
          <?= tc_icon($icon) ?><span><?= e($label) ?></span>
          <?php if ($r === 'messages' && $unread): ?><b class="badge-count"><?= $unread ?></b><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="admin-side-foot">
      <a href="<?= e(url('')) ?>" target="_blank" rel="noopener"><?= tc_icon('external') ?><span>View website</span></a>
      <form method="post" action="<?= e(url('admin?r=logout')) ?>"><?= csrf_field() ?>
        <button type="submit"><?= tc_icon('close') ?><span>Sign out</span></button>
      </form>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-top">
      <button class="side-toggle" id="sideToggle" aria-label="Toggle menu"><?= tc_icon('menu') ?></button>
      <h1><?= e($title ?? 'Admin') ?></h1>
      <div class="admin-top-right">
        <span class="admin-user"><?= tc_icon('users') ?> <?= e($adminUser['name'] ?? '') ?></span>
        <a class="abtn abtn-ghost" href="<?= e(url('admin?r=user-edit&id=' . Auth::id())) ?>">My account</a>
      </div>
    </header>

    <div class="admin-content">
      <?php foreach (flash_pull() as $f): ?>
        <div class="aflash <?= $f['type'] === 'success' ? 'aflash-ok' : 'aflash-err' ?>">
          <?= tc_icon($f['type'] === 'success' ? 'check' : 'close') ?><span><?= e($f['message']) ?></span>
        </div>
      <?php endforeach; ?>

      <?php include APP_PATH . '/views/admin/' . $_view . '.php'; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="<?= e(asset('js/admin.js')) ?>?v=1.0"></script>
</body>
</html>
