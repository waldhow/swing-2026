<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $swing = config('Swing'); ?>
<header class="phero phero-plain">
  <div class="hero-scrim"></div>
  <div class="hero-in wrap">
    <p class="eyebrow">Tack!</p>
    <h1>Vi har tagit emot din förfrågan</h1>
    <p class="lede">Vi återkommer till dig så snart vi kan, oftast inom ett par vardagar. Är det bråttom – ring oss på <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a>.</p>
    <div class="btn-row"><a class="btn btn-ghost" href="/">Till startsidan</a><?= partial('partials/book_button') ?></div>
  </div>
</header>
<?= $this->endSection() ?>
