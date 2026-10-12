<?php
/**
 * Undersidans hero med brödsmulor. Parametrar:
 *   $eyebrow, $h1, $lede (text), $image (nyckel i Config\Images, valfri), $trail
 *   $actions (valfri HTML med knappar)
 *   $strip   (valfri) faktarad i nederkant: lista med ['icon', 'title', 'text'] — ger
 *            den höga varianten (nivå 2: upplevelsesidor) med startsidans mörkare toning
 */
$strip ??= [];
$tall   = $strip !== [];
?>
<header class="phero<?= empty($image) ? ' phero-plain' : '' ?><?= $tall ? ' phero-tall' : '' ?>">
  <?php if (! empty($image)): ?>
  <div class="hero-bg"><?= picture($image, '100vw', true) ?></div>
  <?php endif; ?>
  <div class="hero-scrim"></div>
  <div class="hero-glow"></div>
  <div class="hero-in wrap">
    <?= partial('partials/crumbs', ['trail' => $trail ?? []]) ?>
    <?php if (! empty($eyebrow)): ?><p class="eyebrow"><?= esc($eyebrow) ?></p><?php endif; ?>
    <h1><?= esc($h1) ?></h1>
    <?php if (! empty($lede)): ?><p class="lede"><?= esc($lede) ?></p><?php endif; ?>
    <?php if (! empty($actions)): ?><div class="btn-row"><?= $actions ?></div><?php endif; ?>
  </div>
  <?php if ($tall): ?>
  <div class="hero-strip">
    <ul class="wrap" style="--cols:<?= count($strip) ?>" aria-label="Kort om <?= esc(mb_strtolower($eyebrow ?? 'sidan')) ?>">
      <?php foreach ($strip as $item): ?>
      <li><?= icon($item['icon'], 22) ?><span><strong><?= esc($item['title']) ?></strong><span><?= esc($item['text']) ?></span></span></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
</header>
