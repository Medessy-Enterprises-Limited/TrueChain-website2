</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <img src="<?= e(upload_url(setting('logo_white', 'assets/img/logo-white.png'))) ?>" alt="<?= e(setting('site_name')) ?>" class="footer-logo">
        <div class="footer-about"><?= block('footer-about') ?></div>
        <?php
        $socials = [
            'linkedin'  => setting('social_linkedin'),
            'x-social'  => setting('social_x'),
            'facebook'  => setting('social_facebook'),
            'instagram' => setting('social_instagram'),
            'youtube'   => setting('social_youtube'),
        ];
        $hasSocial = array_filter($socials);
        ?>
        <?php if ($hasSocial): ?>
        <div class="footer-social">
          <?php foreach ($socials as $icon => $href): if (!$href) continue; ?>
            <a href="<?= e($href) ?>" target="_blank" rel="noopener" aria-label="<?= e(ucfirst(str_replace('-social', '', $icon))) ?>"><?= tc_icon($icon) ?></a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div class="footer-col">
        <h4>Our Companies</h4>
        <ul>
          <?php foreach (DB::all('SELECT slug, short_name, name FROM ' . DB::table('companies') . ' WHERE active = 1 ORDER BY sort_order ASC LIMIT 8') as $fc): ?>
            <li><a href="<?= e(url('companies/' . $fc['slug'])) ?>"><?= e($fc['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="footer-col">
        <h4>The Group</h4>
        <ul>
          <li><a href="<?= e(url('about')) ?>">About the Group</a></li>
          <li><a href="<?= e(url('leadership')) ?>">Leadership</a></li>
          <li><a href="<?= e(url('companies')) ?>">Our Companies</a></li>
          <li><a href="<?= e(url('contact')) ?>">Contact</a></li>
          <li><a href="<?= e(url('privacy-policy')) ?>">Privacy Policy</a></li>
          <li><a href="<?= e(url('terms-of-use')) ?>">Terms of Use</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Contact</h4>
        <ul class="footer-contact">
          <?php if (setting('contact_address')): ?>
            <li><?= tc_icon('pin') ?><span><?= e(setting('contact_address')) ?></span></li>
          <?php endif; ?>
          <?php if (setting('contact_phone')): ?>
            <li><?= tc_icon('phone') ?><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></li>
          <?php endif; ?>
          <?php if (setting('contact_email')): ?>
            <li><?= tc_icon('mail') ?><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li>
          <?php endif; ?>
          <?php if (setting('office_hours')): ?>
            <li><?= tc_icon('clock') ?><span><?= e(setting('office_hours')) ?></span></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <?= e(setting('copyright', setting('site_name') . '. All rights reserved.')) ?></p>
      <p class="footer-tagline"><?= e(setting('tagline')) ?></p>
    </div>
  </div>
</footer>

<button class="back-to-top" id="backToTop" aria-label="Back to top"><?= tc_icon('arrow-up') ?></button>

<script src="<?= e(asset('js/site.js')) ?>?v=1.0" defer></script>
</body>
</html>
