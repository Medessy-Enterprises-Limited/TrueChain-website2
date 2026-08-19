<?php /** @var array $sliders @var array $companies */ ?>

<!-- ============ Hero slider ============ -->
<section class="hero" data-slider aria-label="Highlights">
  <?php foreach ($sliders as $i => $s): ?>
    <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>" style="background-image:url('<?= e(upload_url($s['image'])) ?>')">
      <div class="container">
        <div class="hero-content">
          <span class="hero-kicker"><?= e(setting('site_name')) ?></span>
          <h1><?= e($s['title']) ?></h1>
          <?php if ($s['subtitle']): ?><p class="hero-sub"><?= e($s['subtitle']) ?></p><?php endif; ?>
          <div class="hero-ctas">
            <?php if ($s['cta_text']): ?>
              <a class="btn btn-primary btn-lg" href="<?= e(url($s['cta_url'] ?: '')) ?>"><?= e($s['cta_text']) ?> <?= tc_icon('arrow') ?></a>
            <?php endif; ?>
            <?php if ($s['cta2_text']): ?>
              <a class="btn btn-ghost-light btn-lg" href="<?= e(url($s['cta2_url'] ?: '')) ?>"><?= e($s['cta2_text']) ?></a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (count($sliders) > 1): ?>
    <div class="hero-controls">
      <div class="container" style="display:flex;align-items:center;justify-content:space-between">
        <div class="hero-dots" role="tablist" aria-label="Slides">
          <?php foreach ($sliders as $i => $s): ?>
            <button class="hero-dot<?= $i === 0 ? ' active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
          <?php endforeach; ?>
        </div>
        <div class="hero-arrows">
          <button class="hero-arrow" data-prev aria-label="Previous slide"><?= tc_icon('chev-l') ?></button>
          <button class="hero-arrow" data-next aria-label="Next slide"><?= tc_icon('chev-r') ?></button>
        </div>
      </div>
    </div>
  <?php endif; ?>
</section>

<!-- ============ Group intro ============ -->
<section class="section">
  <div class="container intro-grid">
    <div class="intro-copy reveal">
      <div class="section-head">
        <span class="kicker">The Group</span>
        <h2 class="section-title"><?= e(DB::val('SELECT title FROM ' . DB::table('blocks') . " WHERE identifier='home-intro'") ?: 'One group. One platform.') ?></h2>
      </div>
      <div class="section-sub"><?= block('home-intro') ?></div>
      <div class="intro-points">
        <div class="intro-point"><?= tc_icon('check') ?><div><strong>Legally separable, operationally integrated</strong><span>Each company stands alone commercially, yet shares one chain of trust.</span></div></div>
        <div class="intro-point"><?= tc_icon('check') ?><div><strong>Built from real operations</strong><span>Engineered on 11+ years of FMCG logistics execution, not assumptions.</span></div></div>
        <div class="intro-point"><?= tc_icon('check') ?><div><strong>Governed to institutional standards</strong><span>Board-supervised, ESG-committed, NDPA-compliant by design.</span></div></div>
      </div>
      <p style="margin-top:34px"><a class="btn btn-outline" href="<?= e(url('about')) ?>">About the group <?= tc_icon('arrow') ?></a></p>
    </div>

    <div class="intro-visual reveal reveal-d1">
      <div class="org-chart">
        <div class="org-root">
          <strong>True Chain Infrastructure Company</strong>
          <span>Holding Group</span>
        </div>
        <div class="org-line"></div>
        <div class="org-children">
          <a class="org-child" href="<?= e(url('companies/true-chain-registry')) ?>">
            <?= tc_icon('group') ?>
            <strong>True Chain Technologies</strong>
            <span>Registry · SOC · CLN</span>
          </a>
          <a class="org-child" href="<?= e(url('companies/true-chain-institute')) ?>">
            <?= tc_icon('institute') ?>
            <strong>True Chain Institute</strong>
            <span>Training Academy</span>
          </a>
          <a class="org-child" href="<?= e(url('companies/truck-transit-park')) ?>">
            <?= tc_icon('park') ?>
            <strong>Truck Transit Park</strong>
            <span>Corridor Infrastructure</span>
          </a>
        </div>
        <p class="org-note">Anchored by Medessy Enterprises: 11+ years of FMCG logistics heritage</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ Stats ============ -->
