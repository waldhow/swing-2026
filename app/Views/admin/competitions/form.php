<?php
use App\Models\CompetitionModel;

$c      = $competition;
$action = $id === null ? site_url('admin/tavlingar/ny/') : site_url('admin/tavlingar/' . $id . '/');
$local  = static fn (?string $v): string => $v ? str_replace(' ', 'T', substr($v, 0, 16)) : '';
?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head">
  <h1><?= esc($title) ?></h1>
  <a class="adm-link" href="<?= site_url('admin/tavlingar/') ?>">← Alla tävlingar</a>
</div>
<?= partial('admin/_errors', ['errors' => $errors]) ?>

<form class="adm-card adm-form" method="post" action="<?= $action ?>" novalidate>
  <?= csrf_field() ?>
  <p class="adm-help">Endast publicerade tävlingar visas på sajten. Utan datum visas ”Datum meddelas” — hitta aldrig på datum.</p>

  <div class="adm-row2">
    <?= partial('admin/_field', ['name' => 'title', 'label' => 'Titel', 'value' => $c['title'] ?? '', 'errors' => $errors, 'required' => true, 'attrs' => 'maxlength="160"']) ?>
    <?= partial('admin/_field', ['name' => 'slug', 'label' => 'Adress (slug)', 'value' => $c['slug'] ?? '', 'errors' => $errors, 'help' => 'Blir /tavlingar/<slug>/. Lämna tomt för att skapa från titeln.', 'attrs' => 'maxlength="160" pattern="[a-z0-9-]*"']) ?>
  </div>
  <div class="adm-row3">
    <?= partial('admin/_field', ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => CompetitionModel::STATUSES, 'value' => $c['status'] ?? 'draft', 'errors' => $errors, 'required' => true]) ?>
    <?= partial('admin/_field', ['name' => 'visibility', 'label' => 'Synlighet', 'type' => 'select', 'options' => CompetitionModel::VISIBILITIES, 'value' => $c['visibility'] ?? 'public', 'errors' => $errors, 'required' => true]) ?>
    <?= partial('admin/_field', ['name' => 'format', 'label' => 'Format', 'value' => $c['format'] ?? '', 'errors' => $errors, 'help' => 'T.ex. Slagspel, scratch · 18 hål', 'attrs' => 'maxlength="160"']) ?>
  </div>
  <div class="adm-row2">
    <?= partial('admin/_field', ['name' => 'starts_at', 'label' => 'Start', 'type' => 'datetime-local', 'value' => $local($c['starts_at'] ?? null), 'errors' => $errors, 'help' => 'Valfritt.']) ?>
    <?= partial('admin/_field', ['name' => 'ends_at', 'label' => 'Slut', 'type' => 'datetime-local', 'value' => $local($c['ends_at'] ?? null), 'errors' => $errors, 'help' => 'Valfritt — för tävlingar som pågår över flera dagar.']) ?>
  </div>
  <?= partial('admin/_field', ['name' => 'summary', 'label' => 'Sammanfattning', 'value' => $c['summary'] ?? '', 'errors' => $errors, 'help' => 'En mening som visas i tävlingslistan (högst 300 tecken).', 'attrs' => 'maxlength="300"']) ?>
  <?= partial('admin/_field', ['name' => 'description', 'label' => 'Beskrivning', 'type' => 'textarea', 'rows' => 7, 'value' => $c['description'] ?? '', 'errors' => $errors, 'help' => 'Vanlig text. Tom rad = nytt stycke.']) ?>

  <fieldset class="adm-fieldset">
    <legend>Anmälan och avgift</legend>
    <?= partial('admin/_field', ['name' => 'registration_info', 'label' => 'Anmälningsinformation', 'type' => 'textarea', 'rows' => 3, 'value' => $c['registration_info'] ?? '', 'errors' => $errors, 'help' => 'T.ex. sista anmälningsdag och vem som får delta.']) ?>
    <div class="adm-row2">
      <?= partial('admin/_field', ['name' => 'registration_url', 'label' => 'Anmälningslänk', 'type' => 'url', 'value' => $c['registration_url'] ?? '', 'errors' => $errors, 'help' => 'Endast verifierad länk som börjar med https://.', 'attrs' => 'maxlength="500"']) ?>
      <?= partial('admin/_field', ['name' => 'price_info', 'label' => 'Avgift', 'value' => $c['price_info'] ?? '', 'errors' => $errors, 'help' => 'T.ex. Anmälningsavgift 150 kr, simulatoravgift 200 kr.', 'attrs' => 'maxlength="300"']) ?>
    </div>
  </fieldset>

  <fieldset class="adm-fieldset">
    <legend>Villkor och resultat</legend>
    <?= partial('admin/_field', ['name' => 'terms', 'label' => 'Villkor', 'type' => 'textarea', 'rows' => 4, 'value' => $c['terms'] ?? '', 'errors' => $errors]) ?>
    <div class="adm-row2">
      <?= partial('admin/_field', ['name' => 'terms_url', 'label' => 'Länk till fullständiga villkor', 'type' => 'url', 'value' => $c['terms_url'] ?? '', 'errors' => $errors, 'attrs' => 'maxlength="500"']) ?>
      <?= partial('admin/_field', ['name' => 'results_url', 'label' => 'Resultatlänk', 'type' => 'url', 'value' => $c['results_url'] ?? '', 'errors' => $errors, 'attrs' => 'maxlength="500"']) ?>
    </div>
  </fieldset>

  <div class="adm-actions">
    <button type="submit" class="adm-btn adm-btn-primary">Spara</button>
    <a class="adm-btn adm-btn-ghost" href="<?= site_url('admin/tavlingar/') ?>">Avbryt</a>
  </div>
</form>

<?php if ($id !== null): ?>
<form class="adm-danger" method="post" action="<?= site_url('admin/tavlingar/' . $id . '/radera/') ?>" onsubmit="return confirm('Radera tävlingen? Det går inte att ångra.');">
  <?= csrf_field() ?>
  <button type="submit" class="adm-btn adm-btn-danger">Radera tävlingen</button>
</form>
<?php endif; ?>
<?= $this->endSection() ?>
