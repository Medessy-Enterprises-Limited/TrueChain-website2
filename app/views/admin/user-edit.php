<?php /** @var ?array $editUser */ $u = $editUser ?? null; ?>
<form class="aform" method="post" action="<?= e(url('admin?r=user-save')) ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= (int)($u['id'] ?? 0) ?>">

  <div class="acard">
    <div class="acard-head"><div><h2><?= $u ? 'Edit administrator' : 'New administrator' ?></h2></div></div>
    <div class="grid2">
      <div class="frow">
        <label for="f-name">Name *</label>
        <input id="f-name" type="text" name="name" required maxlength="100" value="<?= e($u['name'] ?? '') ?>">
      </div>
      <div class="frow">
        <label for="f-email">Email (sign-in) *</label>
        <input id="f-email" type="email" name="email" required maxlength="190" value="<?= e($u['email'] ?? '') ?>">
      </div>
      <div class="frow">
        <label for="f-pass"><?= $u ? 'New password (leave blank to keep current)' : 'Password *' ?></label>
        <input id="f-pass" type="password" name="password" minlength="10" autocomplete="new-password" <?= $u ? '' : 'required' ?>>
        <p class="help">At least 10 characters. Use a password manager if you can.</p>
      </div>
      <div class="frow checkrow" style="margin-top:30px">
        <input type="checkbox" id="f-active" name="active" value="1" <?= !$u || $u['active'] ? 'checked' : '' ?> <?= $u && (int)$u['id'] === Auth::id() ? 'onclick="return false" title="You cannot disable your own account"' : '' ?>>
        <label for="f-active">Account is active</label>
      </div>
    </div>
  </div>

  <div class="form-foot">
    <button class="abtn" type="submit">Save administrator</button>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=users')) ?>">Back to administrators</a>
  </div>
</form>
