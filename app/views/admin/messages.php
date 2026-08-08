<?php /** @var array $messages */ $f = $_GET['f'] ?? ''; ?>
<div class="acard">
  <div class="acard-head">
    <div><h2>Contact messages</h2><p class="hint">Messages submitted through the website contact form.</p></div>
    <div class="tabs" style="margin:0">
      <a href="<?= e(url('admin?r=messages')) ?>" class="<?= $f === '' ? 'active' : '' ?>">All</a>
      <a href="<?= e(url('admin?r=messages&f=unread')) ?>" class="<?= $f === 'unread' ? 'active' : '' ?>">Unread</a>
    </div>
  </div>
  <?php if (!$messages): ?>
    <p style="color:var(--muted)">No messages<?= $f === 'unread' ? ' awaiting reading' : ' yet' ?>.</p>
  <?php else: ?>
  <table class="atable">
    <thead><tr><th>From</th><th>Subject</th><th>Message</th><th>Received</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($messages as $m): ?>
      <tr class="<?= $m['is_read'] ? '' : 'msg-row-unread' ?>">
        <td><span class="row-title"><?= e($m['name']) ?></span><br><span class="row-sub"><?= e($m['email']) ?></span></td>
        <td><?= e($m['subject']) ?></td>
        <td><span class="row-sub"><?= e(mb_substr($m['message'], 0, 60)) ?><?= mb_strlen($m['message']) > 60 ? '…' : '' ?></span></td>
        <td><span class="row-sub"><?= e(format_date($m['created_at'], 'j M Y, H:i')) ?></span></td>
        <td class="actions">
          <a class="abtn abtn-ghost abtn-sm" href="<?= e(url('admin?r=message-view&id=' . $m['id'])) ?>">Open</a>
          <form method="post" action="<?= e(url('admin?r=message-delete')) ?>" data-confirm="Delete this message?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
            <button class="abtn abtn-danger abtn-sm" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
