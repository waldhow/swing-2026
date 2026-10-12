<?php /* Så fungerar en beställning. $deliver = vad kunden får, t.ex. "presentkortet". */ $orders = config('Orders'); $deliver ??= 'din kod'; ?>
<ol class="steps-list">
  <li><strong>Beställ här på sidan.</strong> Du får ett ordernummer direkt.</li>
  <li><strong>Betala med Swish</strong> till <?= esc($orders->swishNumber) ?> och skriv ordernumret i meddelandet.</li>
  <li><strong>Vi bekräftar via e-post</strong> när betalningen har kommit in, normalt inom 24 timmar, och skickar <?= esc($deliver) ?>.</li>
</ol>
