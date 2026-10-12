<?php
/**
 * Bra att veta — de viktigaste uppgifterna som korta meningar i klartext,
 * läsbara för både människor och maskiner (SEO-04).
 *   $title  rubrik (standard "Bra att veta"); false = ingen synlig rubrik ($label används för skärmläsare)
 *   $items  lista med ['icon', 'title', 'text', 'href' (valfri)] — standard byggs ur Config\Swing
 *   $layout 'list' (sidospalt, standard) eller 'grid' (tre spalter i bred sektion)
 */
$swing   = config('Swing');
$booking = config('Booking');
$title ??= 'Bra att veta';
$items ??= [
    ['icon' => 'clock', 'title' => 'Öppet varje dag', 'text' => str_replace('Alla dagar ', '', $swing->hoursText) . '. Normalt obemannat – du kommer in med din dörrkod.'],
    ['icon' => 'target', 'title' => sv_count($swing->simulatorCount, true) . " {$swing->simulatorTech}-simulatorer", 'text' => "Vi rekommenderar {$swing->recommendedPlayers} spelare per simulator."],
    ['icon' => 'card', 'title' => 'Från ' . kr(service('pricing')->lowestPrice()) . ' i timmen', 'text' => 'Per simulator, inte per person.', 'href' => page_path('priser')],
    ['icon' => 'calendar', 'title' => "Boka i {$booking->appName}-appen", 'text' => 'Du får en personlig dörrkod i bokningsbekräftelsen och i appen.', 'href' => page_path('boka') . '#appar'],
    ['icon' => 'pin', 'title' => "{$swing->street}, {$swing->locality}", 'text' => "Strax utanför {$swing->municipality}.", 'href' => $swing->mapUrl],
    ['icon' => 'phone', 'title' => $swing->phone, 'text' => 'Ring om något inte fungerar på plats.', 'href' => 'tel:' . $swing->phoneE164],
];
$label ??= is_string($title) ? $title : 'Kontaktuppgifter';
$id     = 'facts-' . substr(md5($label), 0, 6);
$layout ??= 'list';
// Hårt bindestreck i rubrikraderna så att "iO-simulatorer" och telefonnummer inte bryts.
$nb = static fn (string $text): string => str_replace('-', "\u{2011}", $text);
?>
<div class="facts<?= $layout === 'grid' ? ' facts-grid' : '' ?><?= $title === false ? ' facts-untitled' : '' ?>" role="group" <?= $title === false ? 'aria-label="' . esc($label) . '"' : 'aria-labelledby="' . $id . '"' ?>>
  <?php if ($title !== false): ?><p class="facts-title" id="<?= $id ?>"><?= esc($title) ?></p><?php endif; ?>
  <ul class="facts-list">
    <?php foreach ($items as $item): ?>
    <li>
      <span class="facts-ic"><?= icon($item['icon'], 20) ?></span>
      <div>
        <?php if (! empty($item['href'])): ?>
        <a class="facts-main" href="<?= esc($item['href']) ?>"<?= str_starts_with($item['href'], 'http') ? external_attrs($item['href']) : '' ?>><?= esc($nb($item['title'])) ?><?= new_tab_hint($item['href']) ?></a>
        <?php else: ?>
        <p class="facts-main"><?= esc($nb($item['title'])) ?></p>
        <?php endif; ?>
        <p class="facts-sub"><?= esc($item['text']) ?></p>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>
</div>
