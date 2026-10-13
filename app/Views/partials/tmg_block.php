<?php
/**
 * Ett innehållsblock i TrackMan-guiden. $b = blocket ur trackman-guide.json,
 * $hTag = rubriknivå för underrubriker, $focusById = avsnitt-id → fokusnivåer,
 * $guide = TrackmanGuide (för kalkylatorn).
 *
 * Nivåmarkeringar (data-lvl) och fokus (data-focus) styrs av CSS när besökaren
 * väljer nivå – utan JS syns allt innehåll som vanligt.
 */
use App\Libraries\TrackmanGuide;

$k = $b['k'];
?>
<?php if ($k === 'p'): ?>
<p class="tmg-p"><?php if (! empty($b['lead'])): ?><strong><?= esc($b['lead']) ?></strong> <?php endif; ?><?= esc($b['t']) ?></p>
<?php elseif ($k === 's'): ?>
<p class="tmg-s"><?= esc($b['t']) ?></p>
<?php elseif ($k === 'h'): ?>
<<?= $hTag ?> class="tmg-h"><?= esc($b['t']) ?></<?= $hTag ?>>
<?php elseif ($k === 'f'): ?>
<p class="tmg-f"><?= esc($b['t']) ?></p>
<?php elseif ($k === 'n'): ?>
<p class="tmg-n"><?= esc($b['t']) ?></p>
<?php elseif ($k === 'tip'): ?>
<div class="tmg-tip">
  <p class="tmg-tip-label"><?= esc($b['lead']) ?></p>
  <p><?= esc($b['t']) ?></p>
</div>
<?php elseif ($k === 'l'): ?>
<?php $tag = ! empty($b['ordered']) ? 'ol' : 'ul'; ?>
<<?= $tag ?> class="tmg-list" role="list">
  <?php foreach ($b['items'] as $i => $item): ?>
  <?php [$lead, $text] = is_array($item) ? $item : [null, $item]; $lvl = $b['lvls'][$i] ?? null; ?>
  <li<?= $lvl !== null ? ' data-lvl="' . esc($lvl) . '"' : '' ?>><span class="tmg-mark" aria-hidden="true"><?= ! empty($b['ordered']) ? ($i + 1) . '.' : '—' ?></span><span><?php if ($lead !== null): ?><strong><?= esc($lead) ?></strong> <?php endif; ?><?= esc($text) ?></span></li>
  <?php endforeach; ?>
</<?= $tag ?>>
<?php elseif ($k === 't'): ?>
<?php $num = $b['num'] ?? []; $wide = ! empty($b['minW']); ?>
<div class="tmg-table"<?= $wide ? ' tabindex="0" role="region" aria-label="Tabell: ' . esc($b['head'][0]) . '"' : '' ?>>
  <table<?= $wide ? ' style="min-width:' . esc($b['minW']) . '"' : '' ?>>
    <thead>
      <tr><?php foreach ($b['head'] as $i => $h): ?><th scope="col"<?= ! empty($num[$i]) ? ' class="num"' : '' ?>><?= esc($h) ?></th><?php endforeach; ?></tr>
    </thead>
    <tbody>
      <?php foreach ($b['rows'] as $ri => $row): ?>
      <?php $lvl = $b['lvls'][$ri] ?? null; ?>
      <tr<?= $lvl !== null ? ' data-lvl="' . esc($lvl) . '"' : '' ?>>
        <?php foreach ($row as $i => $cell): ?>
        <?php $cls = trim((! empty($num[$i]) ? 'num' : '') . ($cell === 'Fyll i' ? ' fill' : '')); ?>
        <?php if ($i === 0): ?><th scope="row"<?= $cls !== '' ? ' class="' . $cls . '"' : '' ?>><?= esc($cell) ?></th><?php else: ?><td<?= $cls !== '' ? ' class="' . $cls . '"' : '' ?>><?= esc($cell) ?></td><?php endif; ?>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php elseif ($k === 'cards'): ?>
<ul class="tmg-cards" role="list">
  <?php foreach ($b['cards'] as [$name, $what, $goal, $ref]): ?>
  <li><a class="tmg-card" href="#s<?= (int) $ref ?>" data-focus="<?= esc(implode(' ', $focusById['s' . $ref] ?? [])) ?>">
    <span class="tmg-card-top"><span class="tmg-card-name"><?= esc($name) ?></span><span class="tmg-card-ref" aria-hidden="true">§<?= (int) $ref ?></span></span>
    <span class="tmg-card-what"><?= esc($what) ?></span>
    <span class="tmg-card-goal"><span class="tmg-card-goal-label">Försök uppnå</span><?= esc($goal) ?></span>
  </a></li>
  <?php endforeach; ?>
