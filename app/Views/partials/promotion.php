<?php
/**
 * Tidsbegränsat erbjudande under startsidans hero (Swing::$promotions).
 * Koden är vanlig markerbar text; kopieringsknappen är en JS-förbättring.
 */
$p  = $promotion;
$id = 'erbjudande-' . esc($p['id'], 'attr');
?>
<section class="sec promo" aria-labelledby="<?= $id ?>">
  <div class="wrap promo-in">
    <div class="promo-text">
      <p class="eyebrow"><?= esc($p['eyebrow']) ?></p>
      <h2 id="<?= $id ?>"><?= esc($p['title']) ?> <em><?= esc($p['titleEm']) ?></em></h2>
      <p class="lede"><?= esc($p['text']) ?></p>
    </div>
    <div class="promo-offer">
      <p class="promo-discount"><span class="promo-pct"><?= esc($p['discount']) ?></span><span><?= esc($p['discountLabel']) ?></span></p>
      <div class="promo-code">
        <p class="promo-code-label">Ange koden vid bokning</p>
        <p class="promo-code-value"><code><?= esc($p['code']) ?></code></p>
        <button type="button" class="promo-copy" data-copy="<?= esc($p['code'], 'attr') ?>" data-js-only hidden><?= icon('copy', 16) ?><span data-copy-label aria-live="polite">Kopiera koden</span></button>
      </div>
    </div>
    <div class="btn-row promo-cta"><?= partial('partials/book_button') ?></div>
  </div>
</section>
