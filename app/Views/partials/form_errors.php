<?php
/** Felsammanfattning överst i formuläret (fokuseras av site.js). $form, $labels = [fält => etikett] */
$errors = $form['errors'] ?? [];
if ($errors === []) {
    return;
}
?>
<div class="form-alert" role="alert" tabindex="-1" data-focus>
  <p><strong><?= isset($errors['form']) && count($errors) === 1 ? esc($errors['form']) : 'Kontrollera formuläret:' ?></strong></p>
  <?php if (! (isset($errors['form']) && count($errors) === 1)): ?>
  <ul>
    <?php foreach ($errors as $field => $message): ?>
    <?php if ($field === 'form'): ?><li><?= esc($message) ?></li><?php continue; endif; ?>
    <li><a href="#f-<?= esc($field) ?>"><?= esc($message) ?></a></li>
    <?php endforeach; ?>
  </ul>
  <?php endif; ?>
</div>
