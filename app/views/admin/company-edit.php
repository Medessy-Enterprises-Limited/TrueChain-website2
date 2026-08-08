<?php /** @var ?array $company */
$c = $company ?? null;
$icons = ['registry' => 'ID / Registry', 'soc' => 'Shield / SOC', 'cln' => 'Network / CLN', 'institute' => 'Graduation / Institute', 'park' => 'Facility / Park', 'truck' => 'Truck / Fleet', 'group' => 'Group / Default'];
?>
<form class="aform" method="post" action="<?= e(url('admin?r=company-save')) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int)($c['id'] ?? 0) ?>">

  <div class="acard">
    <div class="acard-head"><div><h2><?= $c ? 'Edit company' : 'New company' ?></h2></div>
      <?php if ($c): ?><a class="abtn abtn-ghost" href="<?= e(url('companies/' . $c['slug'])) ?>" target="_blank" rel="noopener">View page <?= tc_icon('external') ?></a><?php endif; ?>
    </div>
    <div class="grid2">
      <div class="frow">
        <label for="f-name">Company name *</label>
        <input id="f-name" type="text" name="name" required maxlength="190" value="<?= e($c['name'] ?? '') ?>" data-slug-source>
      </div>
      <div class="frow">
        <label for="f-short">Short name</label>
        <input id="f-short" type="text" name="short_name" maxlength="60" value="<?= e($c['short_name'] ?? '') ?>" placeholder="e.g. Registry">
      </div>
      <div class="frow">
        <label for="f-slug">URL slug</label>
        <input id="f-slug" type="text" name="slug" maxlength="190" value="<?= e($c['slug'] ?? '') ?>" data-slug-target>
      </div>
      <div class="frow">
        <label for="f-cat">Category label</label>
        <input id="f-cat" type="text" name="category" maxlength="60" value="<?= e($c['category'] ?? '') ?>" placeholder="e.g. True Chain Technologies">
      </div>
    </div>
    <div class="frow">
      <label for="f-tagline">Tagline</label>
      <input id="f-tagline" type="text" name="tagline" maxlength="300" value="<?= e($c['tagline'] ?? '') ?>">
      <p class="help">One sentence shown on the home page card.</p>
    </div>
    <div class="frow">
      <label for="f-summary">Summary</label>
      <textarea id="f-summary" name="summary" style="min-height:80px"><?= e($c['summary'] ?? '') ?></textarea>
      <p class="help">Two to three sentences shown on the “Our Companies” listing.</p>
    </div>
    <div class="frow">
      <label>Full description (company page)</label>
      <div class="editor-wrap">
        <div class="editor-tabs">
          <button type="button" data-tab="visual">Visual</button>
          <button type="button" data-tab="html">HTML</button>
        </div>
        <div class="editor-visual"></div>
        <div class="editor-html"><textarea name="content"><?= e($c['content'] ?? '') ?></textarea></div>
        <div class="editor-note">This content contains designed layout markup. Edit in the HTML tab to keep its styling.</div>
      </div>
    </div>
  </div>

  <div class="acard">
    <div class="acard-head"><div><h2>Link and appearance</h2></div></div>
    <div class="grid2">
      <div class="frow">
        <label for="f-url">Website URL</label>
        <input id="f-url" type="text" name="website_url" maxlength="255" value="<?= e($c['website_url'] ?? '') ?>" placeholder="https://truechainregistry.com (or # while not live)">
        <p class="help">Where the “Open website” button sends visitors.</p>
      </div>
      <div class="frow">
        <label for="f-status">Platform status</label>
        <select id="f-status" name="site_status">
          <option value="coming-soon" <?= ($c['site_status'] ?? '') !== 'live' ? 'selected' : '' ?>>Coming soon</option>
          <option value="live" <?= ($c['site_status'] ?? '') === 'live' ? 'selected' : '' ?>>Live</option>
        </select>
      </div>
      <div class="frow">
        <label for="f-icon">Icon</label>
        <select id="f-icon" name="icon">
          <?php foreach ($icons as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= ($c['icon'] ?? 'group') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="frow">
        <label for="f-image">Feature image (optional)</label>
        <div class="media-field">
          <span class="media-preview"></span>
          <input id="f-image" type="text" name="image" value="<?= e($c['image'] ?? '') ?>">
          <button class="abtn abtn-ghost" type="button" data-browse>Browse…</button>
        </div>
      </div>
      <div class="frow">
        <label for="f-order">Display order</label>
        <input id="f-order" type="number" name="sort_order" value="<?= (int)($c['sort_order'] ?? 10) ?>">
      </div>
      <div class="frow checkrow" style="margin-top:30px">
        <input type="checkbox" id="f-active" name="active" value="1" <?= !isset($c['active']) || $c['active'] ? 'checked' : '' ?>>
        <label for="f-active">Visible on the website</label>
      </div>
    </div>
  </div>

  <div class="form-foot">
    <button class="abtn" type="submit">Save company</button>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=companies')) ?>">Back to companies</a>
  </div>
</form>
