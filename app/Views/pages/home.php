<?= $this->extend('layouts/main') ?>

<?= $this->section('head') ?>
<link rel="preload" as="image" type="image/webp" imagesrcset="/assets/img/hero-lounge-800.webp 800w, /assets/img/hero-lounge-1280.webp 1280w, /assets/img/hero-lounge-1920.webp 1920w" imagesizes="100vw" />
<link rel="stylesheet" href="<?= asset('assets/gallery.css') ?>" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$swing   = config('Swing');
$booking = config('Booking');
$pricing = service('pricing');
$images  = config('Images');
?>
<header class="hero hero-full">
  <div class="hero-bg"><?= picture('hero', '100vw', true) ?></div>
  <div class="hero-scrim"></div>
  <div class="hero-glow"></div>
  <div class="hero-in wrap">
    <?php /* Dekorativ — menyn bär logotypen med alt-text. Döljs på mobil så att knapparna syns direkt. */ ?>
    <img class="hero-logo" src="<?= asset('assets/logo.svg') ?>" alt="" width="701" height="540" aria-hidden="true" />
    <p class="eyebrow"><?= esc($swing->locality) ?> · <?= esc($swing->municipality) ?></p>
    <h1>Golfen tar inte paus för <em>vädret</em></h1>
    <p class="lede">Inomhusgolf med <?= esc($swing->simulatorTech) ?> i <?= esc($swing->municipality) ?> – året runt.</p>
    <div class="btn-row">
      <?= partial('partials/book_button') ?>
      <a class="btn btn-ghost" href="<?= page_path('spelpotter') ?>"><?= icon('wallet', 20) ?>Köp spelpott</a>
    </div>
    <?= partial('partials/app_badges', ['class' => 'hero-apps']) ?>
  </div>
  <div class="hero-strip">
    <ul class="wrap" aria-label="Kort om Swing">
      <li><?= icon('target', 22) ?><span><strong><?= $swing->simulatorCount ?> × <?= esc($swing->simulatorTech) ?></strong><span>Simulatorer</span></span></li>
      <li><?= icon('clock', 22) ?><span><strong><?= esc($swing->hoursShort) ?></strong><span>Öppet alla dagar</span></span></li>
      <li><?= icon('coffee', 22) ?><span><strong>Lounge</strong><span>I klubbhusanda</span></span></li>
      <li><?= icon('card', 22) ?><span><strong>Från <?= kr($pricing->lowestPrice()) ?></strong><span>Per simulator och timme</span></span></li>
    </ul>
  </div>
</header>

<section class="sec sec-alt" aria-labelledby="appar">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Apparna</p>
      <h2 id="appar">Boka i Sweetspot. Spela och analysera med TrackMan.</h2>
      <p class="lede">Enklast bokar och betalar du i <?= esc($booking->appName) ?>-appen – där har du också din dörrkod nära till hands. I simulatorn loggar du in med TrackMan Golf-appen, eller spelar som gäst.</p>
    </div>
    <?= partial('partials/apps') ?>
  </div>
</section>


<section class="sec" aria-labelledby="valkommen">
  <div class="wrap">
    <div class="prose">
      <p class="eyebrow">Välkommen till Swing</p>
      <h2 id="valkommen">Modern golfteknik i ett klassiskt klubbrum</h2>
      <p>Välkommen till <?= esc($swing->name) ?> i <?= esc($swing->locality) ?>. Här möts modern golfteknik och känslan av ett klassiskt golfklubbrum. Spela en runda med vännerna, träna på din sving eller utmana kollegorna i en tävling.</p>
      <p>Med <?= $swing->simulatorCount === 4 ? 'fyra' : $swing->simulatorCount ?> <?= esc($swing->simulatorTech) ?>-simulatorer, en ombonad lounge och öppet alla dagar mellan 06.00 och 00.00 finns det gott om möjligheter att spela golf på dina villkor.</p>
    </div>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="simulatorer">
  <div class="wrap split">
    <div class="prose">
      <p class="eyebrow">Simulatorerna</p>
      <h2 id="simulatorer">Fyra simulatorer. Gott om golfglädje.</h2>
      <p>Hos oss spelar du med <?= esc($swing->simulatorTech) ?>, teknik som är utvecklad för inomhusgolf. Oavsett om du vill spela virtuella golfbanor, utveckla ditt spel eller ha en trevlig stund med vännerna finns det plats för dig på Swing.</p>
      <p>Vi rekommenderar <?= esc($swing->recommendedPlayers) ?> spelare per simulator, men du kan självklart boka ensam.</p>
      <div class="btn-row">
        <a class="btn btn-ghost" href="<?= page_path('trackman-io') ?>">Om TrackMan iO och simulatorerna<?= icon('arrowR', 20) ?></a>
      </div>
    </div>
    <div class="img-frame"><?= picture('simulator', '(max-width: 900px) 100vw, 560px') ?></div>
  </div>
