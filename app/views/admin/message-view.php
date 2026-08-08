<?php /** @var array $msg */ ?>
<div class="acard">
  <div class="acard-head">
    <div><h2><?= e($msg['subject'] ?: 'Message') ?></h2><p class="hint">Received <?= e(format_date($msg['created_at'], 'l j F Y \a\t H:i')) ?></p></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <a class="abtn" href="mailto:<?= e($msg['email']) ?>?subject=Re: <?= e(rawurlencode($msg['subject'] ?: 'Your enquiry')) ?>">Reply by email</a>
      <form method="post" action="<?= e(url('admin?r=message-unread')) ?>">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
        <button class="abtn abtn-ghost" type="submit">Mark unread</button>
      </form>
      <form method="post" action="<?= e(url('admin?r=message-delete')) ?>" data-confirm="Delete this message?">
        <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$msg['id'] ?>">
        <button class="abtn abtn-danger" type="submit">Delete</button>
      </form>
    </div>
  </div>

  <div class="msg-meta">
    <div class="item"><strong>Name</strong><?= e($msg['name']) ?></div>
    <div class="item"><strong>Email</strong><a href="mailto:<?= e($msg['email']) ?>"><?= e($msg['email']) ?></a></div>
    <?php if ($msg['phone']): ?><div class="item"><strong>Phone</strong><?= e($msg['phone']) ?></div><?php endif; ?>
    <?php if ($msg['company']): ?><div class="item"><strong>Organisation</strong><?= e($msg['company']) ?></div><?php endif; ?>
    <div class="item"><strong>IP address</strong><?= e($msg['ip']) ?></div>
  </div>

  <div class="msg-body"><?= e($msg['message']) ?></div>

  <p style="margin:20px 0 0"><a class="abtn abtn-ghost" href="<?= e(url('admin?r=messages')) ?>">← Back to messages</a></p>
</div>
