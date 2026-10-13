<?php
/** Beställningsformulär för digitalt presentkort (CONTENT-03). $form */
$form   ??= ['values' => [], 'errors' => []];
$swing   = config('Swing');
$orders  = config('Orders');
$amount  = $form['values']['belopp'] ?? '';
$design  = $form['values']['design'] ?? 'STANDARD';
$error   = $form['errors']['belopp'] ?? null;
?>
<form class="card form" method="post" action="<?= page_path('presentkort') ?>#bestall" id="bestall" novalidate data-validate>
  <?= partial('partials/form_errors', ['form' => $form]) ?>
  <?= partial('partials/honeypot') ?>
  <div class="field<?= $error ? ' has-error' : '' ?>">
    <label class="ilab" for="f-belopp">Belopp <span class="req" aria-hidden="true">*</span></label>
    <select class="inp" id="f-belopp" name="belopp" required data-msg-required="<?= esc(lang('Forms.pickAmount', ['min' => kr($swing->giftCardMin), 'max' => kr($swing->giftCardMax)])) ?>"<?= $error ? ' aria-invalid="true" aria-describedby="f-belopp-err"' : '' ?>>
      <option value="">Välj belopp</option>
      <?php for ($v = $swing->giftCardMin; $v <= $swing->giftCardMax; $v += $swing->giftCardStep): ?>
      <option value="<?= $v ?>"<?= (string) $v === $amount ? ' selected' : '' ?>><?= kr($v) ?></option>
      <?php endfor; ?>
    </select>
    <?php if ($error): ?><p class="ferr" id="f-belopp-err"><?= icon('info', 16) ?><?= esc($error) ?></p><?php endif; ?>
  </div>
  <?php if ($orders->supportsGiftCardDesign()): ?>
  <?php $designError = $form['errors']['design'] ?? null; ?>
  <fieldset class="field<?= $designError ? ' has-error' : '' ?>"<?= $designError ? ' aria-describedby="f-design-err"' : '' ?>>
    <legend class="ilab">Design</legend>
    <div class="choice-list choice-row">
      <?php foreach ($swing->giftCardDesigns as $key => $label): ?>
      <label class="choice"><input type="radio" name="design" value="<?= esc($key) ?>"<?= $design === $key ? ' checked' : '' ?><?= $designError ? ' aria-invalid="true"' : '' ?> /><span><?= esc($label) ?></span></label>
      <?php endforeach; ?>
    </div>
    <?php if ($designError): ?><p class="ferr" id="f-design-err"><?= icon('info', 16) ?><?= esc($designError) ?></p><?php endif; ?>
  </fieldset>
  <?php endif; ?>
  <?= partial('partials/field', ['name' => 'mottagare', 'label' => 'Namn på presentkortet', 'form' => $form, 'hint' => $orders->supportsGiftCardDesign() ? 'Mottagarens namn. Skrivs på kortet med designen Standard.' : 'Mottagarens namn, om du vill att det står på kortet.', 'attrs' => ' maxlength="60"']) ?>
  <?= partial('partials/order_buyer', ['form' => $form, 'emailHint' => 'Presentkortet levereras till den här adressen.']) ?>
  <?= partial('partials/order_terms', ['form' => $form]) ?>
  <div class="btn-row"><button class="btn btn-primary" type="submit" data-busy-label="Beställer…"><?= icon('gift', 20) ?>Beställ – betala med Swish</button></div>
  <p class="fhint">Efter beställningen visar vi hur du betalar. Vi använder dina uppgifter för att leverera beställningen, se <a href="<?= page_path('integritetspolicy') ?>" target="_blank" rel="noopener">integritetspolicyn<span class="sr-only"> (öppnas i ny flik)</span></a>.</p>
</form>
