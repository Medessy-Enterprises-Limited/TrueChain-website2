<?php /** @var array $companies */ ?>
<div class="acard">
  <div class="acard-head">
    <div><h2>Group companies</h2><p class="hint">The companies shown on the home page and the “Our Companies” section. Set each company’s website link here, this is how the corporate site routes visitors to the right platform.</p></div>
    <a class="abtn" href="<?= e(url('admin?r=company-edit')) ?>">+ New company</a>
  </div>
  <table class="atable">
    <thead><tr><th>Company</th><th>Category</th><th>Website link</th><th>Order</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($companies as $c): ?>
      <tr>
        <td>
          <span class="row-title"><?= e($c['name']) ?></span><br>
          <span class="row-sub">/companies/<?= e($c['slug']) ?></span>
        </td>
        <td><span class="row-sub"><?= e($c['category']) ?></span></td>
        <td>
          <?php if ($c['website_url'] && $c['website_url'] !== '#'): ?>
            <a href="<?= e($c['website_url']) ?>" target="_blank" rel="noopener" class="row-sub"><?= e(mb_substr($c['website_url'], 0, 36)) ?></a>
            <?= $c['site_status'] === 'live' ? '<span class="pill pill-ok">live</span>' : '<span class="pill pill-warn">soon</span>' ?>
          <?php else: ?>
            <span class="pill pill-warn">not set</span>
          <?php endif; ?>
        </td>
        <td><?= (int)$c['sort_order'] ?></td>
        <td><?= $c['active'] ? '<span class="pill pill-ok">Visible</span>' : '<span class="pill pill-off">Hidden</span>' ?></td>
        <td class="actions">
          <a class="abtn abtn-ghost abtn-sm" href="<?= e(url('companies/' . $c['slug'])) ?>" target="_blank" rel="noopener">View</a>
          <a class="abtn abtn-ghost abtn-sm" href="<?= e(url('admin?r=company-edit&id=' . $c['id'])) ?>">Edit</a>
          <form method="post" action="<?= e(url('admin?r=company-delete')) ?>" data-confirm="Delete “<?= e($c['name']) ?>”? This cannot be undone.">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
            <button class="abtn abtn-danger abtn-sm" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
