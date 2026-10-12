<?php if (! empty($order['forward_error'])): ?>
<span class="adm-tag adm-tag-err">Ej överlämnad</span>
<?php elseif (empty($order['forwarded_at'])): ?>
<span class="adm-tag adm-tag-warn">Väntar</span>
<?php else: ?>
<span class="adm-tag adm-tag-ok">Överlämnad</span>
<?php endif; ?>
