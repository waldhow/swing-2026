<?php
use App\Models\InquiryModel;

/** Textmejl till Swing om ny förfrågan. $inquiry, $swing */
$packages = ['after-work' => 'Swing After Work', 'tavling' => 'Tävling', 'hela-lokalen' => 'Hela lokalen', 'vet-ej' => 'Vet inte än'];
?>
Ny förfrågan via <?= parse_url(base_url(), PHP_URL_HOST) ?>

Typ: <?= InquiryModel::TYPES[$inquiry['type']] ?? $inquiry['type'] ?>

Namn: <?= $inquiry['name'] ?>

E-post: <?= $inquiry['email'] ?>

<?php if (! empty($inquiry['phone'])): ?>Telefon: <?= $inquiry['phone'] ?>

<?php endif; ?>
<?php if (! empty($inquiry['organisation'])): ?>Företag/förening: <?= $inquiry['organisation'] ?>

<?php endif; ?>
<?php if (! empty($inquiry['participants'])): ?>Antal personer: <?= $inquiry['participants'] ?>

<?php endif; ?>
<?php if (! empty($inquiry['preferred_date'])): ?>Önskad dag/tid: <?= $inquiry['preferred_date'] ?>

<?php endif; ?>
<?php if (! empty($inquiry['package_interest'])): ?>Intresserad av: <?= $packages[$inquiry['package_interest']] ?? $inquiry['package_interest'] ?>

<?php endif; ?>
<?php if (! empty($inquiry['wants_food'])): ?>Vill ha hjälp med mat/catering: Ja

<?php endif; ?>

Meddelande:
<?= $inquiry['message'] !== '' ? $inquiry['message'] : '(inget meddelande)' ?>


Svara direkt på det här mejlet för att svara kunden. Förfrågan finns också i admin: <?= base_url('admin/forfragningar/' . $inquiry['id']) ?>/
