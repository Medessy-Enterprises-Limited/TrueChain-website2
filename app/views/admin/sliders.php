<?php /** @var array $sliders */ ?>
<div class="acard">
  <div class="acard-head">
    <div><h2>Hero slider</h2><p class="hint">The rotating banner at the top of the home page. Drag-free ordering: lower position numbers show first.</p></div>
    <a class="abtn" href="<?= e(url('admin?r=slider-edit')) ?>">+ New slide</a>
  </div>
  <table class="atable">
    <thead><tr><th>Preview</th><th>Headline</th><th>Buttons</th><th>Position</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($sliders as $s): ?>
      <tr>
        <td><div class="media-preview" style="width:84px;height:48px;background-image:url('<?= e(upload_url($s['image'])) ?>');background-size:cover"></div></td>
        <td>
          <span class="row-title"><?= e($s['title']) ?></span><br>
          <span class="row-sub"><?= e(mb_substr((string)$s['subtitle'], 0, 70)) ?><?= mb_strlen((string)$s['subtitle']) > 70 ? '…' : '' ?></span>
        </td>
        <td><span class="row-sub"><?= e($s['cta_text'] ?: '—') ?><?= $s['cta2_text'] ? ' · ' . e($s['cta2_text']) : '' ?></span></td>
        <td><?= (int)$s['sort_order'] ?></td>
        <td><?= $s['active'] ? '<span class="pill pill-ok">Active</span>' : '<span class="pill pill-off">Hidden</span>' ?></td>
        <td class="actions">
          <a class="abtn abtn-ghost abtn-sm" href="<?= e(url('admin?r=slider-edit&id=' . $s['id'])) ?>">Edit</a>
          <form method="post" action="<?= e(url('admin?r=slider-delete')) ?>" data-confirm="Delete this slide?">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
            <button class="abtn abtn-danger abtn-sm" type="submit">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
