<?php use App\Models\OrderModel; ?>
<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head"><h1>Beställningar</h1></div>
<p class="adm-muted">Beställningar hanteras i swing-order. Här syns de lokala kopiorna, om överlämningen lyckades och möjlighet att skicka igen. Kopiorna raderas efter <?= (int) config('Orders')->retentionDays ?> dagar.</p>

<nav class="adm-filters" aria-label="Filtrera beställningar">
  <a href="<?= site_url('admin/bestallningar/') ?>"<?= $filter === '' ? ' aria-current="true"' : '' ?>>Alla</a>
  <a href="<?= site_url('admin/bestallningar/') ?>?visa=fel"<?= $filter === 'fel' ? ' aria-current="true"' : '' ?>>Ej överlämnade</a>
</nav>

<?php if ($orders === []): ?>
<div class="adm-card adm-empty">Inga beställningar matchar.</div>
<?php else: ?>
<div class="adm-tablewrap">
<table class="adm-table">
  <thead>
    <tr><th scope="col">Referens</th><th scope="col">Inkom</th><th scope="col">Produkt</th><th scope="col">Belopp</th><th scope="col">Köpare</th><th scope="col">Status</th></tr>
  </thead>
  <tbody>
    <?php foreach ($orders as $o): ?>
    <tr>
      <td><a href="<?= site_url('admin/bestallningar/' . $o['id'] . '/') ?>"><?= esc($o['reference']) ?></a></td>
      <td><?= esc(date('Y-m-d H:i', strtotime((string) $o['created_at']))) ?></td>
      <td><?= esc($o['product_name']) ?><div class="adm-muted adm-small"><?= esc(OrderModel::TYPES[$o['order_type']] ?? $o['order_type']) ?></div></td>
      <td><?= esc(kr((int) $o['pay_amount'])) ?></td>
      <td><?= esc($o['buyer_name']) ?><div class="adm-muted adm-small"><?= esc($o['buyer_email']) ?></div></td>
      <td><?= partial('admin/orders/_status', ['order' => $o]) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
<?= $pager->links() ?>
<?php endif; ?>
<?= $this->endSection() ?>
