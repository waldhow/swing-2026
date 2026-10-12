<?php
/**
 * Kontaktuppgifter i samma lugna stil som "Bra att veta" — inget kort, inga etiketter.
 * $title valfri (standard: ingen synlig rubrik).
 */
$swing = config('Swing');
echo partial('partials/facts', [
    'title' => $title ?? false,
    'label' => 'Kontaktuppgifter',
    'items' => [
        ['icon' => 'pin', 'title' => $swing->street, 'text' => "{$swing->postalCode} {$swing->locality}, {$swing->municipality}. Öppna kartan för vägbeskrivning.", 'href' => $swing->mapUrl],
        ['icon' => 'phone', 'title' => $swing->phone, 'text' => 'Ring om du har frågor eller om något inte fungerar på plats.', 'href' => 'tel:' . $swing->phoneE164],
        ['icon' => 'mail', 'title' => $swing->email, 'text' => 'Vi svarar så snart vi kan.', 'href' => 'mailto:' . $swing->email],
        ['icon' => 'clock', 'title' => $swing->hoursText, 'text' => 'Lokalen är normalt obemannad – du kommer in med din dörrkod.'],
    ],
]);
