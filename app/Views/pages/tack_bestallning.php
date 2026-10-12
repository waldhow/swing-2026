<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$swing    = config('Swing');
$orders   = config('Orders');
$stripe   = $order['payment_provider'] === 'stripe';
$delivery = ($order['order_type'] === 'PRESENTKORT' ? 'presentkortet' : 'din kod') . ($order['order_type'] === 'FRISKVARD' ? ' och ditt friskvårdskvitto' : '');

// Läge: swish | paid | ended (avbruten/misslyckad) | processing (tillbaka från Stripe, väntar) | unfinished (kassan inte slutförd)
$state = match (true) {
    ! $stripe                                                                       => 'swish',
    $order['payment_status'] === 'paid'                                             => 'paid',
    $cancelled || $failed || in_array($order['payment_status'], ['failed', 'expired'], true) => 'ended',
    $returned                                                                       => 'processing',
    default                                                                         => 'unfinished',
};
$canRetry = in_array($state, ['ended', 'unfinished'], true)
    && config('Payments')->stripeEnabled()
    && strtotime($order['created_at']) >= strtotime('-1 day');

[$eyebrow, $h1, $lede] = match ($state) {
    'swish'      => ['Tack för din beställning', 'Nu återstår bara betalningen', 'Din beställning är registrerad. Betala med Swish så skickar vi ' . $delivery . ' via e-post när betalningen har kommit in, normalt inom 24 timmar.'],
    'paid'       => ['Tack för ditt köp', 'Betalningen är klar', 'Vi har tagit emot din betalning och skickar ' . $delivery . ' via e-post, normalt inom 24 timmar.'],
    'processing' => ['Tack för ditt köp', 'Betalningen behandlas', 'Vi väntar på bekräftelse av betalningen. Du får ett mejl när den är klar – du behöver inte göra något mer.'],
    'unfinished' => ['Din beställning', 'Betalningen är inte klar', 'Beställningen är sparad men inte betald. Gå till betalningen för att slutföra köpet.'],
    default      => $failed
        ? ['Betalningen genomfördes inte', 'Betalningen kunde inte startas', 'Något gick fel när vi skulle skicka dig till betalningen. Inga pengar har dragits. Försök igen om en stund, eller kontakta oss så hjälper vi dig.']
        : ['Betalningen genomfördes inte', 'Inga pengar har dragits', 'Köpet avbröts innan betalningen var klar. Din beställning finns kvar, så du kan försöka igen.'],
};
if (in_array($state, ['ended', 'unfinished'], true) && ! $canRetry) {
    $lede = 'Beställningen betalades inte och kan inte längre slutföras här. Gör en ny beställning, eller kontakta oss så hjälper vi dig.';
}
?>
<header class="phero phero-plain">
  <div class="hero-scrim"></div>
  <div class="hero-in wrap">
    <p class="eyebrow"><?= esc($eyebrow) ?></p>
    <h1><?= esc($h1) ?></h1>
    <p class="lede"><?= esc($lede) ?></p>
    <?php if ($canRetry): ?>
    <form method="post" action="<?= page_path('tack/bestallning/' . $order['public_token'] . '/betala') ?>" class="btn-row">
      <button class="btn btn-primary" type="submit"><?= icon('card', 20) ?><?= $state === 'unfinished' ? 'Gå till betalning' : 'Försök betala igen' ?></button>
    </form>
    <?php endif; ?>
  </div>
</header>
<section class="sec-tight">
  <div class="wrap" style="max-width:760px">
    <?php if ($state === 'swish'): ?>
    <div class="notice">
      <h2>Betala med Swish</h2>
      <div class="swish">
        <div class="row"><span class="k">Swisha till</span><span class="v"><?= esc($orders->swishNumber) ?></span></div>
        <div class="row"><span class="k">Belopp</span><span class="v"><?= kr((int) $order['pay_amount']) ?></span></div>
        <div class="row"><span class="k">Meddelande</span><span class="v"><?= esc($order['reference']) ?></span></div>
      </div>
      <p>Beställningen hanteras inte förrän betalningen har kommit in.</p>
    </div>
    <?php endif; ?>
    <div class="card"<?= $state === 'swish' ? ' style="margin-top:20px"' : '' ?>>
      <h2 class="mt-0" style="font-size:22px">Din beställning</h2>
      <dl class="detail-list">
        <dt>Ordernummer</dt><dd><?= esc($order['reference']) ?></dd>
        <dt>Produkt</dt><dd><?= esc($order['product_name']) ?></dd>
        <dt><?= $state === 'paid' ? 'Betalt' : 'Att betala' ?></dt><dd><?= kr((int) $order['pay_amount']) ?></dd>
        <?php if ((int) $order['value_amount'] !== (int) $order['pay_amount']): ?><dt>Spelvärde</dt><dd><?= kr((int) $order['value_amount']) ?></dd><?php endif; ?>
        <?php if (! empty($order['gift_card_name'])): ?><dt>Namn på kortet</dt><dd><?= esc($order['gift_card_name']) ?></dd><?php endif; ?>
        <dt>E-post</dt><dd><?= esc($order['buyer_email']) ?></dd>
      </dl>
      <?php if (! empty($order['customer_mailed_at'])): ?><p class="note"><?= $stripe ? 'Vi har skickat en orderbekräftelse till din e-post.' : 'Vi har även skickat instruktionerna till din e-post.' ?></p><?php endif; ?>
      <p class="note">Frågor? Mejla <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a> eller ring <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a> och uppge ditt ordernummer.</p>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
