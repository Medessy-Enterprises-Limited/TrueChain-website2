<?php /** @var array $stats @var array $recentMessages */ ?>
<div class="stat-cards">
  <div class="stat-card"><div class="num"><?= $stats['pages'] ?></div><div class="lbl">Pages</div><a href="<?= e(url('admin?r=pages')) ?>" aria-label="Pages"></a></div>
  <div class="stat-card"><div class="num"><?= $stats['companies'] ?></div><div class="lbl">Active companies</div><a href="<?= e(url('admin?r=companies')) ?>" aria-label="Companies"></a></div>
  <div class="stat-card"><div class="num"><?= $stats['sliders'] ?></div><div class="lbl">Active slides</div><a href="<?= e(url('admin?r=sliders')) ?>" aria-label="Slides"></a></div>
  <div class="stat-card"><div class="num"><?= $stats['media'] ?></div><div class="lbl">Media files</div><a href="<?= e(url('admin?r=media')) ?>" aria-label="Media"></a></div>
  <div class="stat-card"><div class="num"><?= $stats['unread'] ?></div><div class="lbl">Unread messages</div><a href="<?= e(url('admin?r=messages&f=unread')) ?>" aria-label="Messages"></a></div>
</div>

<div class="acard">
  <div class="acard-head">
    <div><h2>Latest messages</h2><p class="hint">Contact form submissions from the website.</p></div>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=messages')) ?>">View all</a>
  </div>
  <?php if (!$recentMessages): ?>
    <p style="color:var(--muted)">No messages yet. When visitors use the contact form, their messages appear here.</p>
  <?php else: ?>
    <table class="atable">
      <thead><tr><th>From</th><th>Subject</th><th>Received</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($recentMessages as $m): ?>
        <tr class="<?= $m['is_read'] ? '' : 'msg-row-unread' ?>">
          <td><span class="row-title"><?= e($m['name']) ?></span><br><span class="row-sub"><?= e($m['email']) ?></span></td>
          <td><?= e($m['subject']) ?></td>
          <td><span class="row-sub"><?= e(format_date($m['created_at'], 'j M Y, H:i')) ?></span></td>
          <td class="actions"><a class="abtn abtn-ghost abtn-sm" href="<?= e(url('admin?r=message-view&id=' . $m['id'])) ?>">Open</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="acard">
  <div class="acard-head"><div><h2>Quick actions</h2></div></div>
  <p style="display:flex;gap:10px;flex-wrap:wrap;margin:0">
    <a class="abtn" href="<?= e(url('admin?r=page-edit')) ?>">+ New page</a>
    <a class="abtn" href="<?= e(url('admin?r=slider-edit')) ?>">+ New slide</a>
    <a class="abtn" href="<?= e(url('admin?r=company-edit')) ?>">+ New company</a>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=settings&tab=branding')) ?>">Change logo / favicon</a>
    <a class="abtn abtn-ghost" href="<?= e(url('admin?r=media')) ?>">Upload images</a>
  </p>
</div>

<div class="acard">
  <div class="acard-head"><div><h2>System</h2></div></div>
  <table class="atable">
    <tr><td>PHP version</td><td><?= e(PHP_VERSION) ?></td></tr>
    <tr><td>Database</td><td><?= e(DB::driver() === 'mysql' ? 'MySQL' : 'SQLite') ?></td></tr>
    <tr><td>Maintenance mode</td><td><?= setting('maintenance_mode') === '1' ? '<span class="pill pill-warn">ON — visitors see a maintenance page</span>' : '<span class="pill pill-ok">Off</span>' ?></td></tr>
    <tr><td>Signed in as</td><td><?= e($adminUser['name']) ?> (<?= e($adminUser['email']) ?>)</td></tr>
  </table>
</div>
