<?php
/** Textmejl till kunden efter beställning. $order, $swing, $config */
$amount = number_format((int) $order['pay_amount'], 0, ',', ' ');
?>
Hej <?= $order['buyer_name'] ?>!

Tack för din beställning hos <?= $swing->name ?>.

Ordernummer: <?= $order['reference'] ?>

Produkt: <?= $order['product_name'] ?>

Att betala: <?= $amount ?> kr
<?php if (! empty($order['gift_card_name'])): ?>
Namn på presentkortet: <?= $order['gift_card_name'] ?>

<?php endif; ?>

SÅ BETALAR DU
Swisha <?= $amount ?> kr till <?= $config->swishNumber ?> och skriv <?= $order['reference'] ?> i meddelandet.

Vi hanterar beställningen när betalningen har kommit in. Du får en bekräftelse med din kod via e-post, normalt inom 24 timmar.

Har du frågor? Svara på det här mejlet, mejla <?= $swing->email ?> eller ring <?= $swing->phone ?>.

Vi ses på Swing!
<?= $swing->name ?>

<?= $swing->addressLine() ?>

<?= page_url() ?>
