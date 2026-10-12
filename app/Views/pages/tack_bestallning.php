<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing  = config('Swing');
$orders = config('Orders');
?>
<header class="phero phero-plain">
  <div class="hero-scrim"></div>
  <div class="hero-in wrap">
    <p class="eyebrow">Tack för din beställning</p>
    <h1>Nu återstår bara betalningen</h1>
    <p class="lede">Din beställning är registrerad. Betala med Swish så skickar vi din kod via e-post när betalningen har kommit in, normalt inom 24 timmar.</p>
  </div>
</header>
<section class="sec-tight">
  <div class="wrap" style="max-width:760px">
    <div class="notice">
      <h2>Betala med Swish</h2>
      <div class="swish">
        <div class="row"><span class="k">Swisha till</span><span class="v"><?= esc($orders->swishNumber) ?></span></div>
        <div class="row"><span class="k">Belopp</span><span class="v"><?= kr((int) $order['pay_amount']) ?></span></div>
        <div class="row"><span class="k">Meddelande</span><span class="v"><?= esc($order['reference']) ?></span></div>
      </div>
      <p>Beställningen hanteras inte förrän betalningen har kommit in.</p>
    </div>
    <div class="card" style="margin-top:20px">
      <h2 class="mt-0" style="font-size:22px">Din beställning</h2>
      <dl class="detail-list">
        <dt>Ordernummer</dt><dd><?= esc($order['reference']) ?></dd>
        <dt>Produkt</dt><dd><?= esc($order['product_name']) ?></dd>
        <?php if ((int) $order['value_amount'] !== (int) $order['pay_amount']): ?><dt>Spelvärde</dt><dd><?= kr((int) $order['value_amount']) ?></dd><?php endif; ?>
        <?php if (! empty($order['gift_card_name'])): ?><dt>Namn på kortet</dt><dd><?= esc($order['gift_card_name']) ?></dd><?php endif; ?>
        <dt>E-post</dt><dd><?= esc($order['buyer_email']) ?></dd>
      </dl>
      <?php if (! empty($order['customer_mailed_at'])): ?><p class="note">Vi har även skickat instruktionerna till din e-post.</p><?php endif; ?>
      <p class="note">Frågor? Mejla <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a> eller ring <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a> och uppge ditt ordernummer.</p>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
