<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing   = config('Swing');
$booking = config('Booking');
$orders  = config('Orders');
?>
<?= partial('partials/phero', [
    'eyebrow' => 'Presentkort',
    'h1'      => 'Ge bort golfglädje – presentkort på Swing',
    'lede'    => 'Letar du efter en present till någon som gillar golf? Med ett presentkort på Swing Golf & Lounge ger du bort en upplevelse som passar året runt.',
    'image'   => 'lounge',
    'trail'   => $trail,
    'actions' => $orders->isOperational() ? '<a class="btn btn-primary" href="#kop">' . icon('gift', 20) . 'Köp presentkort</a>' : '',
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div class="prose">
      <p>Mottagaren väljer själv när det är dags att spela i en av våra fyra <?= esc($swing->simulatorTech) ?>-simulatorer.</p>

      <h2>För alla tillfällen</h2>
      <ul>
        <li><strong>Födelsedag.</strong> En present som golfaren kan se fram emot.</li>
        <li><strong>Fars dag.</strong> Ge pappa en stunds golf och gemenskap.</li>
        <li><strong>Mors dag.</strong> Överraska mamma med golf och en mysig loungeupplevelse.</li>
        <li><strong>En gåva utan särskild anledning.</strong> Golfglädje uppskattas året runt.</li>
      </ul>
      <p>Du kan välja digitala presentkort från <?= kr($swing->giftCardMin) ?> till <?= kr($swing->giftCardMax) ?> i jämna hundratal. Presentkortet skickas via e-post och gäller i ett år.</p>

      <h2>Så löser mottagaren in presentkortet</h2>
      <ol>
        <?php foreach ($booking->voucherSteps as $step): ?>
        <li><?= esc($step) ?></li>
        <?php endforeach; ?>
      </ol>
    </div>
    <div class="side">
      <div class="card">
        <h2 class="mt-0" style="font-size:22px">Så går köpet till</h2>
        <?= partial('partials/order_how', ['deliver' => 'presentkortet']) ?>
      </div>
    </div>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="bestall-rubrik">
  <div class="wrap" style="max-width:780px">
    <div class="sec-head" id="kop">
      <p class="eyebrow">Köp presentkort</p>
      <h2 id="bestall-rubrik">Beställ ett digitalt presentkort</h2>
    </div>
    <?php if ($orders->isOperational()): ?>
    <?= partial('partials/order_giftcard', ['form' => $form ?? null]) ?>
    <?php else: ?>
    <?= partial('partials/order_unavailable') ?>
    <?php endif; ?>
    <p class="note">Vill du spela själv och få extra spelvärde? Se <a href="<?= page_path('spelpotter') ?>">spelpotter</a>.</p>
  </div>
</section>
<?= $this->endSection() ?>
