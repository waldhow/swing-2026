<?php
/** Textmejl till kunden efter beställning (Swish) eller betalt köp (Stripe). $order, $swing, $config */
$amount   = number_format((int) $order['pay_amount'], 0, ',', ' ');
$paid     = ($order['payment_status'] ?? '') === 'paid';
$delivery = ($order['order_type'] === 'PRESENTKORT' ? 'presentkortet' : 'din kod') . ($order['order_type'] === 'FRISKVARD' ? ' och ditt friskvårdskvitto' : '');
?>
Hej <?= $order['buyer_name'] ?>!

<?= $paid ? 'Tack för ditt köp' : 'Tack för din beställning' ?> hos <?= $swing->name ?>.

Ordernummer: <?= $order['reference'] ?>

Produkt: <?= $order['product_name'] ?>

<?= $paid ? 'Betalt' : 'Att betala' ?>: <?= $amount ?> kr
<?php if (! empty($order['gift_card_name'])): ?>
Namn på presentkortet: <?= $order['gift_card_name'] ?>

<?php endif; ?>

<?php if ($paid): ?>
BETALNINGEN ÄR KLAR
Vi har tagit emot din betalning. Du får <?= $delivery ?> via e-post, normalt inom 24 timmar.
<?php else: ?>
SÅ BETALAR DU
Swisha <?= $amount ?> kr till <?= $config->swishNumber ?> och skriv <?= $order['reference'] ?> i meddelandet.

Vi hanterar beställningen när betalningen har kommit in. Du får <?= $delivery ?> via e-post, normalt inom 24 timmar.
<?php endif; ?>

Har du frågor? Svara på det här mejlet, mejla <?= $swing->email ?> eller ring <?= $swing->phone ?>.

Vi ses på Swing!
<?= $swing->name ?>

<?= $swing->addressLine() ?>

<?= page_url() ?>
