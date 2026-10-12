<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $swing = config('Swing'); ?>
<?= partial('partials/phero', [
    'eyebrow' => 'Företagsevent',
    'h1'      => 'Företagsevent i Karlskrona – golf och gemenskap',
    'lede'    => 'Letar ni efter en företagsaktivitet som passar både vana golfare och kollegor som är nya på golf? Välkommen till Swing Golf & Lounge i Nättraby – inomhusgolf och en avslappnad loungemiljö inspirerad av klassiska skotska golfklubbar.',
    'image'   => 'oversikt',
    'trail'   => $trail,
    'actions' => '<a class="btn btn-primary" href="#offert">' . icon('mail', 20) . 'Begär offert</a>',
    'strip'   => [
        ['icon' => 'target', 'title' => "Cirka {$swing->eventGolfers}", 'text' => 'Golfare samtidigt'],
        ['icon' => 'users', 'title' => "Upp till {$swing->eventMaxGuests}", 'text' => 'Gäster i lokalen'],
        ['icon' => 'coffee', 'title' => 'Catering', 'text' => 'Efter överenskommelse'],
        ['icon' => 'mail', 'title' => 'Offert', 'text' => 'Skräddarsytt upplägg'],
    ],
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div class="prose">
      <p>Med fyra <?= esc($swing->simulatorTech) ?>-simulatorer finns plats för cirka <?= esc($swing->eventGolfers) ?> golfare och upp till <?= $swing->eventMaxGuests ?> personer i lokalen. Vi hjälper gärna till med ett upplägg som passar gruppen.</p>

      <h2>Swing After Work</h2>
      <p><strong>Lämna kontoret. Samla kollegorna. Låt golfen börja.</strong></p>
      <p>En avslappnad aktivitet med golf, umgänge och möjlighet till mat och dryck. Vi anpassar längd, antal simulatorer och tävlingsform efter era önskemål. Pris enligt offert.</p>

      <h2>Tävling eller hela lokalen</h2>
      <p>Vill ni tävla i närmast hål, längsta drive eller andra utmaningar passar det bra till kickoff, personalaktivitet och kundevent. Ni kan också boka hela anläggningen med fyra simulatorer och lounge. Berätta vad ni har tänkt er så tar vi fram ett förslag och en offert.</p>

      <h2>Mat och dryck</h2>
      <p>Vi kan hjälpa till att ordna catering, bland annat tillsammans med <?= partner('Nättraby Vägkrog') ?>. Utformningen anpassas efter er grupp och vad som är möjligt vid bokningstillfället.</p>

      <h2>Inte bara för företag</h2>
      <p>Vi välkomnar även golfklubbar, föreningar, skolor och kompisgäng som vill samlas kring golfen. Läs mer om <a href="<?= page_path('gruppbokning') ?>">gruppbokning</a>.</p>
    </div>
    <div class="side"><div class="img-frame"><?= picture('lounge', '320px') ?></div></div>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="offert-rubrik">
  <div class="wrap" style="max-width:820px">
    <div class="sec-head" id="offert">
      <p class="eyebrow">Kontakta oss för offert</p>
      <h2 id="offert-rubrik">Berätta om ert event</h2>
      <p class="lede">Berätta hur många ni är, när ni vill komma och vilken aktivitet ni har tänkt er. Vi återkommer med ett förslag.</p>
    </div>
    <?= partial('partials/inquiry_form', ['type' => 'foretag', 'slug' => 'foretagsevent', 'form' => $form ?? null, 'submitLabel' => 'Begär offert']) ?>
  </div>
</section>
<?= $this->endSection() ?>
