<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing  = config('Swing');
$orders = config('Orders');
$direct = $orders->canSellWellnessDirect();
?>
<?= partial('partials/phero', [
    'eyebrow' => 'Friskvård & Epassi',
    'h1'      => 'Använd friskvårdsbidraget till golf',
    'lede'    => 'Vill du hålla igång svingen året runt? Hos Swing Golf & Lounge kan du använda friskvårdsbidrag för inomhusgolf – köp en friskvårdspott direkt hos oss eller via Epassi.',
    'image'   => 'simulator',
    'trail'   => $trail,
    'actions' => ($direct ? '<a class="btn btn-primary" href="#kop">' . icon('heart', 20) . 'Köp friskvårdspott</a>' : '')
        . '<a class="btn ' . ($direct ? 'btn-ghost' : 'btn-primary') . '" href="' . esc($swing->epassiUrl) . '" rel="noopener">Öppna Swing hos Epassi' . icon('external', 18) . '</a>',
]) ?>

<section class="sec" aria-labelledby="direkt">
  <div class="wrap">
    <div class="sec-head" id="kop">
      <p class="eyebrow">Friskvårdspott</p>
      <h2 id="direkt">Köp friskvård direkt</h2>
      <p class="lede">Vi erbjuder även spelpotter direkt hos Swing. Ange ditt personnummer i beställningen, så får du kvittot som underlag för friskvårdsbidraget. Friskvårdspotterna är personliga och giltiga i ett år. <a href="<?= page_path('kontakt') ?>">Kontakta oss</a> om du behöver veta mer om köp och underlag för friskvårdsbidraget.</p>
    </div>
    <?= partial('partials/order_pot_cards', [
        'slug'               => 'friskvard',
        'pots'               => $swing->wellnessPots,
        'form'               => $form ?? null,
        'interactive'        => $direct,
        'legend'             => 'Välj friskvårdspott',
        'withPersonalNumber' => true,
        'deliver'            => 'din kod och ditt kvitto',
    ]) ?>
    <?php if (! $direct): ?>
    <div style="margin-top:26px"><?= partial('partials/order_unavailable') ?></div>
    <?php endif; ?>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="epassi">
  <div class="wrap with-side">
    <div class="stack">
      <div class="prose">
        <h2 id="epassi">Köp via Epassi</h2>
        <p>Öppna Epassi-appen eller <a href="<?= esc($swing->epassiUrl) ?>" rel="noopener">vår sida hos Epassi</a> och välj ett alternativ som passar dig. Följ sedan köpinstruktionerna i Epassi.</p>
      </div>
      <?= partial('partials/pot_table', ['pots' => $swing->epassiPots, 'caption' => 'Epassi-alternativ', 'nameHeading' => 'Alternativ', 'showBonus' => false]) ?>
      <p class="note">Epassi-alternativen har egna belopp och skiljer sig från våra <a href="<?= page_path('spelpotter') ?>">ordinarie spelpotter</a>.</p>
      <div class="btn-row">
        <a class="btn btn-ghost" href="<?= esc($swing->epassiUrl) ?>" rel="noopener">Se Epassi-alternativ<?= icon('external', 18) ?></a>
      </div>
    </div>
    <div class="side">
      <div class="card">
        <img src="/assets/epassi-logo-light.png" alt="Epassi" width="603" height="150" style="height:30px;width:auto;margin-bottom:16px" loading="lazy" />
        <h2 class="mt-0" style="font-size:22px">Hur mycket får jag?</h2>
        <p>Beloppet för ditt friskvårdsbidrag bestäms av din arbetsgivare. Du ser ditt saldo i Epassi.</p>
      </div>
    </div>
  </div>
</section>

<?= partial('partials/cta_band', ['image' => 'lounge', 'title' => 'Träna golf året runt', 'text' => 'Boka en tid som passar dig. Vi har öppet ' . mb_strtolower($swing->hoursText) . '.']) ?>
<?= $this->endSection() ?>
