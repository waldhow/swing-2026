<?php
/**
 * Offert-/kontaktformulär (TECH-04, AI-spec 4.3). $type = kontakt|foretag|grupp|tavling,
 * $slug = sidan formuläret postar till, $form = values/errors vid fel, $submitLabel.
 */
$form     ??= ['values' => [], 'errors' => []];
$swing     = config('Swing');
$group     = in_array($type, ['foretag', 'grupp', 'tavling'], true);
$values    = $form['values'] ?? [];
$errors    = $form['errors'] ?? [];
// Samma regler som i Inquiries::rules(); data-pattern i stället för pattern så att webbläsaren inte tolkar uttrycket själv.
$phoneAttrs = ' data-pattern="' . esc(\App\Controllers\Inquiries::PHONE_PATTERN) . '"';
$maxPeople  = \App\Controllers\Inquiries::MAX_PARTICIPANTS;
$packages  = ['after-work' => 'Swing After Work', 'tavling' => 'Tävling, t.ex. närmast hål eller längsta drive', 'hela-lokalen' => 'Hela lokalen för vårt sällskap', 'vet-ej' => 'Vet inte än – ge oss ett förslag'];

if (! config('Inquiries')->enabled): ?>
<div class="card">
  <p>Mejla oss på <a href="mailto:<?= esc($swing->email) ?>"><?= esc($swing->email) ?></a> eller ring <a href="tel:<?= esc($swing->phoneE164) ?>"><?= esc($swing->phone) ?></a>, så återkommer vi.</p>
</div>
<?php return; endif; ?>
<form class="card form" method="post" action="<?= page_path($slug) ?>#formular" id="formular" novalidate data-validate>
  <?= partial('partials/form_errors', ['form' => $form]) ?>
  <?= partial('partials/honeypot') ?>
  <div class="frow">
    <?= partial('partials/field', ['name' => 'name', 'label' => 'Namn', 'required' => true, 'autocomplete' => 'name', 'form' => $form]) ?>
    <?php if ($group): ?>
    <?= partial('partials/field', ['name' => 'organisation', 'label' => $type === 'foretag' ? 'Företag' : 'Förening, klubb eller sällskap', 'autocomplete' => 'organization', 'form' => $form]) ?>
    <?php else: ?>
    <?= partial('partials/field', ['name' => 'phone', 'label' => 'Telefon', 'type' => 'tel', 'autocomplete' => 'tel', 'form' => $form, 'attrs' => $phoneAttrs, 'messages' => ['pattern' => lang('Forms.phone')]]) ?>
    <?php endif; ?>
  </div>
  <div class="frow">
    <?= partial('partials/field', ['name' => 'email', 'label' => 'E-post', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'form' => $form]) ?>
    <?php if ($group): ?>
    <?= partial('partials/field', ['name' => 'phone', 'label' => 'Telefon', 'type' => 'tel', 'autocomplete' => 'tel', 'form' => $form, 'attrs' => $phoneAttrs, 'messages' => ['pattern' => lang('Forms.phone')]]) ?>
    <?php endif; ?>
  </div>
  <?php if ($group): ?>
  <div class="frow">
    <?= partial('partials/field', ['name' => 'participants', 'label' => 'Ungefärligt antal personer', 'type' => 'number', 'attrs' => ' min="1" max="' . $maxPeople . '" inputmode="numeric"', 'form' => $form, 'messages' => ['range' => lang('Forms.participants', ['max' => $maxPeople])]]) ?>
    <?= partial('partials/field', ['name' => 'preferred_date', 'label' => 'Önskad dag och tid', 'hint' => 'T.ex. "fredag 14 november, från 17"', 'form' => $form]) ?>
  </div>
  <?php if ($type === 'foretag'): ?>
  <fieldset class="field<?= isset($errors['package_interest']) ? ' has-error' : '' ?>"<?= isset($errors['package_interest']) ? ' aria-describedby="f-package_interest-err"' : '' ?>>
    <legend class="ilab">Vad är ni intresserade av? <span class="opt">(valfritt)</span></legend>
    <div class="choice-list">
      <?php foreach ($packages as $key => $label): ?>
      <label class="choice"><input type="radio" name="package_interest" value="<?= esc($key) ?>"<?= ($values['package_interest'] ?? '') === $key ? ' checked' : '' ?><?= isset($errors['package_interest']) ? ' aria-invalid="true"' : '' ?> /><span><?= esc($label) ?></span></label>
      <?php endforeach; ?>
    </div>
    <?php if (isset($errors['package_interest'])): ?><p class="ferr" id="f-package_interest-err"><?= icon('info', 16) ?><?= esc($errors['package_interest']) ?></p><?php endif; ?>
  </fieldset>
  <?php endif; ?>
  <div class="field<?= isset($errors['wants_food']) ? ' has-error' : '' ?>">
    <label class="choice"><input type="checkbox" name="wants_food" value="1"<?= ($values['wants_food'] ?? '') === '1' ? ' checked' : '' ?><?= isset($errors['wants_food']) ? ' aria-invalid="true" aria-describedby="f-wants_food-err"' : '' ?> /><span>Vi vill gärna ha hjälp med mat eller catering</span></label>
    <?php if (isset($errors['wants_food'])): ?><p class="ferr" id="f-wants_food-err"><?= icon('info', 16) ?><?= esc($errors['wants_food']) ?></p><?php endif; ?>
  </div>
  <?php endif; ?>
  <?= partial('partials/field', ['name' => 'message', 'label' => $group ? 'Berätta mer' : 'Meddelande', 'type' => 'textarea', 'required' => ! $group, 'form' => $form, 'hint' => $group ? 'Önskemål om tävling, mat, personal på plats eller annat.' : null]) ?>
  <p class="fhint">Vi använder uppgifterna för att svara på din förfrågan och raderar dem efter <?= (int) config('Inquiries')->retentionMonths ?> månader. Läs mer i vår <a href="<?= page_path('integritetspolicy') ?>" target="_blank" rel="noopener">integritetspolicy<span class="sr-only"> (öppnas i ny flik)</span></a>.</p>
  <div class="btn-row"><button class="btn btn-primary" type="submit"><?= icon('mail', 20) ?><?= esc($submitLabel ?? 'Skicka') ?></button></div>
</form>