<section class="stats-band" aria-label="Group statistics">
  <div class="container">
    <div class="stats-grid reveal">
      <?= block('home-stats') ?>
    </div>
  </div>
</section>

<!-- ============ Companies ============ -->
<section class="section section-soft">
  <div class="container">
    <div class="section-head center reveal">
      <span class="kicker">Our Companies</span>
      <h2 class="section-title">Six ventures. One chain of trust.</h2>
      <p class="section-sub">Every company in the group removes one binding constraint of African road freight, and each one makes the others stronger.</p>
    </div>
    <div class="companies-grid">
      <?php foreach ($companies as $i => $c): ?>
        <div class="company-card reveal reveal-d<?= ($i % 3) + 1 ?>">
          <div class="company-icon"><?= tc_icon($c['icon'] ?: 'group') ?></div>
          <div class="company-cat"><?= e($c['category']) ?></div>
          <h3><?= e($c['name']) ?></h3>
          <p><?= e($c['tagline']) ?></p>
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

<!-- ============ Ecosystem flow ============ -->
<section class="section">
  <div class="container">
    <div class="section-head center reveal">
      <span class="kicker">The Ecosystem</span>
      <h2 class="section-title"><?= e(DB::val('SELECT title FROM ' . DB::table('blocks') . " WHERE identifier='home-ecosystem-intro'") ?: 'One driver record across the chain') ?></h2>
      <div class="section-sub"><?= block('home-ecosystem-intro') ?></div>
    </div>
    <div class="flow">
      <div class="flow-step reveal">
        <div class="flow-icon"><?= tc_icon('institute') ?></div>
        <span class="flow-tag">Train</span>
        <h3>Institute</h3>
        <p>Accredited training writes verified certifications to the driver’s TCID.</p>
      </div>
      <div class="flow-step reveal reveal-d1">
        <div class="flow-icon"><?= tc_icon('registry') ?></div>
        <span class="flow-tag">Verify</span>
        <h3>Registry</h3>
        <p>Identity, licence and integrity records, evidence-scored and disputable.</p>
      </div>
      <div class="flow-step reveal reveal-d2">
        <div class="flow-icon"><?= tc_icon('soc') ?></div>
        <span class="flow-tag">Secure</span>
        <h3>SOC</h3>
        <p>24/7 telematics, AI monitoring, panic SOS and corridor rescue.</p>
      </div>
      <div class="flow-step reveal reveal-d3">
        <div class="flow-icon"><?= tc_icon('cln') ?></div>
        <span class="flow-tag">Coordinate</span>
        <h3>CLN</h3>
        <p>Shared capacity and matched loads cut empty kilometres and cost.</p>
      </div>
      <div class="flow-step reveal reveal-d4">
        <div class="flow-icon"><?= tc_icon('park') ?></div>
        <span class="flow-tag">Support</span>
        <h3>Transit Parks</h3>
        <p>Secure rest, workshops and LCNG refuelling along every corridor.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ CTA ============ -->
<section class="section" style="padding-top:24px">
  <div class="container">
    <div class="cta-band reveal">
      <div>
        <h2><?= e(DB::val('SELECT title FROM ' . DB::table('blocks') . " WHERE identifier='home-cta'") ?: 'Build with us') ?></h2>
        <?= block('home-cta') ?>
      </div>
      <div class="cta-actions">
        <a class="btn btn-primary btn-lg" style="background:#fff;color:var(--navy);box-shadow:none" href="<?= e(url('contact')) ?>">Get in touch <?= tc_icon('arrow') ?></a>
      </div>
    </div>
  </div>
</section>
