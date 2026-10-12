<?php
/**
 * Tabell över potter. $pots = lista ['name','pay','value'], $caption,
 * $showBonus (bool) — visa kolumnen "Extra spelvärde".
 */
$showBonus ??= true;
?>
<div class="table-wrap" role="region" aria-label="<?= esc($caption) ?>" tabindex="0">
  <table class="dtable">
    <caption><?= esc($caption) ?></caption>
    <thead>
      <tr>
        <th scope="col"><?= esc($nameHeading ?? 'Spelpott') ?></th>
        <th scope="col">Du betalar</th>
        <th scope="col">Du får i spelvärde</th>
        <?php if ($showBonus): ?><th scope="col">Extra spelvärde</th><?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pots as $pot): $bonus = $pot['value'] - $pot['pay']; ?>
      <tr>
        <th scope="row"><?= esc($pot['name']) ?></th>
        <td><?= kr($pot['pay']) ?></td>
        <td><?= kr($pot['value']) ?></td>
        <?php if ($showBonus): ?><td><?= $bonus > 0 ? '+' . kr($bonus) : ($bonus < 0 ? '−' . kr(abs($bonus)) : '–') ?></td><?php endif; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
