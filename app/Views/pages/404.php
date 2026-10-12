<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<header class="phero phero-plain">
  <div class="hero-scrim"></div>
  <div class="hero-in wrap">
    <p class="eyebrow">404</p>
    <h1>Sidan hittades inte</h1>
    <p class="lede">Sidan du letade efter finns inte, eller har flyttat. Här är några vägar vidare.</p>
    <div class="btn-row"><?= partial('partials/book_button') ?><a class="btn btn-ghost" href="/">Till startsidan</a></div>
  </div>
</header>
<section class="sec-tight">
  <div class="wrap">
    <div class="nf-links">
      <?php foreach (['priser', 'spelpotter', 'presentkort', 'foretagsevent', 'infor-besoket', 'kontakt'] as $slug): ?>
      <a href="<?= page_path($slug) ?>"><?= esc(config('Pages')->label($slug)) ?><?= icon('arrowR', 18) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
