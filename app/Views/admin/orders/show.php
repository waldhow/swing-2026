<?php
use App\Models\OrderModel;

$o    = $order;
$when = static fn (?string $v): string => $v ? date('Y-m-d H:i', strtotime($v)) : '—';
$designs = config('Swing')->giftCardDesigns;
?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head">
  <h1>Beställning <?= esc($o['reference']) ?></h1>
  <a class="adm-link" href="<?= site_url('admin/bestallningar/') ?>">← Alla beställningar</a>
</div>

<?php if (! empty($o['forward_error'])): ?>
<div class="adm-flash adm-flash-err" role="alert">Senaste överlämningen till swing-order misslyckades: <?= esc($o['forward_error']) ?></div>
<?php endif; ?>

<div class="adm-grid2">
  <section class="adm-card">
    <h2>Order</h2>
    <dl class="adm-dl">
      <dt>Referens</dt><dd><?= esc($o['reference']) ?></dd>
      <dt>Inkom</dt><dd><?= esc($when($o['created_at'])) ?></dd>
      <dt>Typ</dt><dd><?= esc(OrderModel::TYPES[$o['order_type']] ?? $o['order_type']) ?></dd>
      <dt>Produkt</dt><dd><?= esc($o['product_name']) ?><?= $o['campaign_id'] ? ' <span class="adm-muted">(kampanj #' . (int) $o['campaign_id'] . ')</span>' : '' ?></dd>
      <dt>Kunden betalar</dt><dd><?= esc(kr((int) $o['pay_amount'])) ?></dd>
      <dt>Spelvärde</dt><dd><?= esc(kr((int) $o['value_amount'])) ?></dd>
      <?php if (! empty($o['gift_card_name'])): ?><dt>Namn på presentkortet</dt><dd><?= esc($o['gift_card_name']) ?></dd><?php endif; ?>
      <?php if (! empty($o['gift_card_design'])): ?><dt>Design</dt><dd><?= esc($designs[$o['gift_card_design']] ?? $o['gift_card_design']) ?></dd><?php endif; ?>
    </dl>
  </section>
  <section class="adm-card">
    <h2>Köpare</h2>
    <dl class="adm-dl">
      <dt>Namn</dt><dd><?= esc($o['buyer_name']) ?></dd>
      <dt>E-post</dt><dd><a href="mailto:<?= esc($o['buyer_email'], 'attr') ?>"><?= esc($o['buyer_email']) ?></a></dd>
      <?php if ($hasPersonalNum): ?><dt>Personnummer</dt><dd>••••••••-•••• <span class="adm-muted">(krypterat — finns i swing-order)</span></dd><?php endif; ?>
      <dt>Kvitto till kund</dt><dd><?= esc($when($o['customer_mailed_at'])) ?></dd>
    </dl>
    <h2>Överlämning till swing-order</h2>
    <dl class="adm-dl">
      <dt>Kanal</dt><dd><?= esc($o['channel']) ?></dd>
      <dt>Status</dt><dd><?= partial('admin/orders/_status', ['order' => $o]) ?></dd>
      <dt>Överlämnad</dt><dd><?= esc($when($o['forwarded_at'])) ?></dd>
      <dt>Försök</dt><dd><?= (int) $o['forward_attempts'] ?></dd>
    </dl>
    <?php if (! empty($o['forward_error']) || empty($o['forwarded_at'])): ?>
    <form method="post" action="<?= site_url('admin/bestallningar/' . $o['id'] . '/skicka-igen/') ?>">
      <?= csrf_field() ?>
      <button type="submit" class="adm-btn adm-btn-primary">Skicka till swing-order igen</button>
    </form>
    <?php endif; ?>
  </section>
</div>
<?= $this->endSection() ?>
