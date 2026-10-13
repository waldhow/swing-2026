<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
use App\Models\CompetitionModel;

$c     = $competition;
$state = CompetitionModel::timeState($c);
$swing = config('Swing');
?>
<?= partial('partials/phero', [
    'eyebrow' => 'Tävling · ' . $state,
    'h1'      => $c['title'],
    'lede'    => $c['summary'] ?? '',
    'trail'   => $trail,
    'actions' => (! empty($c['registration_url']) && $state !== 'Avslutad')
        ? '<a class="btn btn-primary" href="' . esc($c['registration_url']) . '"' . external_attrs($c['registration_url']) . '>' . icon('flag', 20) . 'Anmäl dig' . new_tab_hint($c['registration_url']) . '</a>'
        : '',
]) ?>

<section class="sec">
  <div class="wrap with-side">
    <div class="prose">
      <?php if (! empty($c['description'])): ?>
      <?php foreach (preg_split('/\R{2,}/', trim($c['description'])) as $paragraph): ?>
      <p><?= nl2br(esc($paragraph)) ?></p>
      <?php endforeach; ?>
      <?php endif; ?>

      <?php if (! empty($c['registration_info'])): ?>
      <h2>Anmälan</h2>
      <p><?= nl2br(esc($c['registration_info'])) ?></p>
      <?php endif; ?>

      <?php if (! empty($c['terms']) || ! empty($c['terms_url'])): ?>
      <h2>Villkor och regler</h2>
      <?php if (! empty($c['terms'])): ?><p><?= nl2br(esc($c['terms'])) ?></p><?php endif; ?>
      <?php if (! empty($c['terms_url'])): ?><p><a href="<?= esc($c['terms_url']) ?>"<?= external_attrs($c['terms_url']) ?>>Fullständiga villkor<?= new_tab_hint($c['terms_url']) ?></a></p><?php endif; ?>
      <?php endif; ?>

      <?php if (! empty($c['results_url'])): ?>
      <h2>Resultat</h2>
      <p><a href="<?= esc($c['results_url']) ?>"<?= external_attrs($c['results_url']) ?>>Se resultat<?= new_tab_hint($c['results_url']) ?></a></p>
      <?php endif; ?>

      <p><a href="<?= page_path('tavlingar') ?>">← Alla tävlingar</a></p>
    </div>
    <div class="side card">
      <h2 class="mt-0" style="font-size:22px">Tävlingsfakta</h2>
      <dl class="detail-list">
        <dt>Status</dt><dd><?= esc($state) ?></dd>
        <dt>Typ</dt><dd><?= esc(CompetitionModel::VISIBILITIES[$c['visibility']] ?? '') ?></dd>
        <?php if (! empty($c['starts_at'])): ?>
        <dt>Datum</dt><dd><time datetime="<?= esc(date('c', strtotime($c['starts_at']))) ?>"><?= esc(sv_date($c['starts_at'], date('H:i', strtotime($c['starts_at'])) !== '00:00')) ?></time><?php if (! empty($c['ends_at'])): ?> – <time datetime="<?= esc(date('c', strtotime($c['ends_at']))) ?>"><?= esc(sv_date($c['ends_at'], date('H:i', strtotime($c['ends_at'])) !== '00:00')) ?></time><?php endif; ?></dd>
        <?php endif; ?>
        <?php if (! empty($c['format'])): ?><dt>Format</dt><dd><?= esc($c['format']) ?></dd><?php endif; ?>
        <?php if (! empty($c['price_info'])): ?><dt>Avgift</dt><dd><?= esc($c['price_info']) ?></dd><?php endif; ?>
        <dt>Plats</dt><dd><?= esc($swing->name) ?>, <?= esc($swing->addressLine()) ?></dd>
      </dl>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
