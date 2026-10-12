<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $swing = config('Swing'); ?>
<?= partial('partials/phero', [
    'eyebrow' => 'Tävlingar & aktiviteter',
    'h1'      => 'Golftävlingar och aktiviteter hos Swing',
    'lede'    => 'Golf är roligt. Golf tillsammans med andra är ännu roligare.',
    'trail'   => $trail,
]) ?>

<section class="sec">
  <div class="wrap">
    <div class="prose" style="margin-bottom:36px">
      <p>På Swing Golf &amp; Lounge vill vi skapa en naturlig mötesplats för golfare i Karlskrona och Blekinge. Därför arrangerar vi både öppna och privata tävlingar.</p>
    </div>

    <h2 id="aktuella" style="font-size:clamp(26px,3.4vw,36px);margin-bottom:8px">Aktuella tävlingar</h2>
    <p class="lede" style="margin-bottom:24px">Här hittar du aktuella tävlingar, datum, anmälningsinformation och resultat. Vi arbetar med veckotävlingar, ligor, tourer och utmaningar som närmast hål och längsta drive.</p>
    <?php if (! empty($currentCompetitions)): ?>
    <div class="grid g3">
      <?php foreach ($currentCompetitions as $c): ?>
      <?= partial('partials/competition_card', ['c' => $c]) ?>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty">
      <h3>Inga tävlingar är publicerade just nu</h3>
      <p>Nya tävlingar läggs upp här när de är klara. Följ oss på <a href="<?= esc($swing->socials['instagram']) ?>" rel="noopener">Instagram</a> eller <a href="<?= esc($swing->socials['facebook']) ?>" rel="noopener">Facebook</a> så missar du inget – eller ordna en egen tävling med ditt sällskap.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="sec sec-alt">
  <div class="wrap cols-2">
    <div class="prose">
      <h2>Öppna och privata tävlingar</h2>
      <p>Vissa aktiviteter är öppna för alla. Andra arrangeras för särskilda företag, golfklubbar eller grupper.</p>
      <p>Läs också om <a href="<?= page_path('team-swing') ?>">Team Swing</a>, vårt engagemang för golfare som satsar.</p>
    </div>
    <div class="prose">
      <h2>Anordna en egen tävling</h2>
      <p>Samla kompisarna, kollegorna eller föreningen för en egen golfutmaning. Vi hjälper gärna till med tävlingsupplägg och möjlighet till mat och dryck.</p>
      <div class="btn-row"><a class="btn btn-primary" href="#egen"><?= icon('trophy', 20) ?>Fråga om egen tävling</a></div>
    </div>
  </div>
</section>

<?php if (! empty($pastCompetitions)): ?>
<section class="sec" aria-labelledby="tidigare">
  <div class="wrap">
    <h2 id="tidigare" style="font-size:clamp(24px,3vw,32px);margin-bottom:20px">Tidigare tävlingar</h2>
    <div class="grid g3">
      <?php foreach ($pastCompetitions as $c): ?>
      <?= partial('partials/competition_card', ['c' => $c]) ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="sec<?= empty($pastCompetitions) ? '' : ' sec-alt' ?>" aria-labelledby="egen-rubrik">
  <div class="wrap" style="max-width:820px">
    <div class="sec-head" id="egen">
      <p class="eyebrow">Egen tävling</p>
      <h2 id="egen-rubrik">Fråga om egen tävling</h2>
    </div>
    <?= partial('partials/inquiry_form', ['type' => 'tavling', 'slug' => 'tavlingar', 'form' => $form ?? null, 'submitLabel' => 'Skicka förfrågan']) ?>
  </div>
</section>
<?= $this->endSection() ?>