</section>

<section class="sec" aria-labelledby="loungen">
  <div class="wrap split rev">
    <div class="prose">
      <p class="eyebrow">Loungen</p>
      <h2 id="loungen">Mer än bara golfsimulatorer</h2>
      <p>Vi ville skapa något som känns som en riktig golfklubb. Därför har vi byggt en lounge inspirerad av klassiska skotska klubbrum, med skinnsoffor, fåtöljer, mörka toner och gott om plats att umgås.</p>
      <p>Ta en kaffe, slå dig ner med kompisarna och njut av golfen – oavsett årstid.</p>
      <div class="btn-row">
        <a class="btn btn-ghost" href="<?= page_path('om-oss') ?>">Läs mer om Swing<?= icon('arrowR', 20) ?></a>
      </div>
    </div>
    <div class="img-frame"><?= picture('lounge', '(max-width: 900px) 100vw, 560px') ?></div>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="priser">
  <div class="wrap with-side">
    <div>
      <div class="sec-head">
        <p class="eyebrow">Priser</p>
        <h2 id="priser">Du betalar för simulatorn – inte per person</h2>
        <p class="lede">Delar ni på en simulator delar ni också på kostnaden. Är ni fyra blir det från <?= kr($pricing->perPerson($pricing->lowestPrice())) ?> per person och timme.</p>
      </div>
      <?= partial('partials/price_table') ?>
      <div class="btn-row">
        <?= partial('partials/book_button') ?>
        <a class="btn btn-ghost" href="<?= page_path('priser') ?>">Alla priser<?= icon('arrowR', 20) ?></a>
      </div>
    </div>
    <div class="side card">
      <h3>Kostnadsfri avbokning</h3>
      <p>Du kan avboka utan kostnad fram till <?= $swing->freeCancellationHours ?> timmar före bokningens start. <?= esc($booking->manageText) ?></p>
    </div>
  </div>
</section>

<section class="sec" aria-labelledby="spelpotter">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Spelpotter</p>
      <h2 id="spelpotter">Spela mer för pengarna</h2>
      <p class="lede">Med en spelpott får du extra spelvärde som du använder när du bokar tid. Spelpotterna är giltiga i ett år.</p>
    </div>
    <?php foreach ($homeCampaigns ?? [] as $campaign): ?>
    <div class="campaign" style="margin-bottom:18px">
      <span class="card-ic" style="margin:0"><?= icon('star', 22) ?></span>
      <div>
        <h3><?= esc($campaign['title']) ?></h3>
        <?php if (! empty($campaign['summary'])): ?><p><?= esc($campaign['summary']) ?></p><?php endif; ?>
        <p class="terms">Gäller t.o.m. <?= esc(sv_date($campaign['valid_until'], false)) ?>. <a href="<?= page_path('spelpotter') ?>#kampanj-<?= (int) $campaign['id'] ?>">Villkor och beställning</a></p>
      </div>
    </div>
    <?php endforeach; ?>
    <div class="pots">
      <?php foreach ($swing->creditPots as $i => $pot): ?>
      <a class="offer<?= ['', ' burgundy', ' bottle'][$i % 3] ?>" href="<?= page_path('spelpotter') ?>#pott-<?= $pot['pay'] ?>" style="text-decoration:none;display:block">
        <span class="sheen"></span>
        <span class="name"><?= esc($pot['name']) ?></span>
        <span class="get"><span class="amt"><?= kr($pot['value'], false) ?></span><span>kr i spelvärde</span></span>
        <span class="pay">
          <span><span class="l">Du betalar</span><span class="v" style="display:block"><?= kr($pot['pay']) ?></span></span>
          <span class="bonus"><?= icon('gift', 13) ?>+<?= kr($pot['value'] - $pot['pay']) ?></span>
        </span>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="btn-row">
      <a class="btn btn-primary" href="<?= page_path('spelpotter') ?>"><?= icon('wallet', 20) ?>Köp spelpott</a>
      <a class="btn btn-ghost" href="<?= page_path('presentkort') ?>"><?= icon('gift', 20) ?>Presentkort</a>
    </div>
  </div>
