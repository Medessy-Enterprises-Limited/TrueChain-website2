<?php /** @var array $pages */ ?>
<div class="acard">
  <div class="acard-head">
    <div><h2>All pages</h2><p class="hint">Create and edit website pages. System pages (About, Privacy, Terms) can be edited but not deleted.</p></div>
    <a class="abtn" href="<?= e(url('admin?r=page-edit')) ?>">+ New page</a>
  </div>
  <table class="atable">
    <thead><tr><th>Title</th><th>URL</th><th>In menu</th><th>Status</th><th>Updated</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($pages as $p): ?>
      <tr>
        <td>
          <span class="row-title"><?= e($p['title']) ?></span>
          <?php if ($p['is_system']): ?> <span class="pill pill-blue">system</span><?php endif; ?>
        </td>
        <td><a href="<?= e(url($p['slug'])) ?>" target="_blank" rel="noopener">/<?= e($p['slug']) ?></a></td>
        <td><?= $p['show_in_nav'] ? '<span class="pill pill-ok">Yes · ' . (int)$p['nav_order'] . '</span>' : '<span class="pill pill-off">No</span>' ?></td>
        <td><?= $p['status'] === 'published' ? '<span class="pill pill-ok">Published</span>' : '<span class="pill pill-warn">Draft</span>' ?></td>
        <td><span class="row-sub"><?= e(format_date($p['updated_at'])) ?></span></td>
        <td class="actions">
          <a class="abtn abtn-ghost abtn-sm" href="<?= e(url('admin?r=page-edit&id=' . $p['id'])) ?>">Edit</a>
          <?php if (!$p['is_system']): ?>
          <form method="post" action="<?= e(url('admin?r=page-delete')) ?>" data-confirm="Delete the page “<?= e($p['title']) ?>”? This cannot be undone.">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
            <button class="abtn abtn-danger abtn-sm" type="submit">Delete</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
