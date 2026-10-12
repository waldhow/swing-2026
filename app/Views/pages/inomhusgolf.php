<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing   = config('Swing');
$booking = config('Booking');
?>
<?= partial('partials/phero', [
    'eyebrow' => 'Inomhusgolf i ' . $swing->locality,
    'h1'      => 'Inomhusgolf i Karlskrona – spela året runt',
    'lede'    => 'Varför låta väder, mörker eller årstid bestämma när du ska spela golf? Hos Swing Golf & Lounge i Nättraby spelar du inomhusgolf året runt.',
    'image'   => 'oversikt',
    'trail'   => $trail,
    'actions' => partial('partials/book_button', ['label' => 'Boka simulator']),
    'strip'   => [
        ['icon' => 'target', 'title' => "{$swing->simulatorCount} × {$swing->simulatorTech}", 'text' => 'Simulatorer'],
        ['icon' => 'clock', 'title' => $swing->hoursShort, 'text' => 'Öppet alla dagar'],
        ['icon' => 'users', 'title' => "{$swing->recommendedPlayers} spelare", 'text' => 'Rekommenderat per simulator'],
        ['icon' => 'card', 'title' => 'Från ' . kr(service('pricing')->lowestPrice()), 'text' => 'Per simulator och timme'],
    ],
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div class="prose">
      <p>Våra <?= $swing->simulatorCount === 4 ? 'fyra' : $swing->simulatorCount ?> <?= esc($swing->simulatorTech) ?>-simulatorer ger dig möjlighet att spela, träna och umgås i en miljö som kombinerar modern teknik med klassisk golfklubbskänsla.</p>

      <h2>Golf för både träning och nöje</h2>
      <p>Vill du utveckla din sving kan du använda TrackMans mätdata för att förstå dina slag bättre. Vill du hellre spela en virtuell golfrunda med kompisarna går det lika bra.</p>
      <p>Du kan boka en simulator för dig själv eller dela tiden med andra. Vi rekommenderar <?= esc($swing->recommendedPlayers) ?> spelare per simulator för en bra upplevelse.</p>

      <h2>Fyra golfsimulatorer</h2>
      <p>Swing har fyra simulatorer med <?= esc($swing->simulatorTech) ?>. Simulator 1 och 3 har något större slagdukar än simulator 2 och 4. Alla är byggda för inomhusgolf. <a href="<?= page_path('trackman-io') ?>">Läs mer om simulatorerna</a>.</p>

      <h2>Så fungerar besöket</h2>
      <ol>
        <li>Boka en ledig tid via <?= esc($booking->provider) ?>.</li>
        <li>Få entrékod i bokningsbekräftelsen.</li>
        <li>Ta med rena klubbor och rena inomhusskor. Swings bollar och peggar finns på plats.</li>
        <li>Starta simulatorn vid behov och logga in i TrackMan Golf eller spela som gäst.</li>
      </ol>
      <p>Lokalen är normalt obemannad. Instruktioner finns på plats och du kan ringa oss på <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a> om du behöver hjälp. Allt du behöver veta finns under <a href="<?= page_path('infor-besoket') ?>">Inför ditt besök</a>.</p>

      <h2>En plats att umgås på</h2>
      <p>Mellan slagen kan du slå dig ner i vår lounge, ta en kaffe eller köpa något från kiosken. Det går även att beställa mat från <?= partner('Nättraby Vägkrog') ?>.</p>
    </div>
    <div class="side">
      <div class="img-frame"><?= picture('lounge', '320px') ?></div>
    </div>
  </div>
</section>

<?= partial('partials/cta_band', ['image' => 'simulator', 'title' => 'Boka din golftid', 'secondary' => ['label' => 'Se priser', 'url' => page_path('priser')]]) ?>
<?= $this->endSection() ?>
