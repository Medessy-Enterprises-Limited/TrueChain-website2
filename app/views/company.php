<?php /** @var array $company @var array $others */ ?>
<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('')) ?>">Home</a><span class="sep">/</span>
      <a href="<?= e(url('companies')) ?>">Our Companies</a><span class="sep">/</span>
      <span><?= e($company['short_name'] ?: $company['name']) ?></span>
    </nav>
    <div class="company-hero-icon"><?= tc_icon($company['icon'] ?: 'group') ?></div>
    <h1><?= e($company['name']) ?></h1>
    <?php if ($company['tagline']): ?><p class="page-sub"><?= e($company['tagline']) ?></p><?php endif; ?>
    <div class="company-meta">
      <?php if ($company['category']): ?><span class="badge badge-cat"><?= e($company['category']) ?></span><?php endif; ?>
      <?php if ($company['site_status'] === 'live'): ?>
        <span class="badge badge-live">● Platform live</span>
      <?php else: ?>
        <span class="badge badge-soon">● Platform coming soon</span>
      <?php endif; ?>
    </div>
  </div>
</section>

<div class="page-body">
  <div class="container company-layout">
    <article class="prose">
      <?= $company['content'] /* admin-authored HTML */ ?>
    </article>

    <aside class="company-aside">
      <?php $hasSite = $company['website_url'] && $company['website_url'] !== '#'; ?>
      <div class="aside-card">
        <h4>Visit the platform</h4>
        <?php if ($hasSite): ?>
          <a class="btn btn-primary" href="<?= e($company['website_url']) ?>" target="_blank" rel="noopener">
            Open website <?= tc_icon('external') ?>
          </a>
          <p class="note">Opens the dedicated <?= e($company['short_name'] ?: $company['name']) ?> website.</p>
        <?php else: ?>
          <a class="btn btn-outline" href="<?= e(url('contact')) ?>">Register interest <?= tc_icon('arrow') ?></a>
          <p class="note">The dedicated website launches soon. Contact us for early access.</p>
        <?php endif; ?>
      </div>

      <div class="aside-card">
        <h4>More from the group</h4>
        <ul class="aside-list">
          <?php foreach ($others as $o): ?>
            <li><a href="<?= e(url('companies/' . $o['slug'])) ?>"><?= tc_icon($o['icon'] ?: 'group') ?><?= e($o['name']) ?></a></li>
          <?php endforeach; ?>
          <li><a href="<?= e(url('companies')) ?>"><?= tc_icon('arrow') ?>All companies</a></li>
        </ul>
      </div>
    </aside>
  </div>
</div>
