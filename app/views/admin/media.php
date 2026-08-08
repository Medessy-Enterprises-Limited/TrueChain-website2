<?php /** @var array $media */ ?>
<div class="acard">
  <div class="acard-head">
    <div><h2>Media library</h2><p class="hint">Upload images for sliders, logos, leadership photos and page content. Allowed: JPG, PNG, GIF, WEBP, SVG, ICO, PDF · up to 8 MB.</p></div>
  </div>
  <form class="upload-zone" method="post" action="<?= e(url('admin?r=media')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <p><strong>Upload a file</strong></p>
    <input type="file" name="file" accept=".jpg,.jpeg,.png,.gif,.webp,.svg,.ico,.pdf" required>
    <p style="margin-top:12px"><button class="abtn" type="submit">Upload</button></p>
  </form>

  <?php if (!$media): ?>
    <p style="color:var(--muted)">No files uploaded yet.</p>
  <?php else: ?>
    <div class="media-grid">
      <?php foreach ($media as $m): $url = upload_url($m['path']); $isImg = str_starts_with((string)$m['mime'], 'image/'); ?>
        <div class="media-item">
          <div class="media-thumb">
            <?php if ($isImg): ?><img src="<?= e($url) ?>" alt="" loading="lazy"><?php else: ?><?= tc_icon('doc', '') ?><?php endif; ?>
          </div>
          <div class="media-meta">
            <span class="media-name"><?= e(mb_substr($m['filename'], 0, 34)) ?></span>
            <span class="media-sub"><?= e(format_bytes((int)$m['size'])) ?> · <?= e(format_date($m['created_at'])) ?></span>
            <div class="media-actions">
              <button class="abtn abtn-ghost abtn-sm" type="button" onclick="navigator.clipboard&&navigator.clipboard.writeText('<?= e($m['path']) ?>');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy path',1200)">Copy path</button>
              <a class="abtn abtn-ghost abtn-sm" href="<?= e($url) ?>" target="_blank" rel="noopener">Open</a>
              <form method="post" action="<?= e(url('admin?r=media-delete')) ?>" data-confirm="Delete this file? Anywhere it is used will show a broken image.">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
                <button class="abtn abtn-danger abtn-sm" type="submit">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
