<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
/** @var \App\Libraries\TrackmanGuide $guide */
$parts  = $guide->parts();
$levels = $guide->levels();

// Vilka nivåer varje avsnitt är fokus för – korten i avsnitt 1 lånar dem.
$focusById = [];
foreach ($parts as $part) {
    foreach ($part['sections'] as $section) {
        $focusById[$section['id']] = $section['focusIn'];
    }
}
$levelChips = static function (string $size = '') use ($levels): string {
    $html = '';
    foreach ($levels as [$key, $label]) {
        $html .= '<button type="button" class="tmg-chip' . $size . '" data-level-pick="' . esc($key) . '" aria-pressed="' . ($key === 'all' ? 'true' : 'false') . '">' . esc($label) . '</button>';
    }

    return $html;
};
?>
<div class="tmg" id="tm-guide" data-level="all">

<header class="phero phero-plain tmg-hero">
  <div class="hero-scrim"></div>
  <div class="hero-glow"></div>
  <div class="hero-in wrap">
    <?= partial('partials/crumbs', ['trail' => $trail]) ?>
    <div class="tmg-hero-grid">
      <div class="tmg-hero-text">
        <p class="eyebrow">Guide · TrackMan iO</p>
        <h1>Förstå dina TrackMan-siffror – och <em>bli en bättre golfare</em></h1>
        <p class="lede">Vad betyder Ball Speed, Smash Factor, Spin Rate, Club Path och alla andra siffror på TrackMan? Och vad är egentligen ett bra värde för just dig?</p>
        <div class="tmg-intro">
          <p>Hos Swing Golf &amp; Lounge spelar och tränar du med TrackMan iO, en avancerad golfsimulator som analyserar både golfbollens rörelse och hur klubban träffar bollen.</p>
          <p>Men du behöver inte vara golfproffs för att ha nytta av siffrorna. Med några enkla kunskaper kan du förstå varför en drive blev kort, varför järnslaget drog åt höger eller varför två till synes likadana slag landade på helt olika avstånd.</p>
          <p>I den här guiden går vi igenom de viktigaste mätvärdena, jämför olika nivåer av golfare och visar hur du använder informationen för att utveckla ditt eget spel.</p>
        </div>
      </div>
      <div class="tmg-hero-side">
        <div class="tmg-hero-img"><?= picture('simulator', '(max-width: 960px) 100vw, 520px', true) ?></div>
        <?php /* Nivåvalet kräver JS – utan JS visas allt innehåll, utan markeringar. */ ?>
        <div class="tmg-level" role="group" aria-labelledby="tmg-level-title" data-js-only hidden>
          <p class="tmg-level-title" id="tmg-level-title">Vilken golfare är du?</p>
          <p class="tmg-level-hint">Vi markerar siffrorna och råden som passar din nivå.</p>
          <div class="tmg-chips"><?= $levelChips() ?></div>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="tmg-bar" data-js-only hidden>
  <div class="wrap">
    <button type="button" class="tmg-bar-btn" aria-haspopup="dialog" aria-controls="tmg-toc" data-toc-open>
      <span class="tmg-bar-now"><span class="tmg-bar-num" data-active-num>01</span><span class="tmg-bar-title" data-active-title><?= esc($parts[0]['sections'][0]['title']) ?></span></span>
      <span class="tmg-bar-label">Innehåll<?= icon('chevronDown', 16, 2.2) ?></span>
    </button>
  </div>
  <div class="tmg-progress" aria-hidden="true"><span data-progress></span></div>
</div>

<dialog class="tmg-toc" id="tmg-toc" aria-labelledby="tmg-toc-title">
  <div class="wrap">
    <div class="tmg-toc-head">
      <h2 class="tmg-toc-title" id="tmg-toc-title">Innehåll</h2>
      <button type="button" class="tmg-close" data-toc-close><?= icon('close', 20) ?><span class="sr-only">Stäng innehållet</span></button>
    </div>
    <div class="tmg-chips" role="group" aria-label="Visa råd för nivå"><?= $levelChips(' tmg-chip-sm') ?></div>
    <nav class="tmg-toc-grid" aria-label="Guidens innehåll">
      <?php foreach ($parts as $part): ?>
      <div class="tmg-toc-part">
        <p class="tmg-toc-label"><?= esc($part['tocLabel']) ?></p>
        <ul role="list">
          <?php foreach ($part['sections'] as $s): ?>
          <li<?= isset($s['lvl']) ? ' data-only-lvl="' . esc($s['lvl']) . '"' : '' ?>><a href="#<?= esc($s['id']) ?>" data-toc-link data-focus="<?= esc(implode(' ', $s['focusIn'])) ?>"><span class="tmg-toc-num"><?= esc($s['num']) ?></span><span class="tmg-toc-text"><?= esc($s['title']) ?></span><span class="tmg-dot" aria-hidden="true"></span></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </nav>
  </div>
</dialog>

<div class="wrap tmg-main">
  <?php foreach ($parts as $part): ?>
  <?php $secTag = $part['divider'] ? 'h3' : 'h2'; ?>
  <?php if ($part['divider']): ?>
  <div class="tmg-part" id="<?= esc($part['id']) ?>">
    <p class="tmg-part-label"><?= esc($part['label']) ?></p>
    <h2 class="tmg-part-title"><?= esc($part['title']) ?></h2>
  </div>
  <?php endif; ?>
  <?php if (! empty($part['levelPart'])): ?>
  <p class="tmg-levelnote">Visar råd för: <strong data-level-label>Alla nivåer</strong> <button type="button" class="tmg-chip tmg-chip-sm" data-level-reset>Visa alla nivåer</button></p>
  <?php endif; ?>
  <?php foreach ($part['sections'] as $s): ?>
  <section class="tmg-sec" id="<?= esc($s['id']) ?>" data-sec data-focus="<?= esc(implode(' ', $s['focusIn'])) ?>"<?= isset($s['lvl']) ? ' data-only-lvl="' . esc($s['lvl']) . '"' : '' ?> aria-labelledby="<?= esc($s['id']) ?>-rubrik">
    <div class="tmg-sec-head">
      <span class="tmg-num" aria-hidden="true"><?= esc($s['num']) ?></span>
      <<?= $secTag ?> class="tmg-sec-title" id="<?= esc($s['id']) ?>-rubrik"><?= esc($s['title']) ?></<?= $secTag ?>>
      <?php if ($s['focusIn'] !== [] || ! empty($s['time'])): ?>
      <div class="tmg-badges">
        <?php if ($s['focusIn'] !== []): ?><span class="tmg-badge-focus"><span class="tmg-dot" aria-hidden="true"></span>Fokus för din nivå</span><?php endif; ?>
        <?php if (! empty($s['time'])): ?><span class="tmg-badge-time"><?= esc($s['time']) ?></span><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
    <div class="tmg-sec-body">
      <?php foreach ($s['blocks'] as $block): ?>
      <?= partial('partials/tmg_block', ['b' => $block, 'hTag' => $secTag === 'h2' ? 'h3' : 'h4', 'focusById' => $focusById, 'guide' => $guide]) ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endforeach; ?>
  <?php endforeach; ?>
</div>

</div>

<?= partial('partials/cta_band', ['id' => 'guide', 'image' => 'simulator', 'title' => 'Golfen tar inte paus för vädret. Inte utvecklingen heller.', 'secondary' => ['label' => 'Se priser', 'url' => page_path('priser')]]) ?>
<?= $this->endSection() ?>
