<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $swing = config('Swing'); ?>
<?= partial('partials/phero', [
    'eyebrow' => 'Inför ditt besök',
    'h1'      => 'Inför ditt besök hos Swing',
    'lede'    => 'Här hittar du det viktigaste att känna till före, under och efter din golftid.',
    'image'   => 'simulator',
    'trail'   => $trail,
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div class="prose">
      <h2>Innan du kommer</h2>
      <ul>
        <li>Tvätta golfklubborna och ta med rena inomhusskor.</li>
        <li>Ladda gärna ner TrackMan Golf-appen om du vill använda ditt konto.</li>
        <li>Använd endast de golfbollar och peggar som finns hos Swing.</li>
        <li>Se till att du har din bokningsbekräftelse och entrékod.</li>
      </ul>
      <p>Enstaka klubbor finns att låna. Om du behöver ett helt set ska du <a href="<?= page_path('kontakt') ?>">kontakta oss</a> i förväg.</p>

      <h2>När du kommer</h2>
      <p>Ange den <?= $swing->doorCodeDigits === 4 ? 'fyrsiffriga' : $swing->doorCodeDigits . '-siffriga' ?> entrékoden och avsluta med <strong>#</strong>. Koden fungerar <?= $swing->doorCodeWindowMinutes ?> minuter före och <?= $swing->doorCodeWindowMinutes ?> minuter efter bokningens starttid. Du hittar koden i bokningsappen och i bekräftelsemejlet.</p>
      <p>Om utrustningen inte redan är igång: tryck på den röda knappen. Den startar dator, lampa och TrackMan iO, och uppstarten tar ungefär två minuter. Om lampan över bollen inte lyser, tryck snabbt på den röda knappen. Ljuset justerar du med fjärrkontrollen.</p>
      <p>Logga in med TrackMan Golf eller spela som gäst.</p>

      <h2>Regler under spelet</h2>
      <ul>
        <li>Endast rena golfklubbor och rena inomhusskor.</li>
        <li>Endast Swings egna bollar och peggar.</li>
        <li>Luta inte bagar, klubbor eller dig själv mot simulatorernas tygväggar.</li>
        <li>Den som bokat måste vara i lokalen under hela den bokade tiden.</li>
        <li>Vagn- och bilförbud gäller året runt.</li>
        <li>Var rädd om utrustningen. Oaktsamhet som orsakar skada kan leda till ersättningsansvar.</li>
      </ul>

      <h2>När du lämnar</h2>
      <p>Om ingen väntar stänger du av simulatorn genom att hålla in den röda knappen i cirka två sekunder. Det tar ungefär två minuter innan allt har stängts ner. Om nästa sällskap väntar ska du bara logga ut – det gör du längst ner till vänster på startsidan i TrackMan.</p>
      <p>Plocka undan, lägg tillbaka bollar och peggar, släng skräp och kontrollera att ytterdörren är stängd.</p>

      <h2>Avbokning</h2>
      <p>Du kan avboka kostnadsfritt fram till <?= $swing->freeCancellationHours === 3 ? 'tre' : $swing->freeCancellationHours ?> timmar före bokningens start. <?= esc(config('Booking')->manageText) ?></p>

      <h2>Vid problem</h2>
      <p>Ring <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a>. Kontakt: <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a>.</p>
      <div class="btn-row">
        <?= partial('partials/book_button') ?>
        <a class="btn btn-ghost" href="tel:<?= esc($swing->phoneE164) ?>"><?= icon('phone', 20) ?>Ring vid problem</a>
      </div>
    </div>
    <div class="side">
      <?= partial('partials/facts', ['title' => 'Kom ihåg', 'items' => [
          ['icon' => 'info', 'title' => 'Koden och sedan #', 'text' => sv_count($swing->doorCodeDigits, true) . " siffror. Gäller {$swing->doorCodeWindowMinutes} minuter före och efter din starttid."],
          ['icon' => 'check', 'title' => 'Rena skor och klubbor', 'text' => 'Inomhusskor krävs i lokalen.'],
          ['icon' => 'target', 'title' => 'Swings bollar och peggar', 'text' => 'Egna bollar får inte användas.'],
          ['icon' => 'calendar', 'title' => "Avboka senast {$swing->freeCancellationHours} timmar före", 'text' => 'Då är det kostnadsfritt.'],
          ['icon' => 'phone', 'title' => $swing->phone, 'text' => 'Ring om något inte fungerar.', 'href' => 'tel:' . $swing->phoneE164],
      ]]) ?>
    </div>
  </div>
</section>
<section class="sec sec-alt" aria-labelledby="appar">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Ladda ner i förväg</p>
      <h2 id="appar">Apparna du har nytta av</h2>
    </div>
    <?= partial('partials/apps') ?>
  </div>
</section>
<?= $this->endSection() ?>
