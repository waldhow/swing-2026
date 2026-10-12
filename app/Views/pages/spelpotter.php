<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing   = config('Swing');
$booking = config('Booking');
$orders  = config('Orders');
$open    = $orders->isOperational();
$best    = end($swing->creditPots);
?>
<?= partial('partials/phero', [
    'eyebrow' => 'Spelpotter',
    'h1'      => 'Spelpotter – mer golf för pengarna',
    'lede'    => 'Spelar du ofta hos Swing Golf & Lounge? Med en spelpott får du mer golf för pengarna. Köp spelvärde i förväg och använd det när du bokar dina tider.',
    'image'   => 'bar',
    'trail'   => $trail,
    'actions' => $open ? '<a class="btn btn-primary" href="#kop">' . icon('wallet', 20) . 'Köp spelpott</a>' : '',
]) ?>

<section class="sec" aria-labelledby="valj">
  <div class="wrap">
    <div class="sec-head" id="kop">
      <p class="eyebrow">Välj din spelpott</p>
      <h2 id="valj">Betala en gång. Spela för mer.</h2>
      <p class="lede">Ju större pott, desto mer extra spelvärde – med <?= esc($best['name']) ?> får du <?= kr($best['value'] - $best['pay']) ?> extra att spela för. Potten gäller i ett år och används när du bokar.</p>
    </div>
    <?= partial('partials/order_pot_cards', [
        'pots'         => $swing->creditPots,
        'campaignPots' => $campaignPots ?? [],
        'form'         => $form ?? null,
        'interactive'  => $open,
    ]) ?>
    <?php if (! $open): ?>
    <div style="margin-top:26px"><?= partial('partials/order_unavailable') ?></div>
    <?php endif; ?>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="anvand">
  <div class="wrap cols-2">
    <div class="prose">
      <h2 id="anvand">Så använder du potten</h2>
      <p><?= esc($booking->creditLinkText) ?> När du bokar en simulator väljer du <strong><?= esc($booking->voucherLabel) ?></strong> i kassan.</p>
      <p>Vill du ge bort golf? Se <a href="<?= page_path('presentkort') ?>">presentkort</a>. Vill du använda friskvårdsbidraget? Se <a href="<?= page_path('friskvard') ?>">friskvård och Epassi</a>.</p>
    </div>
    <div class="prose">
      <h2>Vanliga frågor</h2>
      <h3>Hur länge gäller en spelpott?</h3>
      <p>Ett år från köpet.</p>
      <h3>Är potten personlig?</h3>
      <p>Den är personlig och kan inte överlåtas.</p>
      <h3>Kan jag använda den vid bokning?</h3>
      <p>Ja, genom voucher-alternativet i <?= esc($booking->provider) ?>s kassa.</p>
      <h3>Finns friskvårdsalternativ?</h3>
      <p>Ja, bland annat via Epassi. Epassi har egna belopp, se sidan <a href="<?= page_path('friskvard') ?>">Friskvård och Epassi</a>.</p>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
