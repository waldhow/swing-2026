<?php /* Så fungerar en beställning. $deliver = vad kunden får, t.ex. "presentkortet". */ $orders = config('Orders'); $payments = config('Payments'); $deliver ??= 'din kod'; ?>
<ol class="steps-list">
<?php if ($payments->stripeEnabled()): ?>
  <li><strong>Välj och fyll i här på sidan.</strong> Du får ett ordernummer direkt.</li>
  <li><strong>Betala med <?= esc($payments->methodsText()) ?></strong> i vår säkra kassa hos Stripe.</li>
  <li><strong>Vi skickar <?= esc($deliver) ?> via e-post</strong>, normalt inom 24 timmar efter betalningen.</li>
<?php else: ?>
  <li><strong>Beställ här på sidan.</strong> Du får ett ordernummer direkt.</li>
  <li><strong>Betala med Swish</strong> till <?= esc($orders->swishNumber) ?> och skriv ordernumret i meddelandet.</li>
  <li><strong>Vi bekräftar via e-post</strong> när betalningen har kommit in, normalt inom 24 timmar, och skickar <?= esc($deliver) ?>.</li>
<?php endif; ?>
</ol>