</section>

<section class="sec sec-walnut" aria-labelledby="foretag">
  <div class="wrap split">
    <div class="prose">
      <p class="eyebrow">Företag och grupper</p>
      <h2 id="foretag">Samla kollegorna på Swing</h2>
      <p>Vi hjälper företag, föreningar, golfklubbar och kompisgäng att ordna aktiviteter. Hos oss kan cirka <?= esc($swing->eventGolfers) ?> personer spela golf och upp till <?= $swing->eventMaxGuests ?> personer vistas i lokalen.</p>
      <p>Vi hjälper gärna till med tävling, catering och personal på plats efter överenskommelse.</p>
      <div class="btn-row">
        <a class="btn btn-primary" href="<?= page_path('foretagsevent') ?>#offert"><?= icon('mail', 20) ?>Begär offert</a>
        <a class="btn btn-ghost" href="<?= page_path('gruppbokning') ?>">Gruppbokning<?= icon('arrowR', 20) ?></a>
      </div>
    </div>
    <div class="img-frame"><?= picture('oversikt', '(max-width: 900px) 100vw, 560px') ?></div>
  </div>
</section>

<section class="sec" aria-labelledby="tavlingar">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Tävlingar</p>
      <h2 id="tavlingar">Tävla, utmana och spela tillsammans</h2>
      <p class="lede">Vi arrangerar både öppna och privata golftävlingar. Håll utkik efter veckotävlingar, ligor, tourer och utmaningar som närmast hål och längsta drive.</p>
    </div>
    <?php if (! empty($currentCompetitions)): ?>
    <div class="grid g3">
      <?php foreach ($currentCompetitions as $c): ?>
      <?= partial('partials/competition_card', ['c' => $c]) ?>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="btn-row">
      <a class="btn btn-ghost" href="<?= page_path('tavlingar') ?>"><?= icon('trophy', 20) ?>Tävlingar och aktiviteter</a>
    </div>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="galleri">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Hos oss</p>
      <h2 id="galleri">En titt in i lokalen</h2>
    </div>
    <div class="gal">
      <?php foreach ($images->gallery as $key => $slot): $img = $images->images[$key]; ?>
      <div class="<?= esc($slot) ?>">
        <a class="gal-item" href="/<?= esc($img['src']) ?>" data-caption="<?= esc($img['alt']) ?>">
          <?= picture($key, '(max-width: 820px) 100vw, 50vw') ?>
          <span class="sr-only">Visa större bild</span>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="sec" aria-labelledby="fragor">
  <div class="wrap contact">
    <div>
      <p class="eyebrow">Bra att veta</p>
      <h2 id="fragor" style="font-size:clamp(28px,3.6vw,40px);margin:14px 0 18px">Vanliga frågor</h2>
      <?= partial('partials/faq_list', ['items' => $faqItems]) ?>
      <div class="btn-row">
        <a class="btn btn-ghost" href="<?= page_path('vanliga-fragor') ?>">Alla frågor och svar<?= icon('arrowR', 20) ?></a>
        <a class="btn btn-ghost" href="<?= page_path('infor-besoket') ?>">Inför ditt besök<?= icon('arrowR', 20) ?></a>
      </div>
    </div>
    <div>
      <p class="eyebrow">Hitta hit</p>
      <h2 style="font-size:clamp(28px,3.6vw,40px);margin:14px 0 18px">Hitta till oss</h2>
      <p class="lede" style="margin-bottom:18px">Du hittar <?= esc($swing->name) ?> på <?= esc($swing->street) ?> i <?= esc($swing->locality) ?>, utanför <?= esc($swing->municipality) ?>.</p>
      <?= partial('partials/contact_card') ?>
    </div>
  </div>
</section>

<?= partial('partials/cta_band', ['image' => 'bar', 'title' => 'Boka din nästa golfrunda', 'secondary' => ['label' => 'Se priser', 'url' => page_path('priser')]]) ?>

<div class="lb" role="dialog" aria-modal="true" aria-label="Bildvisning">
  <img src="" alt="" />
  <div class="lb-caption"></div>
  <button type="button" class="lb-btn lb-close" aria-label="Stäng">✕</button>
  <button type="button" class="lb-btn lb-prev" aria-label="Föregående bild">‹</button>
  <button type="button" class="lb-btn lb-next" aria-label="Nästa bild">›</button>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= asset('assets/gallery.js') ?>" defer></script>
<?= $this->endSection() ?>
