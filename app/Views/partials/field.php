<?php
/**
 * Ett formulärfält med etikett, hjälptext och felmeddelande.
 *   $name, $label, $type (text|email|tel|number|textarea), $required, $autocomplete,
 *   $hint, $form (['values' => [], 'errors' => []]), $attrs (extra attribut som sträng),
 *   $ruleLabel (fältnamnet i felmeddelanden, t.ex. "namn"), $messages (fler data-msg-*, t.ex. ['pattern' => '…'])
 *
 * Felmeddelandena skrivs som data-msg-* så att valideringen i webbläsaren
 * (site.js) visar samma text som servern.
 */
$type      ??= 'text';
$required  ??= false;
$ruleLabel ??= mb_strtolower($label);
$messages  ??= [];
if ($required) {
    $messages['required'] = str_replace('{field}', $ruleLabel, lang('Validation.required'));
}
if ($type === 'email') {
    $messages['email'] = lang('Validation.valid_email');
}
$id        = 'f-' . $name;
$value     = $form['values'][$name] ?? '';
$error     = $form['errors'][$name] ?? null;
$describe  = trim((! empty($hint) ? $id . '-hint ' : '') . ($error ? $id . '-err' : ''));
$common    = sprintf(
    'id="%s" name="%s" class="inp"%s%s%s%s%s',
    $id,
    esc($name),
    $required ? ' required' : '',
    ! empty($autocomplete) ? ' autocomplete="' . esc($autocomplete) . '"' : '',
    $describe !== '' ? ' aria-describedby="' . $describe . '"' : '',
    $error ? ' aria-invalid="true"' : '',
    implode('', array_map(static fn (string $rule, string $text): string => ' data-msg-' . esc($rule) . '="' . esc($text) . '"', array_keys($messages), $messages)),
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
