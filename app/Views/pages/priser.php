<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing   = config('Swing');
$booking = config('Booking');
$pricing = service('pricing');
$players = $swing->priceExamplePlayers;
?>
<?= partial('partials/phero', [
    'eyebrow' => 'Priser',
    'h1'      => 'Priser för golfsimulatorer',
    'lede'    => 'Hos Swing Golf & Lounge betalar du per simulator och timme, inte per person. Det gör det enkelt att dela kostnaden när ni spelar tillsammans.',
    'trail'   => $trail,
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div>
      <?= partial('partials/price_table') ?>
      <p class="note">Alla priser gäller per simulator och timme och inkluderar moms. Vi har öppet <?= esc(mb_strtolower($swing->hoursText)) ?>.</p>

      <div class="prose" style="margin-top:40px">
        <h2>Vad kostar det per person?</h2>
        <p>Är ni <?= $players === 4 ? 'fyra' : $players ?> som delar på en simulator blir det så här per person och timme. Räkneexemplet gäller <?= $players ?> personer – ni betalar alltid för simulatorn, inte per person.</p>
      </div>
      <div class="examples" aria-label="Räkneexempel med <?= $players ?> personer">
        <?php foreach ($pricing->distinctPrices() as $price): ?>
        <div class="example"><div class="k">Vid <?= kr($price) ?>/timme</div><div class="v"><?= kr($pricing->perPerson($price)) ?></div><div class="k">per person (exempel, <?= $players ?> pers.)</div></div>
        <?php endforeach; ?>
      </div>
      <div class="prose">
        <p>Vi rekommenderar <?= esc($swing->recommendedPlayers) ?> spelare per simulator. Du kan också spela ensam eller tillsammans med färre personer.</p>

        <h2>Spelar du ofta?</h2>
        <p>Med en spelpott får du extra spelvärde att använda när du bokar – till exempel <?= kr($swing->creditPots[0]['value']) ?> i spelvärde för <?= kr($swing->creditPots[0]['pay']) ?>. Läs mer under <a href="<?= page_path('spelpotter') ?>">Spelpotter</a>.</p>

        <h2>Bokning och avbokning</h2>
        <p>Du bokar och betalar via <?= esc($booking->provider) ?> och får därefter information om tillträde. Kostnadsfri avbokning är möjlig fram till <?= $swing->freeCancellationHours === 3 ? 'tre' : $swing->freeCancellationHours ?> timmar före bokningens start.</p>
        <div class="btn-row">
          <?= partial('partials/book_button', ['label' => 'Boka simulator']) ?>
          <a class="btn btn-ghost" href="<?= page_path('spelpotter') ?>">Se spelpotter<?= icon('arrowR', 20) ?></a>
        </div>
      </div>
    </div>
    <div class="side">
      <div class="card">
        <h2 class="mt-0" style="font-size:22px">Vanliga frågor om pris</h2>
        <?= partial('partials/faq_list', ['items' => $faqItems]) ?>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
