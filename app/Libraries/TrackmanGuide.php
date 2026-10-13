<?php

namespace App\Libraries;

use RuntimeException;

/**
 * TrackMan-guiden ("Förstå dina TrackMan-siffror"). Innehållet ligger i
 * app/Content/trackman-guide.json – överfört maskinellt från designfilen, så
 * texterna ändras där och inte i vyn.
 *
 * Avsnitt har id "s<nr>" (eller eget id), visningsnummer "01" … och de nivåer
 * avsnittet är fokus för (n = nybörjare, m = medel, l = låg-hcp, e = elit).
 */
final class TrackmanGuide
{
    /** @var array<string, mixed> */
    private array $data;

    public function __construct(?string $file = null)
    {
        $file ??= APPPATH . 'Content/trackman-guide.json';
        $data = json_decode((string) file_get_contents($file), true);

        if (! is_array($data) || ! isset($data['parts'], $data['levels'], $data['focus'])) {
            throw new RuntimeException('TrackMan-guiden saknar innehåll: ' . $file);
        }

        $this->data = $data;
    }

    /** @return list<array{0: string, 1: string}> nivåer, först "Alla nivåer" */
    public function levels(): array
    {
        return $this->data['levels'];
    }

    /** @return list<array<string, mixed>> delar med avsnitt, färdiga för vyn */
    public function parts(): array
    {
        $focus = $this->data['focus'];

        return array_map(static function (array $part) use ($focus): array {
            $label = $part['label'] ?? null;

            $part['divider']  = $label !== null;
            $part['tocLabel'] = $label !== null
                ? $label . (! empty($part['title']) && str_starts_with($label, 'Del') ? ' · ' . $part['title'] : '')
                : ($part['tocLabel'] ?? '');
            $part['sections'] = array_map(static function (array $s) use ($focus): array {
                $s['id']     ??= 's' . $s['n'];
                $s['num']    ??= str_pad((string) $s['n'], 2, '0', STR_PAD_LEFT);
                $s['focusIn'] = isset($s['n'])
                    ? array_keys(array_filter($focus, static fn (array $nums): bool => in_array($s['n'], $nums, true)))
                    : [];

                return $s;
            }, $part['sections']);

            return $part;
        }, $this->data['parts']);
    }

    /** @return array<string, array{label: string, ref: float, refLabel: string, cs: int, bs: int}> */
    public function clubs(): array
    {
        return $this->data['clubs'];
    }

    /** @return list<array{0: float, 1: float}> Smash Factor-zoner för driver */
    public function zones(): array
    {
        return $this->data['zones'];
    }

    /** De åtta siffrorna att börja med (kort i avsnitt 1) – även för startsidans puff. */
    public function keyMetrics(): array
    {
        foreach ($this->data['parts'][0]['sections'][0]['blocks'] as $block) {
            if ($block['k'] === 'cards') {
                return array_map(static fn (array $c): array => ['name' => $c[0], 'what' => $c[1], 'goal' => $c[2], 'ref' => $c[3]], $block['cards']);
            }
        }

        return [];
    }

    /** @return list<array{q: string, a: string}> Vanliga frågor (för FAQPage-markup) */
    public function faq(): array
    {
        foreach ($this->parts() as $part) {
            foreach ($part['sections'] as $section) {
                foreach ($section['blocks'] as $block) {
                    if ($block['k'] === 'qa') {
                        return array_map(static fn (array $qa): array => ['q' => $qa[0], 'a' => $qa[1]], $block['qa']);
                    }
                }
            }
        }

        return [];
    }

    /** Smash Factor-omdöme för driver, samma gränser som riktvärdestabellen. */
    public static function smashVerdict(float $value): string
    {
        return match (true) {
            $value < 1.35  => 'Ofta stor förbättringspotential',
            $value <= 1.42 => 'Träffen eller energiöverföringen kan förbättras',
            $value <= 1.46 => 'Bra nivå för många amatörer',
            $value <= 1.49 => 'Mycket effektiv bollträff',
            default        => 'Nära typiskt maximalt effektiv nivå med driver',
        };
    }
}
