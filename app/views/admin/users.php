<?php /** @var array $users */ ?>
<div class="acard">
  <div class="acard-head">
    <div><h2>Administrators</h2><p class="hint">People who can sign in to this panel. Keep this list short.</p></div>
    <a class="abtn" href="<?= e(url('admin?r=user-edit')) ?>">+ New administrator</a>
  </div>
  <table class="atable">
    <thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Last sign-in</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><span class="row-title"><?= e($u['name']) ?></span><?= (int)$u['id'] === Auth::id() ? ' <span class="pill pill-blue">you</span>' : '' ?></td>
        <td><?= e($u['email']) ?></td>
        <td><?= $u['active'] ? '<span class="pill pill-ok">Active</span>' : '<span class="pill pill-off">Disabled</span>' ?></td>
        <td><span class="row-sub"><?= $u['last_login'] ? e(format_date($u['last_login'], 'j M Y, H:i')) : 'Never' ?></span></td>
        <td class="actions">
          <a class="abtn abtn-ghost abtn-sm" href="<?= e(url('admin?r=user-edit&id=' . $u['id'])) ?>">Edit</a>
          <?php if ((int)$u['id'] !== Auth::id()): ?>
          <form method="post" action="<?= e(url('admin?r=user-delete')) ?>" data-confirm="Delete <?= e($u['name']) ?>’s admin account?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
            <button class="abtn abtn-danger abtn-sm" type="submit">Delete</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
