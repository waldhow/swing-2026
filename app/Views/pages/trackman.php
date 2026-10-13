<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $swing = config('Swing'); ?>
<?= partial('partials/phero', [
    'eyebrow' => 'TrackMan iO & simulatorer',
    'h1'      => 'Fyra golfsimulatorer med TrackMan iO',
    'lede'    => 'Hos Swing Golf & Lounge spelar du med TrackMan iO – en simulatorlösning utvecklad för golf inomhus.',
    'image'   => 'simulator',
    'trail'   => $trail,
    'actions' => partial('partials/book_button', ['label' => 'Boka en TrackMan-simulator']),
    'strip'   => [
        ['icon' => 'target', 'title' => $swing->simulatorTech, 'text' => 'I alla ' . sv_count($swing->simulatorCount) . ' simulatorer'],
        ['icon' => 'monitor', 'title' => $swing->simulatorScreens[1] . ' m', 'text' => 'Slagduk, simulator 1 och 3'],
        ['icon' => 'monitor', 'title' => $swing->simulatorScreens[2] . ' m', 'text' => 'Slagduk, simulator 2 och 4'],
        ['icon' => 'chart', 'title' => 'Slagdata', 'text' => 'Via TrackMan Golf-appen'],
    ],
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div class="prose">
      <p>Med fyra simulatorer kan vi välkomna dig som vill träna på egen hand och större grupper som vill spela tillsammans.</p>

      <h2>Teknik för dig som vill utvecklas</h2>
      <p>TrackMan iO ger information om dina golfslag och hjälper dig att analysera ditt spel. Det passar dig som vill förstå dina slag bättre och fortsätta utvecklas även under vintern.</p>
      <p>Logga in med TrackMan Golf-appen om du vill spara dina slag på ditt eget konto, eller spela som gäst.</p>
      <p>Vad betyder Ball Speed, Smash Factor och Club Path – och vad är ett bra värde för just dig? I vår guide går vi igenom siffrorna, jämför olika nivåer och ger dig träningspass att göra hos oss.</p>
      <div class="btn-row"><a class="btn btn-ghost" href="<?= page_path('trackman-guide') ?>">Läs TrackMan-guiden<?= icon('arrowR', 20) ?></a></div>

      <h2>Spela golf tillsammans</h2>
      <p>Simulatorgolf behöver inte handla om siffror. Samla kompisarna, välj en virtuell golfupplevelse och spela tillsammans. Vi rekommenderar <?= esc($swing->recommendedPlayers) ?> personer per simulator.</p>

      <h2>Våra simulatorbås</h2>
      <p>Simulator 1 och 3 har slagdukar på cirka 5,5 × 3 meter, medan simulator 2 och 4 har cirka 4,5 × 3 meter. Alla fyra använder TrackMan iO.</p>
    </div>
    <div class="side">
      <div class="table-wrap">
        <table class="dtable">
          <caption>Slagdukarnas storlek</caption>
          <thead><tr><th scope="col">Simulator</th><th scope="col">Slagduk (ca, b × h)</th></tr></thead>
          <tbody>
            <?php foreach ($swing->simulatorScreens as $number => $size): ?>
            <tr><th scope="row">Simulator <?= (int) $number ?></th><td><?= esc($size) ?> m</td></tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="note">Alla simulatorer har <?= esc($swing->simulatorTech) ?>.</p>
    </div>
  </div>
</section>

<section class="sec sec-alt">
  <div class="wrap split">
    <div class="prose">
      <h2>En lounge att återvända till</h2>
      <p>Här finns också skinnsoffor, fåtöljer, bardisk, kaffe, dryck och snacks för en paus mellan slagen.</p>
      <div class="btn-row"><a class="btn btn-ghost" href="<?= page_path('inomhusgolf') ?>">Om inomhusgolf hos Swing<?= icon('arrowR', 20) ?></a></div>
    </div>
    <div class="img-frame"><?= picture('lounge', '(max-width: 900px) 100vw, 560px') ?></div>
  </div>
</section>

<?= partial('partials/cta_band', ['image' => 'bar', 'title' => 'Boka en simulator', 'secondary' => ['label' => 'Inför ditt besök', 'url' => page_path('infor-besoket')]]) ?>
<?= $this->endSection() ?>
