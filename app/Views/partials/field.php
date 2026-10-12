<?php
/**
 * Ett formulärfält med etikett, hjälptext och felmeddelande.
 *   $name, $label, $type (text|email|tel|number|textarea), $required, $autocomplete,
 *   $hint, $form (['values' => [], 'errors' => []]), $attrs (extra attribut som sträng)
 */
$type     ??= 'text';
$required ??= false;
$id        = 'f-' . $name;
$value     = $form['values'][$name] ?? '';
$error     = $form['errors'][$name] ?? null;
$describe  = trim((! empty($hint) ? $id . '-hint ' : '') . ($error ? $id . '-err' : ''));
$common    = sprintf(
    'id="%s" name="%s" class="inp"%s%s%s%s',
    $id,
    esc($name),
    $required ? ' required' : '',
    ! empty($autocomplete) ? ' autocomplete="' . esc($autocomplete) . '"' : '',
    $describe !== '' ? ' aria-describedby="' . $describe . '"' : '',
    $error ? ' aria-invalid="true"' : '',
);
?>
<div class="field<?= $error ? ' has-error' : '' ?>">
  <label class="ilab" for="<?= $id ?>"><?= esc($label) ?><?= $required ? ' <span class="req" aria-hidden="true">*</span>' : ' <span class="opt">(valfritt)</span>' ?></label>
  <?php if ($type === 'textarea'): ?>
  <textarea <?= $common ?> rows="5" maxlength="3000"<?= $attrs ?? '' ?>><?= esc($value) ?></textarea>
  <?php else: ?>
  <input type="<?= esc($type) ?>" <?= $common ?> value="<?= esc($value) ?>"<?= $attrs ?? '' ?> />
  <?php endif; ?>
  <?php if (! empty($hint)): ?><p class="fhint" id="<?= $id ?>-hint"><?= esc($hint) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="ferr" id="<?= $id ?>-err"><?= icon('info', 16) ?><?= esc($error) ?></p><?php endif; ?>
</div>
