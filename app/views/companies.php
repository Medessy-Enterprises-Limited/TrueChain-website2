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
        <a class="company-card reveal reveal-d<?= ($i % 3) + 1 ?>" href="<?= e(url('companies/' . $c['slug'])) ?>">
          <div class="company-icon"><?= tc_icon($c['icon'] ?: 'group') ?></div>
          <div class="company-cat"><?= e($c['category']) ?></div>
          <h3><?= e($c['name']) ?></h3>
          <p><?= e($c['summary'] ?: $c['tagline']) ?></p>
          <span class="company-link">Learn more <?= tc_icon('arrow') ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
