<?php /** @var array $companies */ ?>
<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('')) ?>">Home</a><span class="sep">/</span><span>Our Companies</span>
    </nav>
    <h1>Our Companies</h1>
    <p class="page-sub">The operating companies of the True Chain group: technology, training and corridor infrastructure designed as one integrated chain.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="companies-grid">
      <?php foreach ($companies as $i => $c): ?>
        <div class="company-card reveal reveal-d<?= ($i % 3) + 1 ?>">
          <div class="company-icon"><?= tc_icon($c['icon'] ?: 'group') ?></div>
          <div class="company-cat"><?= e($c['category']) ?></div>
          <h3><?= e($c['name']) ?></h3>
          <p><?= e($c['summary'] ?: $c['tagline']) ?></p>
          <div class="company-actions">
            <a class="company-link card-link" href="<?= e(url('companies/' . $c['slug'])) ?>">Learn more <?= tc_icon('arrow') ?></a>
            <?php if ($c['website_url'] && $c['website_url'] !== '#'): ?>
              <a class="company-visit" href="<?= e($c['website_url']) ?>" target="_blank" rel="noopener">
                Visit site <?= tc_icon('external') ?>
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
