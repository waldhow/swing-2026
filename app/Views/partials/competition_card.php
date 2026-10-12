<?php
use App\Models\CompetitionModel;

/** Kort för en tävling i listor. $c = rad ur competitions. */
$state = CompetitionModel::timeState($c);
?>
<article class="card event-card">
  <div class="event-meta">
    <span class="tag tag-<?= $state === 'Pågår' ? 'live' : ($state === 'Avslutad' ? 'done' : 'soon') ?>"><?= esc($state) ?></span>
    <span class="tag"><?= esc(CompetitionModel::VISIBILITIES[$c['visibility']] ?? '') ?></span>
  </div>
  <h3><a href="<?= page_path('tavlingar/' . $c['slug']) ?>"><?= esc($c['title']) ?></a></h3>
  <?php if (! empty($c['starts_at'])): ?>
  <p class="event-date"><?= icon('calendar', 16) ?><time datetime="<?= esc(date('c', strtotime($c['starts_at']))) ?>"><?= esc(sv_date($c['starts_at'], date('H:i', strtotime($c['starts_at'])) !== '00:00')) ?></time><?php if (! empty($c['ends_at']) && substr($c['ends_at'], 0, 10) !== substr($c['starts_at'], 0, 10)): ?> – <time datetime="<?= esc(date('c', strtotime($c['ends_at']))) ?>"><?= esc(sv_date($c['ends_at'], false)) ?></time><?php endif; ?></p>
  <?php endif; ?>
  <?php if (! empty($c['format'])): ?><p class="event-format"><?= esc($c['format']) ?></p><?php endif; ?>
  <?php if (! empty($c['summary'])): ?><p><?= esc($c['summary']) ?></p><?php endif; ?>
  <a class="card-link" href="<?= page_path('tavlingar/' . $c['slug']) ?>">Läs mer<span class="sr-only"> om <?= esc($c['title']) ?></span><?= icon('arrowR', 16) ?></a>
</article>
