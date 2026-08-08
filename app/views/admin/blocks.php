<?php /** @var array $blocks */ ?>
<div class="acard">
  <div class="acard-head">
    <div><h2>Static blocks</h2><p class="hint">Reusable pieces of content placed around the site (home page sections, footer text, contact intro). Edit the text freely; the identifier ties a block to its spot.</p></div>
    <a class="abtn" href="<?= e(url('admin?r=block-edit')) ?>">+ New block</a>
  </div>
  <table class="atable">
    <thead><tr><th>Identifier</th><th>Title</th><th>Used for</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($blocks as $b): ?>
      <tr>
        <td><code style="background:#EEF3FA;padding:3px 8px;border-radius:6px;font-size:12.5px"><?= e($b['identifier']) ?></code></td>
        <td><span class="row-title"><?= e($b['title'] ?: '—') ?></span></td>
        <td><span class="row-sub"><?= e($b['note'] ?: '') ?></span></td>
        <td><?= $b['active'] ? '<span class="pill pill-ok">Active</span>' : '<span class="pill pill-off">Hidden</span>' ?></td>
        <td class="actions">
          <a class="abtn abtn-ghost abtn-sm" href="<?= e(url('admin?r=block-edit&id=' . $b['id'])) ?>">Edit</a>
          <form method="post" action="<?= e(url('admin?r=block-delete')) ?>" data-confirm="Delete the block “<?= e($b['identifier']) ?>”? Anywhere it is used will show nothing.">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
            <button class="abtn abtn-danger abtn-sm" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