</ul>
<?php elseif ($k === 'calc'): ?>
<?php
$clubs  = $guide->clubs();
$zones  = $guide->zones();
$driver = $clubs['driver'];
$value  = round($driver['bs'] / $driver['cs'], 2);
$pct    = static fn (float $v): string => number_format(max(0, min(100, (($v - 1.10) / 0.5) * 100)), 2, '.', '') . '%';
?>
<div class="tmg-calc" data-calc data-club="driver" data-clubs="<?= esc(json_encode($clubs)) ?>" data-zones="<?= esc(json_encode($zones)) ?>">
  <div class="tmg-calc-top">
    <p class="tmg-calc-title" id="tmg-calc-title">Räkna ut din Smash Factor</p>
    <div class="tmg-seg" role="group" aria-label="Klubba">
      <?php foreach ($clubs as $key => $club): ?>
      <button type="button" data-club="<?= esc($key) ?>" aria-pressed="<?= $key === 'driver' ? 'true' : 'false' ?>"><?= esc($club['label']) ?></button>
      <?php endforeach; ?>
    </div>
  </div>
  <div class="tmg-calc-grid">
    <div class="tmg-calc-inputs">
      <label class="tmg-range"><span class="tmg-range-top"><span>Club Speed</span><output data-cs-out><?= (int) $driver['cs'] ?> mph</output></span><input type="range" min="50" max="130" step="1" value="<?= (int) $driver['cs'] ?>" data-cs /></label>
      <label class="tmg-range"><span class="tmg-range-top"><span>Ball Speed</span><output data-bs-out><?= (int) $driver['bs'] ?> mph</output></span><input type="range" min="60" max="195" step="1" value="<?= (int) $driver['bs'] ?>" data-bs /></label>
    </div>
    <div class="tmg-calc-result" aria-live="polite">
      <span class="tmg-calc-eq"><span data-bs-eq><?= (int) $driver['bs'] ?></span> / <span data-cs-eq><?= (int) $driver['cs'] ?></span> =</span>
      <span class="tmg-calc-value" data-value><?= esc(str_replace('.', ',', number_format($value, 2))) ?></span>
      <span class="tmg-calc-verdict" data-verdict><?= esc(TrackmanGuide::smashVerdict($value)) ?></span>
    </div>
  </div>
  <div class="tmg-scale" aria-hidden="true">
    <div class="tmg-scale-track" data-zones-track>
      <?php foreach ($zones as $i => [$a, $z]): ?>
      <span class="tmg-zone<?= $value >= $a && $value < $z ? ' on' : '' ?>" style="left:<?= $pct($a) ?>;width:<?= number_format((($z - $a) / 0.5) * 100, 2, '.', '') ?>%;--i:<?= $i ?>"></span>
      <?php endforeach; ?>
      <span class="tmg-scale-ref" data-ref style="left:<?= $pct($driver['ref']) ?>"></span>
      <span class="tmg-scale-mark" data-mark style="left:<?= $pct($value) ?>"></span>
    </div>
    <div class="tmg-scale-labels"><span>1,10</span><span data-ref-label><?= esc($driver['refLabel']) ?></span><span>1,60</span></div>
  </div>
</div>
<?php elseif ($k === 'qa'): ?>
<div class="tmg-qa">
  <?php foreach ($b['qa'] as [$q, $a]): ?>
  <details>
    <summary><span><?= esc($q) ?></span><span class="tmg-qa-plus" aria-hidden="true">+</span></summary>
    <p><?= esc($a) ?></p>
  </details>
  <?php endforeach; ?>
</div>
<?php elseif ($k === 'goals'): ?>
<ul class="tmg-goals" role="list">
  <?php foreach ($b['goals'] as [$q, $a]): ?>
  <li><p class="tmg-goal-q"><?= esc($q) ?></p><p><?= esc($a) ?></p></li>
  <?php endforeach; ?>
</ul>
<?php elseif ($k === 'quote'): ?>
<blockquote class="tmg-quote"><p><?= esc($b['t']) ?></p></blockquote>
<?php endif; ?>
