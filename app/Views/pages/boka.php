<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing   = config('Swing');
$booking = config('Booking');
?>
<?= partial('partials/phero', [
    'eyebrow' => 'Boka golftid',
    'h1'      => 'Boka golfsimulator hos Swing',
    'lede'    => "Redo för en golfrunda? Enklast bokar du i {$booking->appName}-appen – där har du också din dörrkod nära till hands. Vi har fyra {$swing->simulatorTech}-simulatorer och öppet alla dagar mellan 06.00 och 00.00.",
    'image'   => 'simulator',
    'trail'   => $trail,
    'actions' => '<a class="btn btn-primary" href="#appar">' . icon('arrowR', 20) . "Ladda ner {$booking->appName}-appen</a>"
        . ($booking->hasBookingUrl() ? partial('partials/book_button', ['label' => 'Boka i webbläsaren', 'variant' => 'ghost']) : ''),
]) ?>

<section class="sec sec-alt" aria-labelledby="appar-rubrik">
  <div class="wrap">
    <div class="sec-head" id="appar">
      <p class="eyebrow">Rekommenderat</p>
      <h2 id="appar-rubrik">Boka i <?= esc($booking->appName) ?>-appen</h2>
      <p class="lede">Ladda ner appen, sök efter Swing Golf &amp; Lounge och boka en tid. Dörrkoden finns sedan i appen och i bekräftelsemejlet. I simulatorn loggar du in med TrackMan Golf-appen, eller spelar som gäst.</p>
    </div>
    <?= partial('partials/apps') ?>
  </div>
</section>

<section class="sec" aria-label="Så bokar du">
  <div class="wrap with-side">
    <div class="prose">
      <?php if (! $booking->hasBookingUrl()): ?>
      <div class="callout"><span class="bar"></span><p><strong>Bokningen är tillfälligt otillgänglig här.</strong> Ring <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a> eller mejla <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a> så hjälper vi dig att boka.</p></div>
      <?php endif; ?>

      <h2>Så bokar du</h2>
      <ol>
        <?php foreach ($booking->bookingSteps as $step): ?>
        <li><?= esc($step) ?></li>
        <?php endforeach; ?>
      </ol>
      <p>Föredrar du datorn? Du kan också boka i webbläsaren, på <?= esc($booking->provider) ?>s bokningssida.</p>

      <h2>När du kommer hit</h2>
      <p>Ange din <?= $swing->doorCodeDigits === 4 ? 'fyrsiffriga' : $swing->doorCodeDigits . '-siffriga' ?> kod vid ytterdörren och avsluta med <strong>#</strong>. Koden är giltig från <?= $swing->doorCodeWindowMinutes ?> minuter före till <?= $swing->doorCodeWindowMinutes ?> minuter efter bokningens starttid.</p>
      <p>Ta med rena klubbor och rena inomhusskor. Använd de golfbollar och peggar som finns hos Swing. Mer om reglerna hittar du under <a href="<?= page_path('infor-besoket') ?>">Inför ditt besök</a>.</p>

      <h2>Om du behöver avboka</h2>
      <p>Du kan avboka kostnadsfritt fram till <?= $swing->freeCancellationHours === 3 ? 'tre' : $swing->freeCancellationHours ?> timmar före bokad starttid. <?= esc($booking->manageText) ?></p>

      <h2>Betala med spelpott eller presentkort</h2>
      <p>Har du en <a href="<?= page_path('spelpotter') ?>">spelpott</a> eller ett <a href="<?= page_path('presentkort') ?>">presentkort</a> väljer du <strong><?= esc($booking->voucherLabel) ?></strong> i kassan.</p>

      <div class="btn-row">
        <a class="btn btn-primary" href="#appar"><?= icon('arrowR', 20) ?>Ladda ner <?= esc($booking->appName) ?>-appen</a>
        <?= partial('partials/book_button', ['label' => 'Boka i webbläsaren', 'variant' => 'ghost']) ?>
      </div>
    </div>
    <div class="side stack">
      <?= partial('partials/facts') ?>
      <div class="card">
        <h2 class="mt-0" style="font-size:22px">Bra att veta</h2>
        <?= partial('partials/faq_list', ['items' => $faqItems]) ?>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
