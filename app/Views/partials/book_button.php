<?php
/**
 * Boka-knapp mot Config\Booking (verifierad Sweetspot-länk eller /boka/).
 * $label valfri, $variant 'primary' (standard) eller 'ghost'.
 */
$url   = booking_url();
$class = ($variant ?? 'primary') === 'ghost' ? 'btn btn-ghost' : 'btn btn-primary';
?>
<a class="<?= $class ?>" href="<?= esc($url) ?>"<?= external_attrs($url) ?>><?= icon('calendar', 20) ?><?= esc($label ?? 'Boka golftid') ?><?= new_tab_hint($url) ?></a>
