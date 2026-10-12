<?php
/** llms.txt — klartext ur samma källor som sidorna. $swing, $booking, $pricing, $pages */
$line = static fn (string $slug, string $text): string => '- [' . $pages->label($slug) . '](' . page_url($slug) . '): ' . $text;
?>
# <?= $swing->name ?>


> Inomhusgolf med <?= $swing->simulatorCount ?> <?= $swing->simulatorTech ?>-simulatorer och lounge i klassisk skotsk klubbhusanda på <?= $swing->addressLine() ?> (<?= $swing->municipality ?>, <?= $swing->region ?>). Öppet <?= mb_strtolower($swing->hoursText) ?>, normalt obemannat. Bokning sker via <?= $booking->provider ?>.

## Fakta

- Startade: <?= $swing->foundingYear ?>

- Adress: <?= $swing->addressLine() ?>, <?= $swing->municipality ?>

- Telefon: <?= $swing->phone ?>

- E-post: <?= $swing->email ?>

- Öppettider: <?= $swing->hoursText ?>

- Simulatorer: <?= $swing->simulatorCount ?> × <?= $swing->simulatorTech ?>, rekommenderat <?= $swing->recommendedPlayers ?> spelare per simulator
- Pris per simulator och timme (inte per person):
<?php foreach ($pricing->periods() as $period): ?>
  - <?= $period->label ?>: <?= $period->price ?> kr
<?php endforeach; ?>
- Spelpotter (giltiga ett år): <?= implode('; ', array_map(static fn (array $pot): string => "{$pot['name']} – betala {$pot['pay']} kr, få {$pot['value']} kr", $swing->creditPots)) ?>

- Appar: <?= $booking->appName ?> för bokning och betalning (<?= implode(', ', $booking->appLinks) ?>); <?= $swing->trackmanApp['name'] ?> för inloggning i simulatorn och slagdata (<?= implode(', ', $swing->trackmanApp['links']) ?>)
- Dörrkod: <?= $swing->doorCodeDigits ?> siffror följt av #, gäller <?= $swing->doorCodeWindowMinutes ?> minuter före till <?= $swing->doorCodeWindowMinutes ?> minuter efter bokningens start
- Avbokning: kostnadsfri fram till <?= $swing->freeCancellationHours ?> timmar före start
- Event: cirka <?= $swing->eventGolfers ?> golfare, upp till <?= $swing->eventMaxGuests ?> personer i lokalen, offert efter förfrågan

## Sidor

<?= $line('boka', 'Så bokar du och hur dörrkoden fungerar') ?>

<?= $line('priser', 'Prislista per simulator och timme') ?>

<?= $line('spelpotter', 'Spelpotter med extra spelvärde') ?>

<?= $line('presentkort', 'Digitala presentkort 100–1 000 kr') ?>

<?= $line('friskvard', 'Friskvård och Epassi') ?>

<?= $line('foretagsevent', 'Företagsevent och offert') ?>

<?= $line('infor-besoket', 'Regler och praktisk information inför besöket') ?>

<?= $line('vanliga-fragor', 'Vanliga frågor och svar') ?>

<?= $line('kontakt', 'Kontakt och vägbeskrivning') ?>

