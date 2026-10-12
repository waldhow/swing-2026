<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing     = config('Swing');
$booking   = config('Booking');
$inquiries = config('Inquiries');
$orders    = config('Orders');
?>
<?= partial('partials/phero', ['eyebrow' => 'Integritet', 'h1' => 'Integritetspolicy', 'lede' => 'Så behandlar vi dina personuppgifter när du kontaktar oss, beställer eller besöker webbplatsen.', 'trail' => $trail]) ?>

<section class="sec">
  <div class="wrap">
    <div class="prose">
      <p class="small">Senast uppdaterad 11 oktober 2026.</p>

      <h2>Personuppgiftsansvarig</h2>
      <p><?= esc($swing->legalName) ?>, org.nr <?= esc($swing->orgNumber) ?>, <?= esc($swing->addressLine()) ?>, är personuppgiftsansvarig för behandlingen som beskrivs här. Kontakt: <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a>.</p>

      <h2>När du skickar en förfrågan</h2>
      <p>När du använder kontakt- eller offertformuläret behandlar vi de uppgifter du lämnar: namn, e-post, telefon, företag eller förening, antal personer, önskat datum och ditt meddelande. Vi använder dem för att svara dig och ta fram ett förslag. Den rättsliga grunden är vårt berättigade intresse av att besvara förfrågningar och, när du vill boka, åtgärder innan ett avtal ingås. Förfrågningar raderas från webbplatsen efter <?= (int) $inquiries->retentionMonths ?> månader.</p>

      <h2>När du beställer spelpott eller presentkort</h2>
      <p>Vi behandlar namn, e-post, uppgifter om beställningen och, om du anger det, namnet som ska stå på presentkortet. Vid köp av friskvårdspott direkt hos oss behandlar vi även ditt personnummer för friskvårdskvittot. Uppgifterna behövs för att leverera beställningen och för att uppfylla bokföringslagen. Webbplatsens kopia av beställningen raderas efter <?= (int) $orders->retentionDays ?> dagar; underlag som krävs enligt bokföringslagen sparas i vårt ordersystem i den tid lagen kräver.</p>
      <?php if (config('Payments')->stripeEnabled()): ?>
      <p>Betalningen hanteras av vår betalleverantör Stripe. Stripe behandlar dina betaluppgifter, till exempel kortnummer, för att genomföra betalningen och förebygga bedrägerier, enligt sin egen integritetspolicy. Vi får bara veta att betalningen är genomförd, aldrig dina kortuppgifter.</p>
      <?php endif; ?>

      <h2>Bokning, Epassi och andra tjänster</h2>
      <p>Bokning av speltid görs i <?= esc($booking->provider) ?>, och köp med friskvårdsbidrag görs i Epassi. Dessa tjänster ansvarar för behandlingen av dina uppgifter i sina system enligt sina egna integritetspolicyer. Detsamma gäller när du följer länkar till Google Maps, Facebook eller Instagram.</p>

      <h2>Vilka som får tillgång till uppgifterna</h2>
      <p>Uppgifterna hanteras av oss på Swing. Leverantörer av webbhotell, e-post<?= config('Payments')->stripeEnabled() ? ', betalningar' : '' ?> och vårt ordersystem behandlar uppgifter för vår räkning och får bara använda dem för att utföra tjänsten åt oss. Vi säljer aldrig dina uppgifter.</p>

      <h2>Cookies</h2>
      <p>Webbplatsen använder inga cookies för statistik eller marknadsföring, och vi använder inga spårningsverktyg. Typsnitt och bilder hämtas från vår egen server. Kartan öppnas som en länk till Google Maps först när du väljer det.</p>

      <h2>Dina rättigheter</h2>
      <p>Du har rätt att begära tillgång till, rättelse av eller radering av dina personuppgifter, att begära begränsning av behandlingen, att invända mot behandling som grundas på berättigat intresse och att få ut uppgifter du själv har lämnat (dataportabilitet). Kontakta oss på <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a>.</p>
      <p>Om du är missnöjd med hur vi behandlar dina uppgifter kan du lämna klagomål till Integritetsskyddsmyndigheten (IMY), imy.se.</p>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
