<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $swing = config('Swing'); ?>
<?= partial('partials/phero', [
    'eyebrow' => 'Om oss',
    'h1'      => 'En passion för golf. En kärlek till klubbkänslan.',
    'lede'    => "Swing Golf & Lounge startade {$swing->foundingYear} och föddes ur ett gemensamt intresse för golf och en idé om att skapa något tillsammans.",
    'image'   => 'lounge',
    'trail'   => $trail,
    'strip'   => [
        ['icon' => 'flag', 'title' => "Sedan {$swing->foundingYear}", 'text' => "I {$swing->locality}"],
        ['icon' => 'target', 'title' => "{$swing->simulatorCount} × {$swing->simulatorTech}", 'text' => 'Simulatorer'],
        ['icon' => 'coffee', 'title' => 'Klubbhusanda', 'text' => 'Skinnsoffor och fåtöljer'],
        ['icon' => 'pin', 'title' => $swing->locality, 'text' => "Strax utanför {$swing->municipality}"],
    ],
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div class="prose">
      <p>Vi som står bakom Swing, Mats och Pontus, spelar själva golf och ville bygga en plats där golfen kan leva året runt. Men vi ville inte bara skapa en anläggning med simulatorer. Vi ville skapa en plats där man trivs.</p>

      <h2>Inspiration från Skottlands klassiska golfklubbar</h2>
      <p>Vi uppskattar känslan i klassiska skotska klubbhus: mörka toner, skinnmöbler, en ombonad atmosfär och gemenskapen när golfare samlas. Lite som en trevlig pub, fast med golfen i centrum.</p>
      <p>Den känslan vill vi att du ska möta när du kommer till Swing. Här finns lounge, fåtöljer, soffor och en bardisk där du kan ta en kaffe mellan slagen.</p>

      <h2>Från AboutGolf till TrackMan iO</h2>
      <p>Anläggningen har utvecklats genom åren. Vi använde tidigare simulatorer från AboutGolf och har sedan gått över till fyra <?= esc($swing->simulatorTech) ?>-simulatorer.</p>
      <p>Tekniken är viktig för oss, men det är minst lika viktigt att det är enkelt att komma hit, spela golf och ha trevligt tillsammans.</p>

      <h2>En mötesplats för golfare i Blekinge</h2>
      <p>Vi samarbetar med golfklubbar och andra verksamheter i närområdet, bland annat <?= implode(', ', array_map('partner', array_slice($swing->partners, 0, -1))) ?> och <?= partner(end($swing->partners)) ?>.</p>
      <p>Oavsett om du vill träna seriöst, spela med vänner eller ordna en tävling är du välkommen till Swing.</p>
      <p><strong>Vi ses på Swing!</strong></p>

      <div class="btn-row">
        <?= partial('partials/book_button', ['label' => 'Boka tid']) ?>
        <a class="btn btn-ghost" href="<?= page_path('kontakt') ?>">Kontakta oss<?= icon('arrowR', 20) ?></a>
      </div>
    </div>
    <div class="side">
      <div class="img-frame"><?= picture('bar', '320px') ?></div>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
