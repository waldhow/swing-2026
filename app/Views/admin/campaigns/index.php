<?php use App\Models\CampaignModel; ?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head">
  <h1>Kampanjer</h1>
  <a class="adm-btn adm-btn-primary" href="<?= site_url('admin/kampanjer/ny/') ?>">Ny kampanj</a>
</div>
<p class="adm-muted">En publicerad kampanj syns bara under sin giltighetsperiod. Kampanjer med pris och spelvärde kan beställas som spelpott på /spelpotter/.</p>

<?php if ($campaigns === []): ?>
<div class="adm-card adm-empty">Inga kampanjer ännu. <a href="<?= site_url('admin/kampanjer/ny/') ?>">Skapa den första.</a></div>
<?php else: ?>
<div class="adm-tablewrap">
<table class="adm-table">
  <thead>
    <tr><th scope="col">Titel</th><th scope="col">Pott</th><th scope="col">Giltig</th><th scope="col">Status</th><th scope="col">På sajten</th></tr>
  </thead>
  <tbody>
    <?php foreach ($campaigns as $c): ?>
    <tr>
      <td><a href="<?= site_url('admin/kampanjer/' . $c['id'] . '/') ?>"><?= esc($c['title']) ?></a><?= $c['show_on_home'] ? '<div class="adm-muted adm-small">Visas på startsidan</div>' : '' ?></td>
      <td><?= $c['pay_amount'] ? esc(kr((int) $c['pay_amount'])) . ' → ' . esc(kr((int) $c['value_amount'])) : '<span class="adm-muted">—</span>' ?></td>
      <td><?= esc($c['valid_from'] ?? '…') ?> – <?= esc($c['valid_until'] ?? '…') ?></td>
      <td><span class="adm-tag adm-tag-<?= esc($c['status'], 'attr') ?>"><?= esc(CampaignModel::STATUSES[$c['status']] ?? $c['status']) ?></span></td>
      <td><?= in_array($c['id'], $activeIds, false) ? '<span class="adm-tag adm-tag-ok">Aktiv nu</span>' : '<span class="adm-muted">Visas inte</span>' ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
