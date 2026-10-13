<?php
/**
 * Samtycke för statistik (TECH-03). site.js visar rutan när besökaren inte har
 * valt, och laddar Google Analytics först efter ja. Utan JS laddas ingen statistik.
 */
$analytics = config('Analytics');
?>
<section class="consent" id="consent" aria-labelledby="consent-title" data-ga="<?= esc($analytics->ga4Id) ?>" data-days="<?= (int) $analytics->consentDays ?>" hidden>
  <div class="consent-in">
    <div class="consent-text">
      <h2 id="consent-title">Får vi mäta besöken?</h2>
      <p>Vi vill använda Google Analytics för att se hur webbplatsen används och göra den bättre. Det sätter cookies och skickar uppgifter om ditt besök till Google. Du kan ändra dig när som helst under Cookie-inställningar längst ned. <a href="<?= page_path('integritetspolicy') ?>#webbplatsen">Läs mer i integritetspolicyn</a></p>
    </div>
    <div class="consent-actions">
      <button type="button" class="btn btn-ghost" data-consent="denied">Neka</button>
      <button type="button" class="btn btn-ghost" data-consent="granted">Godkänn statistik</button>
    </div>
  </div>
</section>
