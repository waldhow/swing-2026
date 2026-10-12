<?php
/**
 * Fel som gäller hela formuläret, t.ex. spärr vid många inskick (fokuseras av site.js).
 * Fältfel visas vid respektive fält. $form
 */
$message = $form['errors']['form'] ?? null;
if ($message === null) {
    return;
}
?>
<div class="form-alert" role="alert" tabindex="-1" data-focus>
  <p><?= icon('info', 18) ?><?= esc($message) ?></p>
</div>
