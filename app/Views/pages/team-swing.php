<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= partial('partials/phero', [
    'eyebrow' => 'Team Swing',
    'h1'      => 'Team Swing – för golfare som vågar satsa',
    'lede'    => 'På Swing Golf & Lounge stöttar vi golfare som vågar satsa på sin utveckling – oavsett väg.',
    'trail'   => $trail,
]) ?>

<section class="sec">
  <div class="wrap">
    <div class="prose">
      <p>Vi vet hur mycket tid, engagemang och målmedvetenhet som ligger bakom viljan att bli bättre. Team Swing är en del av vårt engagemang för golfen och människorna som spelar den.</p>

      <h2>Ett team med golfen i centrum</h2>
      <p>Vi vill bidra till att golfare får möjlighet att utvecklas, träna och inspirera andra.</p>

      <h2>Möt Team Swing</h2>
      <p>Ett nytt Team Swing är på plats. Vi presenterar spelarna här när profilerna är klara.</p>

      <div class="btn-row">
        <a class="btn btn-ghost" href="<?= page_path('tavlingar') ?>">Tävlingar och aktiviteter<?= icon('arrowR', 20) ?></a>
        <a class="btn btn-ghost" href="<?= page_path('om-oss') ?>">Om Swing<?= icon('arrowR', 20) ?></a>
      </div>
    </div>
  </div>
</section>
<?= $this->endSection() ?>
