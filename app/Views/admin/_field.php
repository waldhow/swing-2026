<?php
/**
 * Formulärfält med etikett, hjälptext och fel.
 * $name, $label, $value, $errors, $type (text|textarea|select|number|date|datetime-local|url|email|password),
 * $options (select), $help, $required, $attrs (extra attribut som sträng).
 */
$type     ??= 'text';
$value    ??= '';
$help     ??= '';
$required ??= false;
$attrs    ??= '';
$options  ??= [];
$error    = $errors[$name] ?? null;
$id       = 'f-' . $name;
$describedBy = trim(($help !== '' ? $id . '-help ' : '') . ($error !== null ? $id . '-err' : ''));
$common   = 'id="' . $id . '" name="' . esc($name, 'attr') . '"'
    . ($required ? ' required aria-required="true"' : '')
    . ($error !== null ? ' aria-invalid="true"' : '')
    . ($describedBy !== '' ? ' aria-describedby="' . $describedBy . '"' : '')
    . ($attrs !== '' ? ' ' . $attrs : '');
?>
<div class="adm-field<?= $error !== null ? ' has-err' : '' ?>">
  <label for="<?= $id ?>"><?= esc($label) ?><?= $required ? ' <span class="adm-req" aria-hidden="true">*</span>' : '' ?></label>
  <?php if ($type === 'textarea'): ?>
  <textarea <?= $common ?> rows="<?= (int) ($rows ?? 5) ?>"><?= esc((string) $value) ?></textarea>
  <?php elseif ($type === 'select'): ?>
  <select <?= $common ?>>
    <?php foreach ($options as $optValue => $optLabel): ?>
    <option value="<?= esc((string) $optValue, 'attr') ?>"<?= (string) $value === (string) $optValue ? ' selected' : '' ?>><?= esc($optLabel) ?></option>
    <?php endforeach; ?>
  </select>
  <?php else: ?>
  <input type="<?= esc($type, 'attr') ?>" value="<?= esc((string) $value, 'attr') ?>" <?= $common ?> />
  <?php endif; ?>
  <?php if ($help !== ''): ?><p class="adm-help" id="<?= $id ?>-help"><?= esc($help) ?></p><?php endif; ?>
  <?php if ($error !== null): ?><p class="adm-err" id="<?= $id ?>-err"><?= esc($error) ?></p><?php endif; ?>
</div>
