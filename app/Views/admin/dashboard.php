<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="adm-head"><h1>Översikt</h1></div>

<?php if (! $ordersReady): ?>
<div class="adm-flash adm-flash-warn" role="status">
  Beställningsformulären är avstängda på sajten: kanalen <strong><?= esc($orderChannel) ?></strong> är inte konfigurerad
  (<?= $orderChannel === 'api' ? 'orders.apiUrl och orders.apiToken' : 'orders.mailTo' ?> i .env). Besökarna hänvisas till telefon och e-post.
</div>
<?php endif; ?>

<div class="adm-stats">
  <a class="adm-stat" href="<?= site_url('admin/forfragningar/') ?>?status=new">
    <span class="adm-stat-n"><?= (int) $newInquiries ?></span><span class="adm-stat-l">Nya förfrågningar</span>
    <?php if ($failedInquiry > 0): ?><span class="adm-tag adm-tag-err"><?= (int) $failedInquiry ?> ej mejlade</span><?php endif; ?>
  </a>
  <a class="adm-stat<?= $failedOrders > 0 ? ' is-alert' : '' ?>" href="<?= site_url('admin/bestallningar/') ?>?visa=fel">
    <span class="adm-stat-n"><?= (int) $failedOrders ?></span><span class="adm-stat-l">Ordrar ej överlämnade</span>
  </a>
  <a class="adm-stat" href="<?= site_url('admin/tavlingar/') ?>">
    <span class="adm-stat-n"><?= (int) $competitions ?></span><span class="adm-stat-l">Aktuella tävlingar</span>
  </a>
  <a class="adm-stat" href="<?= site_url('admin/kampanjer/') ?>">
    <span class="adm-stat-n"><?= (int) $campaigns ?></span><span class="adm-stat-l">Aktiva kampanjer</span>
  </a>
</div>

<div class="adm-grid2">
  <section class="adm-card">
    <h2>Senaste förfrågningar</h2>
    <?php if ($recentInquiries === []): ?>
    <p class="adm-muted">Inga förfrågningar ännu.</p>
    <?php else: ?>
    <ul class="adm-list">
      <?php foreach ($recentInquiries as $inquiry): ?>
      <li>
        <a href="<?= site_url('admin/forfragningar/' . $inquiry['id'] . '/') ?>"><?= esc($inquiry['name']) ?></a>
        <span class="adm-muted"><?= esc(\App\Models\InquiryModel::TYPES[$inquiry['type']] ?? $inquiry['type']) ?> · <?= esc(date('Y-m-d H:i', strtotime((string) $inquiry['created_at']))) ?></span>
        <?php if ($inquiry['status'] === 'new'): ?><span class="adm-tag adm-tag-new">Ny</span><?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>
  <section class="adm-card">
    <h2>Senaste beställningar</h2>
    <?php if ($recentOrders === []): ?>
    <p class="adm-muted">Inga beställningar ännu.</p>
    <?php else: ?>
    <ul class="adm-list">
      <?php foreach ($recentOrders as $order): ?>
      <li>
        <a href="<?= site_url('admin/bestallningar/' . $order['id'] . '/') ?>"><?= esc($order['reference']) ?></a>
        <span class="adm-muted"><?= esc($order['product_name']) ?> · <?= esc(kr((int) $order['pay_amount'])) ?></span>
        <?= partial('admin/orders/_status', ['order' => $order]) ?>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>
</div>
<?= $this->endSection() ?>
