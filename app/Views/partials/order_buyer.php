<?php /* Köparens uppgifter. $emailHint valfri. */ ?>
<div class="frow">
  <?= partial('partials/field', ['name' => 'name', 'label' => 'Ditt namn', 'required' => true, 'autocomplete' => 'name', 'form' => $form]) ?>
  <?= partial('partials/field', ['name' => 'email', 'label' => 'Din e-post', 'type' => 'email', 'required' => true, 'autocomplete' => 'email', 'form' => $form, 'hint' => $emailHint ?? null]) ?>
</div>
