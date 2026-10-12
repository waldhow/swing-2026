<?php use App\Models\CompetitionModel; ?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head">
  <h1>Tävlingar</h1>
  <a class="adm-btn adm-btn-primary" href="<?= site_url('admin/tavlingar/ny/') ?>">Ny tävling</a>
</div>
<p class="adm-muted">Endast publicerade tävlingar visas på sajten. Utan datum visas ”Datum meddelas”.</p>

<nav class="adm-filters" aria-label="Filtrera på status">
  <a href="<?= site_url('admin/tavlingar/') ?>"<?= $status === '' ? ' aria-current="true"' : '' ?>>Alla</a>
  <?php foreach (CompetitionModel::STATUSES as $key => $label): ?>
  <a href="<?= site_url('admin/tavlingar/') ?>?status=<?= esc($key, 'url') ?>"<?= $status === $key ? ' aria-current="true"' : '' ?>><?= esc($label) ?></a>
  <?php endforeach; ?>
</nav>

<?php if ($competitions === []): ?>
<div class="adm-card adm-empty">Inga tävlingar ännu. <a href="<?= site_url('admin/tavlingar/ny/') ?>">Skapa den första.</a></div>
<?php else: ?>
<div class="adm-tablewrap">
<table class="adm-table">
  <thead>
    <tr><th scope="col">Titel</th><th scope="col">Datum</th><th scope="col">Synlighet</th><th scope="col">Status</th><th scope="col">På sajten</th></tr>
  </thead>
  <tbody>
    <?php foreach ($competitions as $c): ?>
    <tr>
      <td><a href="<?= site_url('admin/tavlingar/' . $c['id'] . '/') ?>"><?= esc($c['title']) ?></a><div class="adm-muted adm-small">/tavlingar/<?= esc($c['slug']) ?>/</div></td>
      <td><?= $c['starts_at'] ? esc(date('Y-m-d H:i', strtotime($c['starts_at']))) : '<span class="adm-muted">Datum meddelas</span>' ?></td>
      <td><?= esc(CompetitionModel::VISIBILITIES[$c['visibility']] ?? $c['visibility']) ?></td>
      <td><span class="adm-tag adm-tag-<?= esc($c['status'], 'attr') ?>"><?= esc(CompetitionModel::STATUSES[$c['status']] ?? $c['status']) ?></span></td>
      <td><?= $c['status'] === 'published' ? esc(CompetitionModel::timeState($c)) : '<span class="adm-muted">Visas inte</span>' ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?php endif; ?>
<?= $this->endSection() ?>
