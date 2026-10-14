<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
use App\Libraries\Swish;

$swing    = config('Swing');
$orders   = config('Orders');
$amount   = (int) $order['pay_amount'];
$swishUrl = Swish::paymentUrl($orders->swishNumber, $amount, $order['reference']);
?>
<?php /* Statistik (bara efter samtycke, site.js): beställning gjord – typ och belopp, inga personuppgifter. En gång per order. */ ?>
<div hidden data-track-event="bestallning" data-track-once="<?= esc(md5($order['reference'])) ?>" data-track-params="<?= esc(json_encode(['produkt' => strtolower($order['order_type']), 'value' => (int) $order['pay_amount'], 'currency' => 'SEK'])) ?>"></div>
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
    <div class="notice swish-pay">
      <h2>Betala med Swish</h2>
      <div class="swish-pay-grid">
        <?php /* Mobil: knappen öppnar Swish-appen. Dator: QR-koden skannas med mobilen. Samma förifyllda betalning. */ ?>
        <div class="swish-app">
          <?php /* Swish riktlinjer: officiell ikon (oredigerad) på vit platta, tillsammans med ordet Swish i text. Öppnar appen, därför inte ny flik. */ ?>
          <a class="btn btn-swish" href="<?= esc($swishUrl) ?>" data-same-tab><img src="<?= asset('assets/swish/swish-icon.svg') ?>" alt="" width="30" height="30" />Betala med Swish</a>
          <p class="fhint">Nummer, belopp och ordernummer är redan ifyllda. Du godkänner med BankID.</p>
        </div>
        <figure class="swish-qr">
          <div class="swish-qr-box"><?= Swish::qrSvg($swishUrl, 'QR-kod för Swish: ' . strip_tags(kr($amount)) . ' till ' . $orders->swishNumber . ', meddelande ' . $order['reference']) ?></div>
          <figcaption>Skanna med mobilkameran eller i Swish-appen. Nummer, belopp och ordernummer fylls i automatiskt.</figcaption>
        </figure>
        <div class="swish-manual">
          <h3>Eller swisha manuellt</h3>
          <div class="swish">
            <div class="row"><span class="k">Swisha till</span><span class="v"><?= esc($orders->swishNumber) ?></span></div>
            <div class="row"><span class="k">Belopp</span><span class="v"><?= kr($amount) ?></span></div>
            <div class="row"><span class="k">Meddelande</span><span class="v"><?= esc($order['reference']) ?></span></div>
          </div>
        </div>
      </div>
      <p>Beställningen hanteras inte förrän betalningen har kommit in.</p>
    </div>
    <div class="card" style="margin-top:20px">
      <h2 class="mt-0" style="font-size:22px">Din beställning</h2>
      <dl class="detail-list">
        <dt>Ordernummer</dt><dd><?= esc($order['reference']) ?></dd>
        <dt>Produkt</dt><dd><?= esc($order['product_name']) ?></dd>
        <?php if (! empty($order['gift_card_design'])): ?><dt>Design</dt><dd><?= esc($swing->giftCardDesigns[$order['gift_card_design']] ?? $order['gift_card_design']) ?></dd><?php endif; ?>
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
