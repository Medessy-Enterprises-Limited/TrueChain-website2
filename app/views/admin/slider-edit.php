<?php /** @var ?array $slider */ $s = $slider ?? null; ?>
<form class="aform" method="post" action="<?= e(url('admin?r=slider-save')) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int)($s['id'] ?? 0) ?>">

  <div class="acard">
    <div class="acard-head"><div><h2><?= $s ? 'Edit slide' : 'New slide' ?></h2></div></div>

    <div class="frow">
      <label for="f-title">Headline *</label>
      <input id="f-title" type="text" name="title" required maxlength="190" value="<?= e($s['title'] ?? '') ?>">
    </div>
    <div class="frow">
      <label for="f-subtitle">Supporting text</label>
      <textarea id="f-subtitle" name="subtitle" maxlength="400" style="min-height:80px"><?= e($s['subtitle'] ?? '') ?></textarea>
    </div>
    <div class="frow">
      <label for="f-image">Background image</label>
      <div class="media-field">
        <span class="media-preview"></span>
        <input id="f-image" type="text" name="image" value="<?= e($s['image'] ?? '') ?>" placeholder="Pick from the media library or keep a built-in artwork">
        <button class="abtn abtn-ghost" type="button" data-browse>Browse…</button>
      </div>
      <p class="help">Recommended: a wide image around 1920×900px. Built-in artworks: assets/img/hero-1.svg, hero-2.svg, hero-3.svg.</p>
    </div>

    <div class="grid2">
      <div class="frow">
        <label for="f-cta">Primary button text</label>
        <input id="f-cta" type="text" name="cta_text" maxlength="100" value="<?= e($s['cta_text'] ?? '') ?>" placeholder="e.g. Explore the group">
      </div>
      <div class="frow">
        <label for="f-ctau">Primary button link</label>
        <input id="f-ctau" type="text" name="cta_url" maxlength="255" value="<?= e($s['cta_url'] ?? '') ?>" placeholder="e.g. companies  or  https://…">
      </div>
      <div class="frow">
        <label for="f-cta2">Secondary button text</label>
        <input id="f-cta2" type="text" name="cta2_text" maxlength="100" value="<?= e($s['cta2_text'] ?? '') ?>">
      </div>
      <div class="frow">
        <label for="f-cta2u">Secondary button link</label>
        <input id="f-cta2u" type="text" name="cta2_url" maxlength="255" value="<?= e($s['cta2_url'] ?? '') ?>">
      </div>
    </div>

    <div class="grid2">
      <div class="frow">
        <label for="f-order">Position</label>
        <input id="f-order" type="number" name="sort_order" value="<?= (int)($s['sort_order'] ?? 10) ?>">
        <p class="help">Lower numbers show first.</p>
      </div>
      <div class="frow checkrow" style="margin-top:30px">
        <input type="checkbox" id="f-active" name="active" value="1" <?= !isset($s['active']) || $s['active'] ? 'checked' : '' ?>>
        <label for="f-active">Slide is active</label>
      </div>
    </div>
  </div>

  <div class="form-foot">
    <button class="abtn" type="submit">Save slide</button>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=sliders')) ?>">Back to slider</a>
  </div>
</form>
