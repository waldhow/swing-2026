<?php /* Visas när beställningskanalen inte är konfigurerad — ingen död köpknapp (TECH-06). */ $swing = config('Swing'); ?>
<div class="card">
  <h3>Beställ via e-post eller telefon</h3>
  <p>Webbeställningen är inte öppen just nu. Mejla <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a> eller ring <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a> och berätta vad du vill köpa, så hjälper vi dig.</p>
</div>
