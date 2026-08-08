<?php
/** Media picker (popup). @var array $media */
$target = preg_replace('/[^A-Za-z0-9_\-]/', '', $_GET['target'] ?? '');
$builtins = [
    'assets/img/hero-1.svg' => 'Hero artwork 1',
    'assets/img/hero-2.svg' => 'Hero artwork 2',
    'assets/img/hero-3.svg' => 'Hero artwork 3',
    'assets/img/logo.png'   => 'Logo (colour)',
    'assets/img/logo-white.png' => 'Logo (white)',
    'assets/img/favicon.png' => 'Favicon mark',
];
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Choose media</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sora:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>?v=1.0">
<style>body{padding:22px}h1{font-size:17px}</style>
</head>
<body data-base="<?= e(BASE_PATH) ?>">
  <h1>Choose a file</h1>
  <form class="upload-zone" method="post" action="<?= e(url('admin?r=media&picker=1&target=' . $target)) ?>" enctype="multipart/form-data" style="padding:16px">
    <?= csrf_field() ?>
    <input type="file" name="file" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.ico,.pdf" required>
    <button class="abtn abtn-sm" type="submit" style="margin-left:8px">Upload</button>
  </form>

  <h2 style="font-size:14px;margin:18px 0 10px">Built-in artwork</h2>
  <div class="media-grid">
    <?php foreach ($builtins as $path => $label): ?>
      <div class="media-item">
        <div class="media-thumb"><img src="<?= e(url($path)) ?>" alt=""></div>
        <div class="media-meta">
          <span class="media-name"><?= e($label) ?></span>
          <div class="media-actions">
            <button class="abtn abtn-sm" type="button" onclick="pick('<?= e($path) ?>')">Use this</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <h2 style="font-size:14px;margin:22px 0 10px">Your uploads</h2>
  <?php if (!$media): ?><p style="color:var(--muted)">Nothing uploaded yet.</p><?php endif; ?>
  <div class="media-grid">
    <?php foreach ($media as $m): $u = upload_url($m['path']); $isImg = str_starts_with((string)$m['mime'], 'image/'); ?>
      <div class="media-item">
        <div class="media-thumb"><?php if ($isImg): ?><img src="<?= e($u) ?>" alt="" loading="lazy"><?php else: ?>PDF<?php endif; ?></div>
        <div class="media-meta">
          <span class="media-name"><?= e(mb_substr($m['filename'], 0, 30)) ?></span>
          <div class="media-actions">
            <button class="abtn abtn-sm" type="button" onclick="pick('<?= e($m['path']) ?>')">Use this</button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<script>
function pick(path) {
  var target = <?= json_encode($target) ?>;
  if (window.opener && !window.opener.closed && target && window.opener.__setMediaField) {
    window.opener.__setMediaField(target, path);
  }
  window.close();
}
</script>
</body>
</html>
