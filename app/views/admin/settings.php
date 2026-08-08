<?php /** @var string $tab */
$tabs = [
    'general'  => 'General',
    'branding' => 'Branding',
    'contact'  => 'Contact details',
    'social'   => 'Social media',
    'seo'      => 'SEO & Analytics',
    'advanced' => 'Advanced',
];
?>
<div class="tabs">
  <?php foreach ($tabs as $key => $label): ?>
    <a href="<?= e(url('admin?r=settings&tab=' . $key)) ?>" class="<?= $tab === $key ? 'active' : '' ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<form class="aform" method="post" action="<?= e(url('admin?r=settings&tab=' . $tab)) ?>">
  <?= csrf_field() ?>

  <?php if ($tab === 'general'): ?>
  <div class="acard">
    <div class="acard-head"><div><h2>General</h2><p class="hint">Identity used across the website.</p></div></div>
    <div class="grid2">
      <div class="frow"><label>Site name</label><input type="text" name="site_name" value="<?= e(setting('site_name')) ?>"></div>
      <div class="frow"><label>Short name</label><input type="text" name="site_short" value="<?= e(setting('site_short')) ?>"><p class="help">Used in browser titles, e.g. “About · True Chain”.</p></div>
    </div>
    <div class="frow"><label>Tagline</label><input type="text" name="tagline" value="<?= e(setting('tagline')) ?>"><p class="help">Shown in the footer.</p></div>
    <div class="grid2">
      <div class="frow"><label>Copyright line</label><input type="text" name="copyright" value="<?= e(setting('copyright')) ?>"></div>
      <div class="frow"><label>Timezone</label>
        <select name="timezone">
          <?php foreach (['Africa/Lagos', 'UTC', 'Europe/London', 'America/New_York', 'America/Toronto'] as $tz): ?>
            <option value="<?= e($tz) ?>" <?= setting('timezone', 'Africa/Lagos') === $tz ? 'selected' : '' ?>><?= e($tz) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <?php elseif ($tab === 'branding'): ?>
  <div class="acard">
    <div class="acard-head"><div><h2>Branding</h2><p class="hint">Upload new artwork in the media library, then pick it here. Defaults are the built-in brand files.</p></div></div>
    <div class="frow">
      <label for="s-logo">Logo (used in the header, on light backgrounds)</label>
      <div class="media-field">
        <span class="media-preview"></span>
        <input id="s-logo" type="text" name="logo" value="<?= e(setting('logo')) ?>">
        <button class="abtn abtn-ghost" type="button" data-browse>Browse…</button>
      </div>
    </div>
    <div class="frow">
      <label for="s-logow">Footer logo (white version, for dark backgrounds)</label>
      <div class="media-field">
        <span class="media-preview" style="background-color:#0B1B33"></span>
        <input id="s-logow" type="text" name="logo_white" value="<?= e(setting('logo_white')) ?>">
        <button class="abtn abtn-ghost" type="button" data-browse>Browse…</button>
      </div>
    </div>
    <div class="frow">
      <label for="s-favicon">Favicon (browser tab icon, square PNG or ICO)</label>
      <div class="media-field">
        <span class="media-preview"></span>
        <input id="s-favicon" type="text" name="favicon" value="<?= e(setting('favicon')) ?>">
        <button class="abtn abtn-ghost" type="button" data-browse>Browse…</button>
      </div>
    </div>
  </div>

  <?php elseif ($tab === 'contact'): ?>
  <div class="acard">
    <div class="acard-head"><div><h2>Contact details</h2><p class="hint">Shown on the contact page and in the footer.</p></div></div>
    <div class="grid2">
      <div class="frow"><label>Public email</label><input type="email" name="contact_email" value="<?= e(setting('contact_email')) ?>"></div>
      <div class="frow"><label>Phone</label><input type="text" name="contact_phone" value="<?= e(setting('contact_phone')) ?>"></div>
    </div>
    <div class="frow"><label>Address</label><input type="text" name="contact_address" value="<?= e(setting('contact_address')) ?>"></div>
    <div class="frow"><label>Office hours</label><input type="text" name="office_hours" value="<?= e(setting('office_hours')) ?>"></div>
  </div>
  <div class="acard">
    <div class="acard-head"><div><h2>Form notifications</h2><p class="hint">Email yourself whenever the contact form is submitted (uses your host’s PHP mail).</p></div></div>
    <div class="frow checkrow">
      <input type="checkbox" id="notify_on_contact" name="notify_on_contact" value="1" <?= setting('notify_on_contact') === '1' ? 'checked' : '' ?>>
      <label for="notify_on_contact">Send an email notification for each new message</label>
    </div>
    <div class="frow"><label>Notification recipient</label><input type="email" name="notify_email" value="<?= e(setting('notify_email')) ?>"></div>
  </div>

  <?php elseif ($tab === 'social'): ?>
  <div class="acard">
    <div class="acard-head"><div><h2>Social media</h2><p class="hint">Full URLs. Leave a field empty to hide its icon in the footer.</p></div></div>
    <div class="grid2">
      <div class="frow"><label>LinkedIn</label><input type="text" name="social_linkedin" value="<?= e(setting('social_linkedin')) ?>" placeholder="https://www.linkedin.com/company/…"></div>
      <div class="frow"><label>X (Twitter)</label><input type="text" name="social_x" value="<?= e(setting('social_x')) ?>" placeholder="https://x.com/…"></div>
      <div class="frow"><label>Facebook</label><input type="text" name="social_facebook" value="<?= e(setting('social_facebook')) ?>"></div>
      <div class="frow"><label>Instagram</label><input type="text" name="social_instagram" value="<?= e(setting('social_instagram')) ?>"></div>
      <div class="frow"><label>YouTube</label><input type="text" name="social_youtube" value="<?= e(setting('social_youtube')) ?>"></div>
    </div>
  </div>

  <?php elseif ($tab === 'seo'): ?>
  <div class="acard">
    <div class="acard-head"><div><h2>SEO</h2><p class="hint">Defaults for search engines; individual pages can override them.</p></div></div>
    <div class="frow"><label>Default meta title</label><input type="text" name="meta_title" value="<?= e(setting('meta_title')) ?>"></div>
    <div class="frow"><label>Default meta description</label><textarea name="meta_description" maxlength="300" style="min-height:70px"><?= e(setting('meta_description')) ?></textarea></div>
  </div>
  <div class="acard">
    <div class="acard-head"><div><h2>Analytics</h2><p class="hint">Paste a tracking snippet (e.g. Google Analytics). It is inserted into every public page’s &lt;head&gt;.</p></div></div>
    <div class="frow"><label>Tracking code</label><textarea name="analytics_code" style="min-height:120px;font-family:ui-monospace,Menlo,monospace;font-size:12.5px"><?= e(setting('analytics_code')) ?></textarea></div>
  </div>

  <?php elseif ($tab === 'advanced'): ?>
  <div class="acard">
    <div class="acard-head"><div><h2>Advanced</h2></div></div>
    <div class="frow checkrow">
      <input type="checkbox" id="maintenance_mode" name="maintenance_mode" value="1" <?= setting('maintenance_mode') === '1' ? 'checked' : '' ?>>
      <label for="maintenance_mode">Maintenance mode (visitors see a “be right back” page; you can still browse while signed in)</label>
    </div>
  </div>
  <div class="acard">
    <div class="acard-head"><div><h2>Housekeeping</h2></div></div>
    <table class="atable">
      <tr><td>Installer</td><td><?= is_file(ROOT_PATH . '/install.php') ? '<span class="pill pill-warn">install.php still on server — delete it via your hosting File Manager</span>' : '<span class="pill pill-ok">Removed</span>' ?></td></tr>
      <tr><td>HTTPS</td><td><?= is_https() ? '<span class="pill pill-ok">Active</span>' : '<span class="pill pill-warn">Not detected — enable SSL in cPanel, then force HTTPS in .htaccess</span>' ?></td></tr>
      <tr><td>PHP</td><td><?= e(PHP_VERSION) ?></td></tr>
      <tr><td>Database</td><td><?= e(DB::driver()) ?></td></tr>
    </table>
  </div>
  <?php endif; ?>

  <div class="form-foot">
    <button class="abtn" type="submit">Save settings</button>
  </div>
</form>
