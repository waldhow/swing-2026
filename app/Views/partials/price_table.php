<?php
/** Pristabell per simulator och timme (UX-03). $caption valfri. */
$pricing = service('pricing');
?>
<div class="table-wrap">
  <table class="dtable">
    <caption><?= esc($caption ?? 'Pris per simulator och timme') ?></caption>
    <thead>
      <tr><th scope="col">När</th><th scope="col">Pris per simulator och timme</th></tr>
    </thead>
    <tbody>
      <?php foreach ($pricing->periods() as $period): ?>
      <tr><th scope="row"><?= esc($period->label) ?></th><td><?= kr($period->price) ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
