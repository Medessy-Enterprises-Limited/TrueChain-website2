<?php /** @var array $errors @var bool $sent @var array $old */ ?>
<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('')) ?>">Home</a><span class="sep">/</span><span>Contact</span>
    </nav>
    <h1>Contact Us</h1>
    <p class="page-sub">One enquiry reaches the whole group. Tell us what you need and we will route it to the right company.</p>
  </div>
</section>

<section class="section">
  <div class="container contact-layout">
    <div class="contact-info reveal">
      <h2><?= e(DB::val('SELECT title FROM ' . DB::table('blocks') . " WHERE identifier='contact-intro'") ?: 'Let’s talk') ?></h2>
      <div class="section-sub"><?= block('contact-intro') ?></div>
      <div class="contact-cards">
        <?php if (setting('contact_address')): ?>
        <div class="contact-card">
          <div class="ci"><?= tc_icon('pin') ?></div>
          <div><strong>Head office</strong><span><?= e(setting('contact_address')) ?></span></div>
        </div>
        <?php endif; ?>
        <?php if (setting('contact_phone')): ?>
        <div class="contact-card">
          <div class="ci"><?= tc_icon('phone') ?></div>
          <div><strong>Phone</strong><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></div>
        </div>
        <?php endif; ?>
        <?php if (setting('contact_email')): ?>
        <div class="contact-card">
          <div class="ci"><?= tc_icon('mail') ?></div>
          <div><strong>Email</strong><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></div>
        </div>
        <?php endif; ?>
        <?php if (setting('office_hours')): ?>
        <div class="contact-card">
          <div class="ci"><?= tc_icon('clock') ?></div>
          <div><strong>Office hours</strong><span><?= e(setting('office_hours')) ?></span></div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="form-card reveal reveal-d1">
      <?php if ($sent): ?>
        <div class="alert alert-ok"><?= tc_icon('check') ?><div><strong>Thank you, your message has been received.</strong><br>Our team will get back to you as soon as possible.</div></div>
      <?php endif; ?>
      <?php if (!empty($errors['rate'])): ?>
        <div class="alert alert-err"><?= tc_icon('close') ?><div><?= e($errors['rate']) ?></div></div>
      <?php endif; ?>

      <form method="post" action="<?= e(url('contact')) ?>" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="form_ts" id="form_ts" value="">
        <div class="hp-field" aria-hidden="true">
          <label>Leave this field empty<input type="text" name="website_url_hp" tabindex="-1" autocomplete="off"></label>
        </div>

        <div class="form-grid">
          <div class="field <?= isset($errors['name']) ? 'invalid' : '' ?>">
            <label for="f-name">Full name *</label>
            <input id="f-name" name="name" required maxlength="190" value="<?= e($old['name']) ?>" autocomplete="name">
            <?php if (isset($errors['name'])): ?><span class="err-text"><?= e($errors['name']) ?></span><?php endif; ?>
          </div>
          <div class="field <?= isset($errors['email']) ? 'invalid' : '' ?>">
            <label for="f-email">Email address *</label>
            <input id="f-email" name="email" type="email" required maxlength="190" value="<?= e($old['email']) ?>" autocomplete="email">
            <?php if (isset($errors['email'])): ?><span class="err-text"><?= e($errors['email']) ?></span><?php endif; ?>
          </div>
          <div class="field">
            <label for="f-phone">Phone</label>
            <input id="f-phone" name="phone" maxlength="50" value="<?= e($old['phone']) ?>" autocomplete="tel">
          </div>
          <div class="field">
            <label for="f-company">Organisation</label>
            <input id="f-company" name="company" maxlength="190" value="<?= e($old['company']) ?>" autocomplete="organization">
          </div>
          <div class="field full">
            <label for="f-subject">What is your enquiry about?</label>
            <select id="f-subject" name="subject">
              <?php
              $subjects = ['General enquiry', 'Partnership / commercial', 'True Chain Registry', 'True Chain SOC', 'Collaborative Logistics Network', 'True Chain Institute (training)', 'Truck Transit Park', 'Investor relations', 'Careers', 'Press and media', 'Data protection request'];
              foreach ($subjects as $s): ?>
                <option value="<?= e($s) ?>" <?= $old['subject'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field full <?= isset($errors['message']) ? 'invalid' : '' ?>">
            <label for="f-message">Message *</label>
            <textarea id="f-message" name="message" required maxlength="5000" placeholder="Tell us about your fleet, your training needs, or your project…"><?= e($old['message']) ?></textarea>
            <?php if (isset($errors['message'])): ?><span class="err-text"><?= e($errors['message']) ?></span><?php endif; ?>
          </div>
        </div>
        <p style="margin:20px 0 0">
          <button class="btn btn-primary btn-lg" type="submit">Send message <?= tc_icon('arrow') ?></button>
        </p>
        <p style="font-size:13px;color:var(--muted);margin:16px 0 0">By submitting this form you agree to our <a href="<?= e(url('privacy-policy')) ?>">Privacy Policy</a>.</p>
      </form>
    </div>
  </div>
</section>
