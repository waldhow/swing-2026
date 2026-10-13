<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing     = config('Swing');
$booking   = config('Booking');
$inquiries = config('Inquiries');
$orders    = config('Orders');
$ga        = config('Analytics')->enabled();
$mail      = '<a href="mailto:' . esc($swing->email) . '">' . esc($swing->email) . '</a>';
?>
<?= partial('partials/phero', ['eyebrow' => 'Integritet', 'h1' => 'Integritetspolicy', 'lede' => 'Så behandlar vi dina personuppgifter när du kontaktar oss, köper, bokar, deltar i tävlingar, besöker anläggningen eller webbplatsen.', 'trail' => $trail]) ?>

<section class="sec">
  <div class="wrap">
    <div class="prose">
      <p class="small">Senast uppdaterad 13 oktober 2026. Ersätter integritetspolicyn från 15 december 2023.</p>

      <h2 id="kort">Kort sammanfattning</h2>
      <ul>
        <li>Vi behandlar bara de uppgifter som behövs för att svara dig, leverera det du köper, hantera bokningar och tävlingar och följa lagen.</li>
        <li>Vi säljer aldrig dina uppgifter och lämnar inte ut dem till andra för deras marknadsföring.</li>
        <?php if ($ga): ?>
        <li>Statistik med Google Analytics används bara om du godkänner det. Utan ditt samtycke sätts inga cookies för statistik, och inga för marknadsföring alls.</li>
        <?php else: ?>
        <li>Webbplatsen använder inga cookies för statistik eller marknadsföring och inga spårningsverktyg.</li>
        <?php endif; ?>
        <li>Personnummer frågar vi bara efter när du köper en friskvårdspott direkt hos oss, för friskvårdskvittot.</li>
        <li>Lokalen är kamerabevakad, och det är skyltat på plats.</li>
        <li>Du kan alltid fråga vilka uppgifter vi har om dig, och be oss rätta eller radera dem.</li>
      </ul>

      <h2 id="ansvarig">Personuppgiftsansvarig</h2>
      <p><?= esc($swing->name) ?> drivs av <?= esc($swing->legalName) ?>, org.nr <?= esc($swing->orgNumber) ?>, <?= esc($swing->addressLine()) ?>. <?= esc($swing->legalName) ?> är personuppgiftsansvarig för behandlingen som beskrivs här. Du når oss på <?= $mail ?> eller <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a>.</p>

      <h2 id="oversikt">Översikt</h2>
    </div>
    <div class="table-wrap" style="margin:18px 0 30px">
      <table class="dtable dtable-text" role="table" aria-labelledby="oversikt">
        <caption>Varför vi behandlar uppgifterna, med vilken rätt och hur länge</caption>
        <thead>
          <tr role="row"><th scope="col" role="columnheader">Ändamål</th><th scope="col" role="columnheader">Laglig grund</th><th scope="col" role="columnheader">Hur länge</th></tr>
        </thead>
        <tbody>
          <tr role="row"><th scope="row" role="rowheader">Svara på förfrågningar och frågor</th><td role="cell" data-label="Laglig grund">Berättigat intresse, och åtgärder innan avtal när du vill boka</td><td role="cell" data-label="Hur länge">Webbplatsens kopia <?= (int) $inquiries->retentionMonths ?> månader. I vår e-post så länge ärendet pågår och kan behöva följas upp.</td></tr>
          <tr role="row"><th scope="row" role="rowheader">Leverera spelpott, presentkort och friskvårdspott</th><td role="cell" data-label="Laglig grund">Avtal</td><td role="cell" data-label="Hur länge">Webbplatsens kopia <?= (int) $orders->retentionDays ?> dagar. I vårt ordersystem så länge köpet gäller och kan reklameras.</td></tr>
          <tr role="row"><th scope="row" role="rowheader">Bokföring och kvitton</th><td role="cell" data-label="Laglig grund">Rättslig förpliktelse (bokföringslagen)</td><td role="cell" data-label="Hur länge">Sju år efter utgången av det kalenderår då räkenskapsåret avslutades</td></tr>
          <tr role="row"><th scope="row" role="rowheader">Bokningar av speltid</th><td role="cell" data-label="Laglig grund">Avtal</td><td role="cell" data-label="Hur länge">Så länge bokningen kan behöva hanteras, och i <?= esc($booking->provider) ?> enligt deras villkor</td></tr>
          <tr role="row"><th scope="row" role="rowheader">Tävlingar och event</th><td role="cell" data-label="Laglig grund">Avtal, och berättigat intresse för resultatlistor</td><td role="cell" data-label="Hur länge">Deltagaruppgifter till och med att tävlingen är avslutad. Resultatlistor kan finnas kvar.</td></tr>
          <tr role="row"><th scope="row" role="rowheader">Kamerabevakning i lokalen</th><td role="cell" data-label="Laglig grund">Berättigat intresse</td><td role="cell" data-label="Hur länge">En kort tid, sedan raderas inspelningarna automatiskt. Längre bara när en händelse utreds.</td></tr>
          <?php if ($ga): ?>
          <tr role="row"><th scope="row" role="rowheader">Statistik över hur webbplatsen används</th><td role="cell" data-label="Laglig grund">Samtycke</td><td role="cell" data-label="Hur länge">Tills du drar tillbaka samtycket. Statistiken sparas i Google Analytics i högst 14 månader.</td></tr>
          <?php endif; ?>
          <tr role="row"><th scope="row" role="rowheader">Skydda webbplatsen mot missbruk</th><td role="cell" data-label="Laglig grund">Berättigat intresse</td><td role="cell" data-label="Hur länge">Krypterad IP-adress högst en timme. Webbhotellets tekniska loggar enligt webbhotellets rutiner.</td></tr>
        </tbody>
      </table>
    </div>
    <div class="prose">

      <h2 id="forfragningar">När du kontaktar oss</h2>
      <p>När du använder kontakt- eller offertformuläret, mejlar eller ringer oss behandlar vi de uppgifter du lämnar: namn, e-post, telefon, företag eller förening, antal personer, önskat datum och ditt meddelande. Vi använder dem för att svara dig och ta fram ett förslag. Den lagliga grunden är vårt berättigade intresse av att besvara frågor och, när du vill boka, åtgärder innan ett avtal ingås. Förfrågningar som kommer via webbplatsen raderas där efter <?= (int) $inquiries->retentionMonths ?> månader.</p>

      <h2 id="kop">När du köper spelpott, presentkort eller friskvårdspott</h2>
      <p>Vi behandlar ditt namn, din e-post, ordernummer, vad du köper, belopp och, för presentkort, namnet som ska stå på kortet. När du betalar med Swish får vi betalarens namn och mobilnummer från betalningen, så att vi kan stämma av den mot din beställning. Uppgifterna behövs för att leverera det du köper, skicka bekräftelse och kod, ge kundservice och bokföra köpet.</p>
      <p>Den lagliga grunden är avtalet med dig, och för bokföringen den rättsliga förpliktelsen i bokföringslagen. Webbplatsens kopia av beställningen raderas efter <?= (int) $orders->retentionDays ?> dagar. Beställningen finns kvar i vårt ordersystem så länge köpet gäller och kan reklameras. Underlag som räknas som räkenskapsinformation sparas så länge bokföringslagen kräver.</p>

      <h3 id="personnummer">Personnummer</h3>
      <p>När du köper en friskvårdspott direkt hos oss behöver vi ditt personnummer. Det står på kvittot du lämnar till din arbetsgivare för friskvårdsbidraget. Det är klart motiverat med hänsyn till ändamålet och behovet av säker identifiering (3 kap. 10 § dataskyddslagen). Personnumret sparas krypterat på webbplatsen och används inte till något annat. Vi frågar inte efter personnummer i andra sammanhang.</p>

      <h2 id="bokning">När du bokar speltid</h2>
      <p>Bokning görs i <?= esc($booking->provider) ?>. Vi får tillgång till dina bokningsuppgifter, till exempel namn, e-post, telefon och bokad tid, för att hantera bokningen, tillträdet till anläggningen och dina frågor. Den lagliga grunden är avtalet med dig. <?= esc($booking->provider) ?> behandlar också uppgifterna enligt sin egen integritetspolicy.</p>

      <h2 id="tavlingar">När du deltar i tävlingar och event</h2>
      <p>Vi behandlar namn, kontaktuppgifter och de uppgifter som behövs för tävlingen, till exempel ålder eller födelseår när det finns åldersklasser. Resultatlistor med namn och placering kan publiceras på webbplatsen och i våra sociala medier. Den lagliga grunden är avtalet om ditt deltagande, och för resultatlistorna vårt berättigade intresse. Vill du inte synas i en resultatlista kan du säga till, så tar vi bort ditt namn.</p>

      <h2 id="kamerabevakning">Kamerabevakning i lokalen</h2>
      <p>Lokalen är kamerabevakad, och det är skyltat på plats. Anläggningen är normalt obemannad, och bevakningen ska förebygga och utreda brott, skadegörelse och olyckor och skydda besökare, personal och utrustning. Vi behandlar bilder från kamerorna. Den lagliga grunden är vårt berättigade intresse av att skydda anläggningen och de som vistas där.</p>
      <p>Inspelningarna raderas automatiskt efter en kort tid och sparas längre bara när de behövs för att utreda en händelse. Bara ett fåtal behöriga har tillgång till dem, och de lämnas ut till polisen eller andra myndigheter bara vid misstanke om brott eller när lagen kräver det. Du har rätt att begära ut inspelningar där du syns, så länge de finns kvar och andra personers integritet kan skyddas.</p>

      <h2 id="epassi">Friskvård via Epassi</h2>
      <p>Köp med friskvårdsbidrag via Epassi görs i Epassis tjänst. Epassi ansvarar för de uppgifter du lämnar där. Vi får de uppgifter som behövs för att leverera köpet till dig.</p>

      <h2 id="webbplatsen">När du besöker webbplatsen</h2>
      <?php if ($ga): ?>
      <p><strong>Statistik med Google Analytics, bara om du godkänner det.</strong> Första gången du besöker webbplatsen frågar vi om vi får mäta besöket. Säger du ja laddas Google Analytics och sätter cookies (<code>_ga</code> och <code>_ga_…</code>, som finns kvar i upp till två år om du inte tar bort dem). Google får då uppgifter om ditt besök: vilka sidor du tittar på, hur du kom till oss, vilken typ av enhet och webbläsare du använder och ungefär var du befinner dig (land eller stad). Google Analytics sparar inte din IP-adress. Vi har stängt av Googles annonsfunktioner och Google signals, och vi använder statistiken bara för att förstå hur webbplatsen används och göra den bättre.</p>
      <p>Den lagliga grunden är ditt samtycke. Säger du nej laddas Google Analytics inte alls. Du kan ändra dig när som helst under <em>Cookie-inställningar</em> längst ned på varje sida. Säger du nej i efterhand tar vi bort Analytics-cookies från din webbläsare. Ditt val sparas i en nödvändig cookie (<code>swing_consent</code>) i 12 månader, sedan frågar vi igen.</p>
      <p>Andra cookies än dessa sätts inte på de publika sidorna. Inloggningen till vår administration använder en nödvändig cookie som bara gäller personalen. Typsnitt och bilder hämtas från vår egen server. Google Maps, <?= esc($booking->provider) ?>, Facebook och Instagram nås bara via länkar och öppnas först när du klickar.</p>
      <?php else: ?>
      <p>Webbplatsen använder inga cookies för statistik eller marknadsföring, och vi använder inga spårningsverktyg. De publika sidorna sätter inga cookies alls. Inloggningen till vår administration använder en nödvändig cookie som bara gäller personalen. Typsnitt och bilder hämtas från vår egen server. Google Maps, <?= esc($booking->provider) ?>, Facebook och Instagram nås bara via länkar och öppnas först när du klickar.</p>
      <?php endif; ?>
      <p>För att skydda formulären mot spam och missbruk begränsar vi hur många inskick som kan göras från samma IP-adress. IP-adressen sparas då i krypterad form (hashad) i högst en timme. Vårt webbhotell loggar tekniska uppgifter som IP-adress och tidpunkt för drift och säkerhet. Den lagliga grunden är vårt berättigade intresse av en säker webbplats.</p>

      <h2 id="sociala-medier">Sociala medier</h2>
      <p>Om du kontaktar oss via Facebook eller Instagram använder vi uppgifterna för att svara dig. Meta behandlar uppgifterna i sina tjänster enligt sin egen integritetspolicy.</p>

      <h2 id="mottagare">Vilka som får tillgång till uppgifterna</h2>
      <p>Uppgifterna hanteras av oss på <?= esc($swing->name) ?>. Följande leverantörer behandlar uppgifter för vår räkning, som personuppgiftsbiträden. De får bara använda uppgifterna för att utföra tjänsten åt oss:</p>
      <ul>
        <li><strong>Loopia:</strong> webbhotell, databas och utskick av e-post från webbplatsen.</li>
        <li><strong>Google (Google Workspace):</strong> vår e-post.</li>
        <?php if ($ga): ?><li><strong>Google (Google Analytics):</strong> besöksstatistik, bara om du har godkänt det.</li><?php endif; ?>
        <li><strong>Vercel och Supabase:</strong> driften av vårt ordersystem, med lagring inom EU (Irland).</li>
        <li><strong>Slack:</strong> interna notiser till personalen om nya beställningar.</li>
      </ul>
      <p>Följande ansvarar själva för sin behandling, som självständigt personuppgiftsansvariga:</p>
      <ul>
        <li><strong><?= esc($booking->provider) ?>:</strong> bokning.</li>
        <li><strong>Epassi:</strong> friskvårdsbidrag.</li>
        <li><strong>Banker och Swish:</strong> betalningar.</li>
        <li><strong>Meta:</strong> Facebook och Instagram.</li>
      </ul>
      <p>Vi lämnar ut uppgifter till myndigheter, till exempel Skatteverket eller polisen, när lagen kräver det. Vi säljer aldrig dina uppgifter.</p>

      <h2 id="overforing">Överföring utanför EU/EES</h2>
      <p>Vårt ordersystem lagras inom EU (Irland), och webbplatsen och dess databas finns hos Loopia i Sverige. Google, Slack och Meta kan behandla uppgifter utanför EU/EES. Även leverantörer som lagrar uppgifterna inom EU kan i vissa fall komma åt dem från länder utanför EU, till exempel vid support. Överföringen sker då med stöd av EU-kommissionens beslut om EU–US Data Privacy Framework eller EU-kommissionens standardavtalsklausuler.</p>

      <h2 id="sakerhet">Så skyddar vi uppgifterna</h2>
      <p>Webbplatsen använder krypterad anslutning (HTTPS). Personnummer sparas krypterade, och lösenord till vår administration sparas bara i hashad form. Bara behöriga medarbetare kommer åt uppgifterna. Uppgifter som inte längre behövs raderas automatiskt enligt tiderna ovan.</p>

      <h2 id="rattigheter">Dina rättigheter</h2>
      <p>Du har rätt att:</p>
      <ul>
        <li>få veta vilka uppgifter vi har om dig och få en kopia (tillgång)</li>
        <li>få felaktiga uppgifter rättade</li>
        <li>få uppgifter raderade, när de inte längre behövs eller saknar laglig grund. Uppgifter vi måste spara enligt bokföringslagen kan vi inte radera i förtid.</li>
        <li>begära att behandlingen begränsas</li>
        <li>invända mot behandling som grundas på berättigat intresse</li>
        <li>få ut uppgifter du själv har lämnat i ett maskinläsbart format (dataportabilitet)</li>
        <?php if ($ga): ?><li>när som helst dra tillbaka ditt samtycke till statistik, under Cookie-inställningar längst ned på sidan</li><?php endif; ?>
      </ul>
      <p>Kontakta oss på <?= $mail ?>. Vi svarar inom en månad och kan behöva be dig styrka din identitet först.</p>

      <h2 id="klagomal">Klagomål</h2>
      <p>Om du är missnöjd med hur vi behandlar dina uppgifter vill vi gärna att du hör av dig till oss först. Du har också rätt att lämna klagomål till Integritetsskyddsmyndigheten (IMY), imy.se.</p>

      <h2 id="andringar">Ändringar</h2>
      <p>Vi uppdaterar policyn när vi ändrar hur vi behandlar personuppgifter. Datumet högst upp visar när den senast ändrades.</p>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
