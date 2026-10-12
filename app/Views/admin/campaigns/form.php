<?php
use App\Models\CampaignModel;

$c      = $campaign;
$action = $id === null ? site_url('admin/kampanjer/ny/') : site_url('admin/kampanjer/' . $id . '/');
?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head">
  <h1><?= esc($title) ?></h1>
  <a class="adm-link" href="<?= site_url('admin/kampanjer/') ?>">← Alla kampanjer</a>
</div>
<?= partial('admin/_errors', ['errors' => $errors]) ?>

<form class="adm-card adm-form" method="post" action="<?= $action ?>" novalidate>
  <?= csrf_field() ?>
  <?= partial('admin/_field', ['name' => 'title', 'label' => 'Titel', 'value' => $c['title'] ?? '', 'errors' => $errors, 'required' => true, 'help' => 'T.ex. Oktober-special.', 'attrs' => 'maxlength="160"']) ?>
  <?= partial('admin/_field', ['name' => 'summary', 'label' => 'Sammanfattning', 'value' => $c['summary'] ?? '', 'errors' => $errors, 'help' => 'Kort text som visas på sajten (högst 300 tecken).', 'attrs' => 'maxlength="300"']) ?>
  <?= partial('admin/_field', ['name' => 'terms', 'label' => 'Villkor', 'type' => 'textarea', 'rows' => 4, 'value' => $c['terms'] ?? '', 'errors' => $errors, 'help' => 'T.ex. giltighet för potten och vem erbjudandet gäller.']) ?>

  <fieldset class="adm-fieldset">
    <legend>Spelpott (valfritt)</legend>
    <p class="adm-help">Anges både pris och spelvärde kan kampanjen beställas som spelpott på /spelpotter/ under giltighetsperioden. swing-order måste ha lediga koder i kodlagret för just det beloppet — annars stoppas ordern där.</p>
    <div class="adm-row2">
      <?= partial('admin/_field', ['name' => 'pay_amount', 'label' => 'Kunden betalar (kr)', 'type' => 'number', 'value' => $c['pay_amount'] ?? '', 'errors' => $errors, 'attrs' => 'min="1" max="100000" step="1" inputmode="numeric"']) ?>
      <?= partial('admin/_field', ['name' => 'value_amount', 'label' => 'Spelvärde (kr)', 'type' => 'number', 'value' => $c['value_amount'] ?? '', 'errors' => $errors, 'attrs' => 'min="1" max="100000" step="1" inputmode="numeric"']) ?>
    </div>
  </fieldset>

  <fieldset class="adm-fieldset">
    <legend>Giltighet och publicering</legend>
    <div class="adm-row3">
      <?= partial('admin/_field', ['name' => 'valid_from', 'label' => 'Giltig från', 'type' => 'date', 'value' => $c['valid_from'] ?? '', 'errors' => $errors, 'help' => 'Tomt = från publicering.']) ?>
      <?= partial('admin/_field', ['name' => 'valid_until', 'label' => 'Giltig till och med', 'type' => 'date', 'value' => $c['valid_until'] ?? '', 'errors' => $errors, 'help' => 'Krävs för publicerade kampanjer.']) ?>
      <?= partial('admin/_field', ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => CampaignModel::STATUSES, 'value' => $c['status'] ?? 'draft', 'errors' => $errors, 'required' => true]) ?>
    </div>
    <div class="adm-row2">
      <div class="adm-field adm-check">
        <input type="checkbox" id="f-show_on_home" name="show_on_home" value="1"<?= ! empty($c['show_on_home']) ? ' checked' : '' ?> />
        <label for="f-show_on_home">Visa på startsidan</label>
      </div>
      <?= partial('admin/_field', ['name' => 'sort_order', 'label' => 'Sorteringsordning', 'type' => 'number', 'value' => $c['sort_order'] ?? 0, 'errors' => $errors, 'help' => 'Lägre tal visas först.', 'attrs' => 'step="1"']) ?>
    </div>
  </fieldset>

  <div class="adm-actions">
    <button type="submit" class="adm-btn adm-btn-primary">Spara</button>
    <a class="adm-btn adm-btn-ghost" href="<?= site_url('admin/kampanjer/') ?>">Avbryt</a>
  </div>
</form>

<?php if ($id !== null): ?>
<form class="adm-danger" method="post" action="<?= site_url('admin/kampanjer/' . $id . '/radera/') ?>" onsubmit="return confirm('Radera kampanjen? Det går inte att ångra.');">
  <?= csrf_field() ?>
  <button type="submit" class="adm-btn adm-btn-danger">Radera kampanjen</button>
</form>
<?php endif; ?>
<?= $this->endSection() ?>
