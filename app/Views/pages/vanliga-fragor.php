<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= partial('partials/phero', [
    'eyebrow' => 'Vanliga frågor',
    'h1'      => 'Vanliga frågor om Swing Golf & Lounge',
    'lede'    => 'Svar om bokning, priser, klädsel, entrékod, TrackMan, Epassi, presentkort och företag.',
    'trail'   => $trail,
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div>
      <?php foreach ($faqGroups as $heading => $items): ?>
      <section class="faq-group" aria-labelledby="g-<?= esc(url_title($heading, '-', true)) ?>">
        <h2 id="g-<?= esc(url_title($heading, '-', true)) ?>"><?= esc($heading) ?></h2>
        <?= partial('partials/faq_list', ['items' => $items]) ?>
      </section>
      <?php endforeach; ?>
    </div>
    <div class="side stack">
      <?= partial('partials/facts') ?>
      <div class="card">
        <h2 class="mt-0" style="font-size:22px">Hittar du inte svaret?</h2>
        <p>Läs <a href="<?= page_path('infor-besoket') ?>">Inför ditt besök</a> eller <a href="<?= page_path('kontakt') ?>">kontakta oss</a>.</p>
        <div class="btn-row"><?= partial('partials/book_button', ['label' => 'Boka tid']) ?></div>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
