<?php
/**
 * Butiksknappar för bokningsproviderns app (Config\Booking) med en kort uppmaning.
 * $pitch valfri text ovanför (false = ingen), $class extra klass.
 */
$booking = config('Booking');
$pitch ??= $booking->appPitch;
$badges  = [
    'App Store'   => ['src' => '/assets/apps/app-store-sv.svg', 'w' => 138, 'h' => 46, 'alt' => 'Ladda ned %s i App Store'],
    'Google Play' => ['src' => '/assets/apps/google-play-sv.png', 'w' => 155, 'h' => 46, 'alt' => 'Ladda ned %s på Google Play'],
];
?>
<div class="app-badges<?= ! empty($class) ? ' ' . esc($class) : '' ?>">
  <?php if ($pitch !== false): ?><p class="app-pitch"><?= esc($pitch) ?></p><?php endif; ?>
  <div class="stores">
    <?php foreach ($booking->appLinks as $store => $url): $b = $badges[$store] ?? null; if ($b === null) { continue; } ?>
    <a href="<?= esc($url) ?>"<?= external_attrs($url) ?>><img src="<?= $b['src'] ?>" alt="<?= esc(sprintf($b['alt'], $booking->appName)) ?>" width="<?= $b['w'] ?>" height="<?= $b['h'] ?>" /><?= new_tab_hint($url) ?></a>
    <?php endforeach; ?>
  </div>
</div>
