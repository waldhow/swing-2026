<?php if (count($trail ?? []) > 1): ?>
<nav class="crumbs" aria-label="Brödsmulor">
  <ol>
    <?php foreach ($trail as $i => $crumb): ?>
    <?php if ($i === array_key_last($trail)): ?>
    <li><span aria-current="page"><?= esc($crumb['label']) ?></span></li>
    <?php else: ?>
    <li><a href="<?= esc(parse_url($crumb['url'], PHP_URL_PATH) ?: '/') ?>"><?= esc($crumb['label']) ?></a></li>
    <?php endif; ?>
    <?php endforeach; ?>
  </ol>
</nav>
<?php endif; ?>
