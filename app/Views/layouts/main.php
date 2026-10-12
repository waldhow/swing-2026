<?php
/**
 * Delad layout. Förväntar sig från controllern/PageRenderer:
 *   $meta      ['title', 'description', 'canonical' (null = ingen), 'robots' (valfri)]
 *   $navActive vilken toppmenypunkt som är aktiv
 *   $trail     brödsmulor (renderas av sidans hero-partial)
 *   $jsonLd    lista med JSON-LD-objekt
 */
$swing     = config('Swing');
$pagesCfg  = config('Pages');
$indexable = config('Seo')->indexable;
$robots    = $indexable ? ($meta['robots'] ?? 'index, follow') : 'noindex, nofollow';
$ogImage   = base_url(config('Images')->ogImage);
// Aktuell sida = första segmentet i sökvägen (tavlingar/x hör till tavlingar).
$current   = explode('/', trim(service('request')->getUri()->getPath(), '/'))[0];
?>
<!DOCTYPE html>
<html lang="sv">
<head>
<meta charset="UTF-8" />
<script>document.documentElement.classList.add('js')</script>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?= esc($meta['title']) ?></title>
<meta name="description" content="<?= esc($meta['description']) ?>" />
<meta name="robots" content="<?= esc($robots) ?>" />
<?php if (! empty($meta['canonical'])): ?>
<link rel="canonical" href="<?= esc($meta['canonical']) ?>" />
<meta property="og:url" content="<?= esc($meta['canonical']) ?>" />
<?php endif; ?>
<meta property="og:type" content="website" />
<meta property="og:locale" content="sv_SE" />
<meta property="og:site_name" content="<?= esc($swing->name) ?>" />
<meta property="og:title" content="<?= esc($meta['title']) ?>" />
<meta property="og:description" content="<?= esc($meta['description']) ?>" />
<meta property="og:image" content="<?= esc($ogImage) ?>" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="theme-color" content="#180f07" />
<?php /* ICO först (reserv, mörk ruta), SVG sist så att moderna webbläsare väljer den — den växlar färg efter ljust/mörkt tema. */ ?>
<link rel="icon" href="/favicon.ico" sizes="32x32" />
<link rel="icon" type="image/svg+xml" href="<?= asset('favicon.svg') ?>" />
<link rel="apple-touch-icon" href="/apple-touch-icon.png" />
<?php /* Typsnitten ligger på egen server — besökarens webbläsare kontaktar inte Google. */ ?>
<link rel="preload" href="/assets/fonts/hanken-grotesk-latin-normal.woff2" as="font" type="font/woff2" crossorigin />
<link rel="stylesheet" href="<?= asset('assets/fonts/fonts.css') ?>" />
<link rel="stylesheet" href="<?= asset('assets/site.css') ?>" />
<link rel="stylesheet" href="<?= asset('assets/swing.css') ?>" />
<?= $this->renderSection('head') ?>
<?php foreach ($jsonLd as $block): ?>
<script type="application/ld+json"><?= json_encode($block, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php endforeach; ?>
</head>
<body>
<a class="skip-link" href="#innehall">Hoppa till innehållet</a>

<header class="nav">
  <div class="nav-in">
    <a class="brand" href="/"><img src="/assets/logo.svg" alt="<?= esc($swing->name) ?> – till startsidan" width="701" height="540" /></a>
    <a class="nav-cta nav-cta-m" href="<?= esc(booking_url()) ?>"<?= external_attrs(booking_url()) ?>><?= icon('calendar', 17) ?>Boka tid<?= new_tab_hint(booking_url()) ?></a>
    <button class="burger" type="button" aria-expanded="false" aria-controls="huvudmeny"><?= icon('menu', 22) ?><span class="sr-only">Meny</span></button>
    <nav class="nav-links" id="huvudmeny" aria-label="Huvudmeny">
      <ul class="nav-list">
        <?php foreach ($pagesCfg->mainNav as $i => $item): $children = $item['children'] ?? []; $active = $current === $item['slug'] || in_array($current, $children, true); ?>
        <?php if ($children === []): ?>
        <li><a href="<?= page_path($item['slug']) ?>"<?= $active ? ' class="on" aria-current="page"' : '' ?>><?= esc($item['label']) ?></a></li>
        <?php else: ?>
        <li class="nav-group">
          <div class="nav-split">
            <a href="<?= page_path($item['slug']) ?>"<?= $active ? ' class="on"' : '' ?><?= $current === $item['slug'] ? ' aria-current="page"' : '' ?>><?= esc($item['label']) ?></a>
            <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="undermeny-<?= $i ?>"><?= icon('chevronDown', 16, 2.2) ?><span class="sr-only">Fler sidor under <?= esc($item['label']) ?></span></button>
          </div>
          <div class="nav-sub" id="undermeny-<?= $i ?>">
            <div class="nav-sub-in wrap">
              <p class="nav-sub-title"><?= esc($item['label']) ?></p>
              <ul style="--n:<?= count($children) ?>">
                <?php foreach ($children as $child): ?>
                <li><a href="<?= page_path($child) ?>"<?= $current === $child ? ' aria-current="page"' : '' ?>><span class="t"><?= esc($pagesCfg->label($child)) ?></span><?php if (! empty($pagesCfg->menuBlurbs[$child])): ?><span class="d"><?= esc($pagesCfg->menuBlurbs[$child]) ?></span><?php endif; ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </li>
        <?php endif; ?>
        <?php endforeach; ?>
      </ul>
      <a class="nav-cta" href="<?= esc(booking_url()) ?>"<?= external_attrs(booking_url()) ?>><?= icon('calendar', 18) ?>Boka tid<?= new_tab_hint(booking_url()) ?></a>
    </nav>
  </div>
</header>

<main id="innehall" tabindex="-1">
<?= $this->renderSection('content') ?>
</main>

<footer class="site-foot">
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <img class="foot-logo" src="/assets/logo.svg" alt="<?= esc($swing->name) ?>" width="701" height="540" loading="lazy" />
        <address>
          <?= esc($swing->street) ?><br /><?= esc($swing->postalCode . ' ' . $swing->locality) ?>, <?= esc($swing->municipality) ?><br />
          <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a><br />
          <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a>
        </address>
        <p class="small">Öppet <?= esc(mb_strtolower($swing->hoursText)) ?>. Lokalen är normalt obemannad.</p>
        <div class="socials">
          <a href="<?= esc($swing->socials['facebook']) ?>" rel="noopener"><?= icon('fb', 20) ?><span class="sr-only">Swing på Facebook</span></a>
          <a href="<?= esc($swing->socials['instagram']) ?>" rel="noopener"><?= icon('ig', 20) ?><span class="sr-only">Swing på Instagram</span></a>
        </div>
      </div>
      <?php foreach ($pagesCfg->footerNav as $heading => $slugs): ?>
      <nav aria-label="<?= esc($heading) ?>">
        <h2 class="foot-h"><?= esc($heading) ?></h2>
        <ul>
          <?php foreach ($slugs as $slug): ?>
          <li><a href="<?= page_path($slug) ?>"><?= esc($pagesCfg->label($slug)) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <?php endforeach; ?>
    </div>
    <div class="foot-bot">
      <span>© <?= date('Y') ?> <?= esc($swing->name) ?> · <?= esc($swing->legalName) ?>, org.nr <?= esc($swing->orgNumber) ?></span>
      <span><a href="<?= page_path('kopvillkor') ?>">Köpvillkor</a> · <a href="<?= page_path('integritetspolicy') ?>">Integritetspolicy</a></span>
    </div>
  </div>
</footer>

<script src="<?= asset('assets/site.js') ?>" defer></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
