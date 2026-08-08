<?php /** @var ?array $leader */ $l = $leader ?? null; ?>
<form class="aform" method="post" action="<?= e(url('admin?r=leader-save')) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int)($l['id'] ?? 0) ?>">

  <div class="acard">
    <div class="acard-head"><div><h2><?= $l ? 'Edit profile' : 'New profile' ?></h2></div></div>
    <div class="grid2">
      <div class="frow">
        <label for="f-name">Full name *</label>
        <input id="f-name" type="text" name="name" required maxlength="190" value="<?= e($l['name'] ?? '') ?>">
      </div>
      <div class="frow">
        <label for="f-jtitle">Title / role</label>
        <input id="f-jtitle" type="text" name="job_title" maxlength="190" value="<?= e($l['title'] ?? '') ?>" placeholder="e.g. Founder and Group Chief Executive Officer">
      </div>
    </div>
    <div class="frow">
      <label>Biography</label>
      <div class="editor-wrap">
        <div class="editor-tabs">
          <button type="button" data-tab="visual">Visual</button>
          <button type="button" data-tab="html">HTML</button>
        </div>
        <div class="editor-visual"></div>
        <div class="editor-html"><textarea name="bio"><?= e($l['bio'] ?? '') ?></textarea></div>
        <div class="editor-note">This biography contains layout markup; the HTML tab preserves it exactly.</div>
      </div>
    </div>
    <div class="grid2">
      <div class="frow">
        <label for="f-photo">Photo</label>
        <div class="media-field">
          <span class="media-preview"></span>
          <input id="f-photo" type="text" name="photo" value="<?= e($l['photo'] ?? '') ?>">
          <button class="abtn abtn-ghost" type="button" data-browse>Browse…</button>
        </div>
        <p class="help">Square images look best (e.g. 800×800px).</p>
      </div>
      <div class="frow">
        <label for="f-linkedin">LinkedIn URL</label>
        <input id="f-linkedin" type="text" name="linkedin" maxlength="255" value="<?= e($l['linkedin'] ?? '') ?>" placeholder="https://www.linkedin.com/in/…">
      </div>
      <div class="frow">
        <label for="f-email">Public email (optional)</label>
        <input id="f-email" type="email" name="email" maxlength="190" value="<?= e($l['email'] ?? '') ?>">
      </div>
      <div class="frow">
        <label for="f-order">Display order</label>
        <input id="f-order" type="number" name="sort_order" value="<?= (int)($l['sort_order'] ?? 10) ?>">
      </div>
    </div>
    <div class="frow checkrow">
      <input type="checkbox" id="f-active" name="active" value="1" <?= !isset($l['active']) || $l['active'] ? 'checked' : '' ?>>
      <label for="f-active">Visible on the website</label>
    </div>
  </div>

  <div class="form-foot">
    <button class="abtn" type="submit">Save profile</button>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=leaders')) ?>">Back to leadership</a>
  </div>
</form>
