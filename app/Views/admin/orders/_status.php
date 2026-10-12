<?php use App\Models\OrderModel; ?>
<?php if (OrderModel::awaitsPayment($order)): ?>
<span class="adm-tag<?= $order['payment_status'] === 'pending' ? ' adm-tag-warn' : '' ?>"><?= esc(OrderModel::PAYMENT_STATUSES[$order['payment_status']] ?? $order['payment_status']) ?></span>
<?php elseif (! empty($order['forward_error'])): ?>
<span class="adm-tag adm-tag-err">Ej överlämnad</span>
<?php elseif (empty($order['forwarded_at'])): ?>
<span class="adm-tag adm-tag-warn">Väntar</span>
<?php else: ?>
<span class="adm-tag adm-tag-ok">Överlämnad</span>
<?php endif; ?>
