<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $swing = config('Swing'); ?>
<?= partial('partials/phero', [
    'eyebrow' => 'Kontakt & hitta hit',
    'h1'      => 'Kontakta Swing Golf & Lounge',
    'lede'    => 'Har du frågor om bokning, spelpotter, presentkort eller vill du arrangera ett event? Vi hjälper dig gärna.',
    'trail'   => $trail,
    'actions' => '<a class="btn btn-primary" href="tel:' . esc($swing->phoneE164) . '">' . icon('phone', 20) . 'Ring ' . esc($swing->phone) . '</a>'
        . '<a class="btn btn-ghost" href="mailto:' . esc($swing->email) . '">' . icon('mail', 20) . 'E-posta oss</a>',
]) ?>

<section class="sec">
  <div class="wrap contact">
    <div>
      <?= partial('partials/contact_card') ?>
    </div>
    <div class="prose">
      <h2 class="mt-0">Hitta till oss</h2>
      <p><?= esc($swing->name) ?> ligger på <?= esc($swing->street) ?> i <?= esc($swing->locality) ?>, utanför <?= esc($swing->municipality) ?> i <?= esc($swing->region) ?>.</p>
      <p><a href="<?= esc($swing->mapUrl) ?>"<?= external_attrs($swing->mapUrl) ?>>Visa vägen i Google Maps<?= new_tab_hint($swing->mapUrl) ?></a></p>

      <h2>Förfrågningar om företag och grupper</h2>
      <p>Kontakta oss med ungefärligt antal deltagare, önskat datum och eventuella önskemål om mat, tävling eller personal på plats. Vi återkommer med ett förslag. Du kan också använda formuläret för <a href="<?= page_path('foretagsevent') ?>#offert">företagsevent</a> eller <a href="<?= page_path('gruppbokning') ?>#fraga">gruppbokning</a>.</p>

      <h2>Problem under besöket?</h2>
      <p>Lokalen är normalt obemannad. Ring <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a> om något inte fungerar. Svar på vanliga frågor finns under <a href="<?= page_path('vanliga-fragor') ?>">Vanliga frågor</a> och <a href="<?= page_path('infor-besoket') ?>">Inför ditt besök</a>.</p>
    </div>
  </div>
</section>

<section class="sec sec-alt" aria-labelledby="skriv-rubrik">
  <div class="wrap" style="max-width:820px">
    <div class="sec-head" id="skriv">
      <p class="eyebrow">Skriv till oss</p>
      <h2 id="skriv-rubrik">Skicka ett meddelande</h2>
    </div>
    <?= partial('partials/inquiry_form', ['type' => 'kontakt', 'slug' => 'kontakt', 'form' => $form ?? null, 'submitLabel' => 'Skicka meddelande']) ?>
  </div>
</section>
<?= $this->endSection() ?>
