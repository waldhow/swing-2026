<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing   = config('Swing');
$booking = config('Booking');
$orders  = config('Orders');
?>
<?= partial('partials/phero', ['eyebrow' => 'Villkor', 'h1' => 'Köpvillkor', 'lede' => 'Villkor för köp av spelpotter och digitala presentkort hos Swing Golf & Lounge.', 'trail' => $trail]) ?>

<section class="sec">
  <div class="wrap">
    <div class="prose">
      <p class="small">Senast uppdaterad 11 oktober 2026.</p>

      <h2>1. Säljare</h2>
      <p><?= esc($swing->name) ?> drivs av <?= esc($swing->legalName) ?>, org.nr <?= esc($swing->orgNumber) ?>, <?= esc($swing->addressLine()) ?>. Du når oss på <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a> eller <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a>.</p>

      <h2>2. Villkorens omfattning</h2>
      <p>Villkoren gäller när du beställer spelpotter eller digitala presentkort via swinggolf.se. Bokning av speltid görs i <?= esc($booking->provider) ?> och omfattas av de villkor som visas där. Köp via Epassi omfattas av Epassis villkor.</p>

      <h2>3. Beställning och betalning</h2>
      <ul>
        <li>Alla priser anges i svenska kronor inklusive moms.</li>
        <li>När du har beställt får du ett ordernummer. Du betalar med Swish till <?= esc($orders->swishNumber) ?> och anger ordernumret i meddelandet.</li>
        <li>Beställningen hanteras när betalningen har kommit in. Vi skickar en bekräftelse med din kod via e-post, normalt inom 24 timmar.</li>
        <li>Kontrollera att e-postadressen du anger är rätt – det är dit vi skickar koden.</li>
      </ul>

      <h2>4. Spelpotter</h2>
      <ul>
        <li>En spelpott är förbetalt spelvärde som används som betalning när du bokar speltid hos oss. Det extra spelvärdet ingår i potten.</li>
        <li><?= esc($booking->creditLinkText) ?> Du använder potten genom att välja <?= esc($booking->voucherLabel) ?> i kassan.</li>
        <li>Spelpotten gäller i <?= $swing->creditValidityMonths ?> månader från köpdagen. Spelvärde som inte har använts inom giltighetstiden förfaller.</li>
        <li>Spelpotten är personlig och kan inte överlåtas.</li>
        <li>Spelpotten kan inte lösas in mot kontanter, utom när du ångrar ett köp enligt punkt 7.</li>
      </ul>

      <h2>5. Presentkort</h2>
      <ul>
        <li>Presentkort är digitala och skickas via e-post. Du väljer belopp från <?= kr($swing->giftCardMin) ?> till <?= kr($swing->giftCardMax) ?> i steg om <?= kr($swing->giftCardStep) ?>.</li>
        <li>Presentkortet används som betalning för speltid och löses in via <?= esc($booking->provider) ?>.</li>
        <li>Presentkortet gäller i <?= $swing->giftCardValidityMonths ?> månader från köpdagen. Värde som inte har använts inom giltighetstiden förfaller.</li>
        <li>Presentkortet är inte personligt och kan ges bort.</li>
        <li>Presentkortet kan inte lösas in mot kontanter, utom när du ångrar ett köp enligt punkt 7.</li>
      </ul>

      <h2>6. Friskvård</h2>
      <p>Friskvårdspotter är personliga och får bara användas till din egen speltid. De gäller i <?= $swing->creditValidityMonths ?> månader från köpdagen. När du köper en friskvårdspott direkt hos oss anger du ditt personnummer, som behövs för kvittot.</p>

      <h2>7. Ångerrätt</h2>
      <p>När du köper en spelpott eller ett presentkort på distans har du 14 dagars ångerrätt från köpdagen enligt lagen om distansavtal och avtal utanför affärslokaler, så länge inget av värdet har använts. Meddela oss inom 14 dagar, till exempel via <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a>. Vi betalar tillbaka senast 14 dagar efter att vi har fått ditt meddelande.</p>

      <h2>8. Reklamation och tvister</h2>
      <p>Har något blivit fel – hör av dig till oss så löser vi det. Om vi inte blir överens kan du vända dig till Allmänna reklamationsnämnden (ARN), arn.se.</p>

      <h2>9. Ändringar</h2>
      <p>Vi kan uppdatera villkoren. De villkor som gällde när du köpte gäller för ditt köp.</p>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
