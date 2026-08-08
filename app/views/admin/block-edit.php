<?php /** @var ?array $blockRow */ $b = $blockRow ?? null; ?>
<form class="aform" method="post" action="<?= e(url('admin?r=block-save')) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int)($b['id'] ?? 0) ?>">

  <div class="acard">
    <div class="acard-head"><div><h2><?= $b ? 'Edit block' : 'New block' ?></h2></div></div>
    <div class="grid2">
      <div class="frow">
        <label for="f-ident">Identifier *</label>
        <input id="f-ident" type="text" name="identifier" required maxlength="100" value="<?= e($b['identifier'] ?? '') ?>" placeholder="e.g. home-intro">
        <p class="help">Lowercase letters, numbers and dashes. This is how the site finds the block, change with care.</p>
      </div>
      <div class="frow">
        <label for="f-title">Title</label>
        <input id="f-title" type="text" name="title" maxlength="190" value="<?= e($b['title'] ?? '') ?>">
        <p class="help">Some sections display this as their heading.</p>
      </div>
    </div>
    <div class="frow">
      <label for="f-note">Internal note</label>
      <input id="f-note" type="text" name="note" maxlength="255" value="<?= e($b['note'] ?? '') ?>" placeholder="Where is this block used?">
    </div>
    <div class="frow">
      <label>Content</label>
      <div class="editor-wrap">
        <div class="editor-tabs">
          <button type="button" data-tab="visual">Visual</button>
          <button type="button" data-tab="html">HTML</button>
        </div>
        <div class="editor-visual"></div>
        <div class="editor-html"><textarea name="content"><?= e($b['content'] ?? '') ?></textarea></div>
        <div class="editor-note">This block contains designed layout markup. Edit in the HTML tab to keep its styling.</div>
      </div>
    </div>
    <div class="frow checkrow">
      <input type="checkbox" id="f-active" name="active" value="1" <?= !isset($b['active']) || $b['active'] ? 'checked' : '' ?>>
      <label for="f-active">Block is active</label>
    </div>
  </div>

  <div class="form-foot">
    <button class="abtn" type="submit">Save block</button>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=blocks')) ?>">Back to blocks</a>
  </div>
</form>
