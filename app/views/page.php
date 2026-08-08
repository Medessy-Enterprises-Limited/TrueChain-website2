<?php /** @var array $page */ ?>
<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('')) ?>">Home</a><span class="sep">/</span><span><?= e($page['title']) ?></span>
    </nav>
    <h1><?= e($page['title']) ?></h1>
  </div>
</section>

<div class="page-body">
  <div class="container">
    <article class="prose">
      <?= $page['content'] /* admin-authored HTML */ ?>
    </article>
  </div>
</div>
