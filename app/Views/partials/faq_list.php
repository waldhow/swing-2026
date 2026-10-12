<?php
/** FAQ som <details> — svaren finns i initial HTML (SEO-04) och fungerar utan JS. $items, $headingLevel */
$tag = 'h' . ($headingLevel ?? 3);
?>
<div class="faq">
  <?php foreach ($items as $item): ?>
  <details id="<?= esc($item['id']) ?>">
    <summary><<?= $tag ?> class="faq-q"><?= esc($item['q']) ?></<?= $tag ?>><?= icon('chevronDown', 20, 2, 'faq-ic') ?></summary>
    <div class="faq-a"><p><?= $item['a'] /* byggs av Faq-klassen med esc() på all text */ ?></p></div>
  </details>
  <?php endforeach; ?>
</div>
