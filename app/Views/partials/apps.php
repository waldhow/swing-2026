<?php
/**
 * Appkorten: bokningsproviderns app (Config\Booking) och TrackMan Golf (Config\Swing).
 * Prototypens .apps/.app-card-mönster. $headingLevel valfri (standard 3).
 */
$booking = config('Booking');
$tm      = config('Swing')->trackmanApp;
$tag     = 'h' . ($headingLevel ?? 3);
$cards   = [
    ['class' => '', 'name' => $booking->appName, 'role' => $booking->appRole, 'icon' => $booking->appIcon, 'iconBg' => $booking->appIconBackground, 'text' => $booking->appText, 'features' => $booking->appFeatures, 'links' => $booking->appLinks],
    ['class' => ' tm', 'name' => $tm['name'], 'role' => $tm['role'], 'icon' => $tm['icon'], 'iconBg' => $tm['iconBackground'], 'text' => $tm['text'], 'features' => $tm['features'], 'links' => $tm['links']],
];
$badges = [
    'App Store'   => ['src' => '/assets/apps/app-store-sv.svg', 'w' => 138, 'h' => 46, 'alt' => 'Ladda ned %s i App Store'],
    'Google Play' => ['src' => '/assets/apps/google-play-sv.png', 'w' => 155, 'h' => 46, 'alt' => 'Ladda ned %s på Google Play'],
];
?>
<div class="apps">
  <?php foreach ($cards as $card): ?>
  <article class="app-card<?= $card['class'] ?>">
    <div class="app-head">
      <span class="app-icon<?= $card['iconBg'] !== 'transparent' ? ' on-tile' : '' ?>" style="--icon-bg:<?= esc($card['iconBg']) ?>"><img src="/<?= esc($card['icon']) ?>" alt="" width="52" height="52" loading="lazy" /></span>
      <div>
        <p class="role"><?= esc($card['role']) ?></p>
        <<?= $tag ?>><?= esc($card['name']) ?>‑appen</<?= $tag ?>>
      </div>
    </div>
    <p><?= esc($card['text']) ?></p>
    <ul class="feat">
      <?php foreach ($card['features'] as $feature): ?>
      <li><?= icon('check', 17, 2.4) ?><?= esc($feature) ?></li>
      <?php endforeach; ?>
    </ul>
    <div class="stores">
      <?php foreach ($card['links'] as $store => $url): $b = $badges[$store] ?? null; if ($b === null) { continue; } ?>
      <a href="<?= esc($url) ?>"<?= external_attrs($url) ?>><img src="<?= $b['src'] ?>" alt="<?= esc(sprintf($b['alt'], $card['name'])) ?>" width="<?= $b['w'] ?>" height="<?= $b['h'] ?>" loading="lazy" /><?= new_tab_hint($url) ?></a>
      <?php endforeach; ?>
    </div>
  </article>
  <?php endforeach; ?>
</div>
