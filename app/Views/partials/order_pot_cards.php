<?php
/**
 * Säljande pottval: de färgade spelpottskorten (samma som på startsidan) är
 * själva valet i beställningen — vanliga radioknappar, så tangentbord,
 * skärmläsare och sidan utan JS fungerar.
 *   $pots, $campaignPots, $form, $interactive (false = bara visning, ingen kassa)
 *   $slug (sidan formuläret postar till, standard spelpotter), $legend,
 *   $withPersonalNumber (friskvård), $deliver (vad kunden får, för "Så går köpet till"),
 *   $emailHint (texten under e-postfältet)
 */
$form         ??= ['values' => [], 'errors' => []];
$campaignPots ??= [];
$interactive  ??= true;
$slug         ??= 'spelpotter';
$legend       ??= 'Välj spelpott';
$deliver      ??= 'din kod';
$emailHint    ??= 'Spelpotten levereras till den här adressen.';
$selected      = $form['values']['produkt'] ?? '';
$error         = $form['errors']['produkt'] ?? null;
$themes        = ['', ' burgundy', ' bottle'];

$cards = [];
foreach ($campaignPots as $c) {
    $cards[] = [
        'key' => 'kampanj-' . $c['id'], 'name' => $c['title'], 'pay' => (int) $c['pay_amount'], 'value' => (int) $c['value_amount'],
        'campaign' => true, 'until' => $c['valid_until'], 'terms' => $c['terms'] ?? '', 'summary' => $c['summary'] ?? '', 'theme' => '',
    ];
}
// Ordinarie potter har fast färg per pott (samma som på startsidan), oavsett kampanjer.
foreach (array_values($pots) as $i => $pot) {
    $cards[] = ['key' => 'pott-' . $pot['pay'], 'name' => $pot['name'], 'pay' => $pot['pay'], 'value' => $pot['value'], 'campaign' => false, 'theme' => $themes[$i % 3]];
}
$tag = $interactive ? 'label' : 'div';
?>
<?php if ($interactive): ?>
<form class="pot-shop" method="post" action="<?= page_path($slug) ?>#bestall" id="bestall" novalidate data-validate>
  <?= partial('partials/form_errors', ['form' => $form]) ?>
  <?= partial('partials/honeypot') ?>
  <fieldset class="pot-fieldset" id="f-produkt" data-msg-required="<?= esc(lang('Forms.pickPot')) ?>"<?= $error ? ' aria-describedby="f-produkt-err"' : '' ?>>
    <legend class="sr-only"><?= esc($legend) ?></legend>
    <?php if ($error): ?><p class="ferr" id="f-produkt-err"><?= icon('info', 16) ?><?= esc($error) ?></p><?php endif; ?>
<?php endif; ?>
    <div class="pot-cards">
      <?php foreach ($cards as $card): $bonus = $card['value'] - $card['pay']; ?>
      <<?= $tag ?> class="offer pot-card<?= $card['theme'] ?>"<?= $interactive ? ' id="' . esc($card['key']) . '"' : '' ?>>
        <?php if ($interactive): ?><input class="pot-radio" type="radio" name="produkt" value="<?= esc($card['key']) ?>" required<?= $selected === $card['key'] ? ' checked' : '' ?><?= $error ? ' aria-invalid="true"' : '' ?> /><?php endif; ?>
        <span class="sheen"></span>
        <?php if ($card['campaign']): ?><span class="ribbon">KAMPANJ</span><?php endif; ?>
        <span class="name"><?= esc(mb_strtoupper($card['name'])) ?></span>
        <span class="get"><span class="amt"><?= kr($card['value'], false) ?></span><span>kr i spelvärde</span></span>
        <span class="pay">
          <span><span class="l">Du betalar</span><span class="v"><?= kr($card['pay']) ?></span></span>
          <?php if ($bonus > 0): ?><span class="bonus"><?= icon('gift', 13) ?>+<?= kr($bonus) ?></span><?php endif; ?>
        </span>
        <?php if ($card['campaign']): ?>
        <span class="valid">Kan beställas t.o.m. <?= esc(sv_date($card['until'], false)) ?>.<?= $card['summary'] !== '' ? ' ' . esc($card['summary']) : '' ?><?= $card['terms'] !== '' ? ' ' . esc($card['terms']) : '' ?></span>
        <?php endif; ?>
        <?php if ($interactive): ?>
        <span class="pot-pick" aria-hidden="true"><span class="off">Välj <?= esc($card['name']) ?></span><span class="on"><?= icon('check', 18, 2.4) ?>Vald</span></span>
        <?php endif; ?>
      </<?= $tag ?>>
      <?php endforeach; ?>
    </div>
<?php if ($interactive): ?>
  </fieldset>

  <div class="pot-checkout">
    <div class="card form">
      <h3 class="checkout-title">Dina uppgifter</h3>
      <?= partial('partials/order_buyer', ['form' => $form, 'emailHint' => $emailHint]) ?>
      <?php if (! empty($withPersonalNumber)): ?>
      <?= partial('partials/field', ['name' => 'personnummer', 'label' => 'Personnummer', 'required' => true, 'autocomplete' => 'off', 'form' => $form, 'hint' => 'Behövs för friskvårdskvittot. ÅÅÅÅMMDD-NNNN.', 'attrs' => ' inputmode="numeric" maxlength="13" data-pnr', 'messages' => ['pnr-format' => lang('Forms.pnrFormat'), 'pnr-checksum' => lang('Forms.pnrChecksum')]]) ?>
      <?php endif; ?>
      <?= partial('partials/order_terms', ['form' => $form]) ?>
      <div class="btn-row"><button class="btn btn-primary" type="submit" data-busy-label="Beställer…"><?= icon('wallet', 20) ?>Beställ – betala med Swish</button></div>
      <p class="fhint">Efter beställningen visar vi hur du betalar. Vi använder dina uppgifter för att leverera beställningen, se <a href="<?= page_path('integritetspolicy') ?>" target="_blank" rel="noopener">integritetspolicyn<span class="sr-only"> (öppnas i ny flik)</span></a>.</p>
    </div>
    <div class="card">
      <h3 class="checkout-title">Så går köpet till</h3>
      <?= partial('partials/order_how', ['deliver' => $deliver]) ?>
    </div>
  </div>
</form>
<?php endif; ?>
