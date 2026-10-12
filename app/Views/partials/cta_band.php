<?php
/**
 * Avslutande CTA-band som ett vykort från klubbhuset: text och knappar till
 * vänster, ett godkänt foto som tonar in från höger (överst på mobil).
 *   $title, $text, $secondary = ['label' => ..., 'url' => ...] (valfri),
 *   $bookLabel (valfri), $image (nyckel i Config\Images, standard "lounge"), $id
 * Primärknappen är alltid bokning.
 */
$swing = config('Swing');
$id    = 'cta-' . ($id ?? 'band');
?>
<section class="sec-tight cta-band" aria-labelledby="<?= esc($id) ?>">
  <div class="wrap">
    <div class="cta-card">
      <div class="cta-photo" aria-hidden="true"><?= picture($image ?? 'lounge', '(max-width: 760px) 100vw, 560px', false, '') ?></div>
      <div class="cta-body">
        <h2 id="<?= esc($id) ?>"><?= esc($title ?? 'Redo för en runda?') ?></h2>
        <p><?= esc($text ?? "Boka en simulator och spela när det passar dig – vi har öppet {$swing->hoursShort} alla dagar.") ?></p>
        <div class="btn-row">
          <?= partial('partials/book_button', ['label' => $bookLabel ?? 'Boka golftid']) ?>
          <?php if (! empty($secondary)): ?>
          <a class="btn btn-ghost" href="<?= esc($secondary['url']) ?>"><?= esc($secondary['label']) ?><?= icon('arrowR', 20) ?></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
