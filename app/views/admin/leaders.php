<?php /** @var array $leaders */ ?>
<div class="acard">
  <div class="acard-head">
    <div><h2>Leadership profiles</h2><p class="hint">Shown on the Leadership page. Add a photo from the media library; without one, a monogram avatar is displayed.</p></div>
    <a class="abtn" href="<?= e(url('admin?r=leader-edit')) ?>">+ New profile</a>
  </div>
  <table class="atable">
    <thead><tr><th>Name</th><th>Title</th><th>Order</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($leaders as $l): ?>
      <tr>
        <td><span class="row-title"><?= e($l['name']) ?></span></td>
        <td><span class="row-sub"><?= e($l['title']) ?></span></td>
        <td><?= (int)$l['sort_order'] ?></td>
        <td><?= $l['active'] ? '<span class="pill pill-ok">Visible</span>' : '<span class="pill pill-off">Hidden</span>' ?></td>
        <td class="actions">
          <a class="abtn abtn-ghost abtn-sm" href="<?= e(url('admin?r=leader-edit&id=' . $l['id'])) ?>">Edit</a>
          <form method="post" action="<?= e(url('admin?r=leader-delete')) ?>" data-confirm="Delete the profile for <?= e($l['name']) ?>?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$l['id'] ?>">
            <button class="abtn abtn-danger abtn-sm" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
