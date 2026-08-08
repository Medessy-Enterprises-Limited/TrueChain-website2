<?php /** @var array $leaders */ ?>
<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('')) ?>">Home</a><span class="sep">/</span><span>Leadership</span>
    </nav>
    <h1>Leadership</h1>
    <p class="page-sub">Operational depth, doctoral-level rigour and institutional governance, the leadership building the True Chain group.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="leaders-grid">
      <?php foreach ($leaders as $i => $l): ?>
        <div class="leader-card reveal reveal-d<?= ($i % 3) + 1 ?>">
          <div class="leader-photo">
            <?php if ($l['photo']): ?>
              <img src="<?= e(upload_url($l['photo'])) ?>" alt="<?= e($l['name']) ?>">
            <?php else: ?>
              <?php
              $initials = implode('', array_map(fn($w) => mb_substr($w, 0, 1),
                  array_slice(array_filter(preg_split('/\s+/', preg_replace('/^(Dr|Mr|Mrs|Ms|Prof)\.?\s+/i', '', $l['name']))), 0, 2)));
              ?>
              <div class="leader-initials"><?= e(strtoupper($initials)) ?></div>
            <?php endif; ?>
          </div>
          <div class="leader-body">
            <h3><?= e($l['name']) ?></h3>
            <div class="leader-role"><?= e($l['title']) ?></div>
            <div class="leader-bio clamp"><?= $l['bio'] /* admin-authored HTML */ ?></div>
            <button class="leader-more" data-bio-toggle type="button">Read more</button>
            <?php if ($l['linkedin']): ?>
              <p style="margin:14px 0 0"><a href="<?= e($l['linkedin']) ?>" target="_blank" rel="noopener" class="company-link"><?= tc_icon('linkedin') ?> LinkedIn</a></p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="cta-band reveal" style="margin-top:72px">
      <div>
        <h2>Join the build-out</h2>
        <p>The group is recruiting senior executives across operations, finance, technology, ESG and training as part of its institutional strengthening plan.</p>
      </div>
      <div class="cta-actions">
        <a class="btn btn-primary btn-lg" style="background:#fff;color:var(--navy);box-shadow:none" href="<?= e(url('contact')) ?>">Express interest <?= tc_icon('arrow') ?></a>
      </div>
    </div>
  </div>
</section>
