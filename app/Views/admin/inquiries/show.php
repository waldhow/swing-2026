<?php
use App\Models\InquiryModel;

$i      = $inquiry;
$typeLabel = InquiryModel::TYPES[$i['type']] ?? $i['type'];
$rows = [
    'Typ'                => $typeLabel,
    'Namn'               => $i['name'],
    'Organisation'       => $i['organisation'],
    'Telefon'            => $i['phone'],
    'Antal personer'     => $i['participants'],
    'Önskad dag och tid' => $i['preferred_date'],
    'Paketintresse'      => $i['package_interest'],
    'Mat och dryck'      => $i['wants_food'] ? 'Ja, vill ha förslag' : null,
];
$subject = rawurlencode('Sv: ' . $typeLabel . ' hos Swing Golf & Lounge');
?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head">
  <h1><?= esc($typeLabel) ?> från <?= esc($i['name']) ?></h1>
  <a class="adm-link" href="<?= site_url('admin/forfragningar/') ?>">← Alla förfrågningar</a>
</div>

<?php if (! empty($i['mail_error'])): ?>
<div class="adm-flash adm-flash-warn" role="status">Förfrågan kunde inte mejlas till Swing: <?= esc($i['mail_error']) ?></div>
<?php endif; ?>

<div class="adm-grid2">
  <section class="adm-card">
    <dl class="adm-dl">
      <dt>Inkom</dt><dd><?= esc(date('Y-m-d H:i', strtotime((string) $i['created_at']))) ?></dd>
      <dt>E-post</dt><dd><a href="mailto:<?= esc($i['email'], 'attr') ?>?subject=<?= $subject ?>"><?= esc($i['email']) ?></a></dd>
      <?php foreach ($rows as $label => $value): ?>
      <?php if ($value !== null && $value !== ''): ?>
      <dt><?= esc($label) ?></dt><dd><?= $label === 'Telefon' ? '<a href="tel:' . esc(preg_replace('/[^\d+]/', '', (string) $value), 'attr') . '">' . esc((string) $value) . '</a>' : esc((string) $value) ?></dd>
      <?php endif; ?>
      <?php endforeach; ?>
      <dt>Status</dt><dd><span class="adm-tag adm-tag-<?= $i['status'] === 'new' ? 'new' : 'ok' ?>"><?= esc(InquiryModel::STATUSES[$i['status']] ?? $i['status']) ?></span></dd>
    </dl>
  </section>
  <section class="adm-card">
    <h2>Meddelande</h2>
    <div class="adm-message"><?= nl2br(esc($i['message'])) ?></div>
  </section>
</div>

<div class="adm-actions">
  <a class="adm-btn adm-btn-primary" href="mailto:<?= esc($i['email'], 'attr') ?>?subject=<?= $subject ?>">Svara via e-post</a>
  <form method="post" action="<?= site_url('admin/forfragningar/' . $i['id'] . '/status/') ?>">
    <?= csrf_field() ?>
    <button type="submit" class="adm-btn adm-btn-ghost"><?= $i['status'] === 'new' ? 'Markera som hanterad' : 'Markera som ny' ?></button>
  </form>
  <form method="post" action="<?= site_url('admin/forfragningar/' . $i['id'] . '/radera/') ?>" onsubmit="return confirm('Radera förfrågan? Det går inte att ångra.');">
    <?= csrf_field() ?>
    <button type="submit" class="adm-btn adm-btn-danger">Radera</button>
  </form>
</div>
<?= $this->endSection() ?>
