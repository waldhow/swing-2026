<?php if (! empty($errors)): ?>
<div class="adm-flash adm-flash-err" role="alert">
  <strong>Formuläret innehåller fel:</strong>
  <ul>
    <?php foreach ($errors as $field => $message): ?>
    <li><a href="#f-<?= esc($field, 'attr') ?>"><?= esc($message) ?></a></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>
