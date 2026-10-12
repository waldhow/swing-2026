<?php $error = $form['errors']['villkor'] ?? null; $checked = ($form['values']['villkor'] ?? '') !== ''; ?>
<div class="field<?= $error ? ' has-error' : '' ?>">
  <label class="choice"><input type="checkbox" id="f-villkor" name="villkor" value="1" required data-msg-required="<?= esc(lang('Forms.acceptTerms')) ?>"<?= $checked ? ' checked' : '' ?><?= $error ? ' aria-invalid="true" aria-describedby="f-villkor-err"' : '' ?> /><span>Jag har läst och godkänner <a href="<?= page_path('kopvillkor') ?>">köpvillkoren</a>.</span></label>
  <?php if ($error): ?><p class="ferr" id="f-villkor-err"><?= icon('info', 16) ?><?= esc($error) ?></p><?php endif; ?>
</div>
