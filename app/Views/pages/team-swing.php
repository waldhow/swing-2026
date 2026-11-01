<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$team = config('TeamSwing');
?>
<?= partial('partials/phero', [
    'eyebrow' => 'Team Swing',
    'h1'      => 'Team Swing – för golfare som vågar satsa',
    'lede'    => 'På Swing Golf & Lounge stöttar vi golfare som vågar satsa på sin utveckling – oavsett väg. Möt de fem spelarna som tränar och tävlar hos oss i vinter.',
    'trail'   => $trail,
]) ?>

<section class="sec" aria-labelledby="mot-teamet">
  <div class="wrap">
    <div class="sec-head">
      <h2 id="mot-teamet">Möt Team Swing</h2>
      <p class="lede">Några siktar på världstoppen i simulatorgolf, andra på Nordic League eller collegegolf i USA. Gemensamt har de att vintern inte betyder paus.</p>
    </div>
    <ul class="ts-roster" role="list">
      <?php foreach ($team->players as $p): ?>
      <li><a class="ts-card" href="#<?= esc($p['slug'], 'attr') ?>">
        <span class="ts-card-img"><?= picture('team-' . $p['slug'], '(max-width: 560px) 50vw, (max-width: 960px) 33vw, 220px', false, '') ?></span>
        <span class="ts-card-name"><?= esc($p['name']) ?></span>
        <span class="ts-card-club"><?= esc($p['club']) ?></span>
      </a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<?php foreach ($team->players as $i => $p): ?>
<article class="sec ts-player<?= $i % 2 === 0 ? ' sec-alt' : '' ?>" id="<?= esc($p['slug'], 'attr') ?>" aria-labelledby="<?= esc($p['slug'], 'attr') ?>-namn">
  <div class="wrap ts-player-in">
    <div class="ts-player-img"><?= picture('team-' . $p['slug'], '(max-width: 960px) 100vw, 440px') ?></div>
    <div class="ts-player-text">
      <p class="eyebrow"><?= esc($p['club']) ?></p>
      <h2 id="<?= esc($p['slug'], 'attr') ?>-namn"><?= esc($p['name']) ?></h2>
      <p class="ts-tagline"><?= esc($p['tagline']) ?></p>
      <?php foreach ($team->questions as $key => $question): ?>
      <?php if (! empty($p['answers'][$key])): ?>
      <div class="ts-answer">
        <h3><?= esc($question) ?></h3>
        <?php foreach ($p['answers'][$key] as $paragraph): ?><p><?= esc($paragraph) ?></p><?php endforeach; ?>
      </div>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</article>
<?php endforeach; ?>

<section class="sec" aria-labelledby="engagemang">
  <div class="wrap">
    <div class="prose">
      <h2 id="engagemang">Ett team med golfen i centrum</h2>
      <p>Vi vet hur mycket tid, engagemang och målmedvetenhet som ligger bakom viljan att bli bättre. Team Swing är en del av vårt engagemang för golfen och människorna som spelar den – vi vill bidra till att golfare får möjlighet att utvecklas, träna och inspirera andra.</p>
      <div class="btn-row">
        <a class="btn btn-ghost" href="<?= page_path('tavlingar') ?>"><?= icon('trophy', 20) ?>Tävlingar och aktiviteter</a>
        <a class="btn btn-ghost" href="<?= page_path('trackman-guide') ?>">Förstå dina TrackMan-siffror<?= icon('arrowR', 20) ?></a>
      </div>
    </div>
  </div>
</section>

<?= partial('partials/cta_band', ['image' => 'simulator', 'title' => 'Träna där Team Swing tränar', 'secondary' => ['label' => 'Se priser', 'url' => page_path('priser')]]) ?>
<?= $this->endSection() ?>
