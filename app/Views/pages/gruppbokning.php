<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $swing = config('Swing'); ?>
<?= partial('partials/phero', [
    'eyebrow' => 'Gruppbokning',
    'h1'      => 'Gruppbokning – spela golf tillsammans',
    'lede'    => 'Samla kompisarna, klubben eller föreningen för en stund med golf, tävling och gemenskap på Swing Golf & Lounge.',
    'image'   => 'lounge',
    'trail'   => $trail,
    'actions' => '<a class="btn btn-primary" href="#fraga">' . icon('users', 20) . 'Fråga om gruppbokning</a>',
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div class="prose">
      <p>Vi har fyra <?= esc($swing->simulatorTech) ?>-simulatorer och en stor lounge där ni kan umgås före, under och efter spelet.</p>

      <h2>För små och stora sällskap</h2>
      <p>Ni kan spela tillsammans i en simulator eller kontakta oss om ni vill boka flera. Vi rekommenderar <?= esc($swing->recommendedPlayers) ?> golfare per simulator. För större grupper kan vi ordna upplägg för cirka <?= esc($swing->eventGolfers) ?> spelande personer och upp till <?= $swing->eventMaxGuests ?> gäster i lokalen.</p>
      <p>Är ni fyra eller färre räcker det att <a href="<?= page_path('boka') ?>">boka en simulator</a> som vanligt.</p>

      <h2>Skapa er egen tävling</h2>
      <p>Vill ni tävla i närmast hål, längsta drive eller spela en golfrunda tillsammans? Vi hjälper gärna till att planera en privat tävling för er grupp.</p>

      <h2>Kombinera med mat</h2>
      <p>Vi kan hjälpa till med catering och andra önskemål inför gruppbokningen.</p>
    </div>
    <div class="side card">
      <h2 class="mt-0" style="font-size:22px">Företag?</h2>
      <p>Planerar ni en kickoff, personalaktivitet eller kundevent? Läs om <a href="<?= page_path('foretagsevent') ?>">företagsevent och Swing After Work</a>.</p>
    </div>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="fraga-rubrik">
  <div class="wrap" style="max-width:820px">
    <div class="sec-head" id="fraga">
      <p class="eyebrow">Gruppbokning</p>
      <h2 id="fraga-rubrik">Fråga om gruppbokning</h2>
      <p class="lede">Berätta ungefär hur många ni är och när ni vill komma, så återkommer vi med ett förslag.</p>
    </div>
    <?= partial('partials/inquiry_form', ['type' => 'grupp', 'slug' => 'gruppbokning', 'form' => $form ?? null, 'submitLabel' => 'Skicka förfrågan']) ?>
  </div>
</section>
<?= $this->endSection() ?>
