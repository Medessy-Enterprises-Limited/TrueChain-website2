<?php /** @var ?array $page */
$p = $page ?? null;
$isSystem = $p && (int)$p['is_system'] === 1;
?>
<form class="aform" method="post" action="<?= e(url('admin?r=page-save')) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int)($p['id'] ?? 0) ?>">

  <div class="acard">
    <div class="acard-head"><div><h2><?= $p ? 'Edit page' : 'New page' ?></h2></div>
      <?php if ($p): ?><a class="abtn abtn-ghost" href="<?= e(url($p['slug'])) ?>" target="_blank" rel="noopener">View page <?= tc_icon('external') ?></a><?php endif; ?>
    </div>
    <div class="grid2">
      <div class="frow">
        <label for="f-title">Title *</label>
        <input id="f-title" type="text" name="title" required maxlength="190" value="<?= e($p['title'] ?? '') ?>" data-slug-source>
      </div>
      <div class="frow">
        <label for="f-slug">URL slug</label>
        <input id="f-slug" type="text" name="slug" maxlength="190" value="<?= e($p['slug'] ?? '') ?>" <?= $isSystem ? 'readonly' : '' ?> data-slug-target>
        <p class="help"><?= $isSystem ? 'System page: the address is fixed so menus and links keep working.' : 'The page address, e.g. “our-story” → /our-story' ?></p>
      </div>
    </div>

    <div class="frow">
      <label>Content</label>
      <div class="editor-wrap">
        <div class="editor-tabs">
          <button type="button" data-tab="visual">Visual</button>
          <button type="button" data-tab="html">HTML</button>
        </div>
        <div class="editor-visual"></div>
        <div class="editor-html"><textarea name="content"><?= e($p['content'] ?? '') ?></textarea></div>
        <div class="editor-note">This page contains designed layout markup. Edit in the HTML tab to keep its styling; the Visual tab may simplify the design.</div>
      </div>
    </div>
  </div>

  <div class="acard">
    <div class="acard-head"><div><h2>Menu and visibility</h2></div></div>
    <div class="grid3">
      <div class="frow checkrow" style="margin-top:30px">
        <input type="checkbox" id="f-nav" name="show_in_nav" value="1" <?= !empty($p['show_in_nav']) ? 'checked' : '' ?>>
        <label for="f-nav">Show in main menu</label>
      </div>
      <div class="frow">
        <label for="f-navlabel">Menu label</label>
        <input id="f-navlabel" type="text" name="nav_label" maxlength="100" value="<?= e($p['nav_label'] ?? '') ?>" placeholder="Defaults to the title">
      </div>
      <div class="frow">
        <label for="f-navorder">Menu position</label>
        <input id="f-navorder" type="number" name="nav_order" value="<?= (int)($p['nav_order'] ?? 50) ?>">
        <p class="help">Lower numbers appear first. Built-ins: Companies = 20, Leadership = 30.</p>
      </div>
    </div>
    <div class="frow">
      <label for="f-status">Status</label>
      <select id="f-status" name="status">
        <option value="published" <?= ($p['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
        <option value="draft" <?= ($p['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft (hidden from visitors)</option>
      </select>
    </div>
  </div>

  <div class="acard">
    <div class="acard-head"><div><h2>Search engine settings</h2><p class="hint">Optional. Used by Google and social networks.</p></div></div>
    <div class="frow">
      <label for="f-mtitle">Meta title</label>
      <input id="f-mtitle" type="text" name="meta_title" maxlength="190" value="<?= e($p['meta_title'] ?? '') ?>">
    </div>
    <div class="frow">
      <label for="f-mdesc">Meta description</label>
      <textarea id="f-mdesc" name="meta_description" maxlength="300" style="min-height:70px"><?= e($p['meta_description'] ?? '') ?></textarea>
    </div>
  </div>

  <div class="form-foot">
    <button class="abtn" type="submit">Save page</button>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=pages')) ?>">Back to pages</a>
  </div>
</form>
