<?php

namespace App\Libraries;

use Config\Booking;
use Config\Swing;

/**
 * Vanliga frågor — en källa för FAQ-sidan, startsidans urval och FAQPage-markup.
 * Svaren byggs av Config\Swing och Config\Booking så att fakta alltid stämmer
 * med resten av sajten. Svaren får innehålla enkel HTML (länkar).
 */
final class Faq
{
    public function __construct(
        private readonly Swing $swing,
        private readonly Booking $booking,
        private readonly Pricing $pricing,
    ) {
    }

    /**
     * @return array<string, list<array{id: string, q: string, a: string}>> grupperade efter rubrik
     */
    public function grouped(): array
    {
        $s      = $this->swing;
        $b      = $this->booking;
        $from   = kr($this->pricing->lowestPrice());
        $link   = static fn (string $slug, string $text): string => '<a href="' . page_path($slug) . '">' . esc($text) . '</a>';
        $phone  = '<a href="tel:' . esc($s->phoneE164) . '">' . esc($s->phone) . '</a>';
        $mail   = '<a href="mailto:' . esc($s->email) . '">' . esc($s->email) . '</a>';

        return [
            'Bokning och besök' => [
                ['id' => 'var-ligger-swing', 'q' => 'Var ligger Swing?', 'a' => esc("På {$s->street} i {$s->locality}, strax utanför {$s->municipality}.") . ' ' . $link('kontakt', 'Hitta hit') . '.'],
                ['id' => 'oppettider', 'q' => 'Vilka öppettider har ni?', 'a' => esc($s->hoursText . '.')],
                ['id' => 'hur-bokar-jag', 'q' => 'Hur bokar jag?', 'a' => esc("Enklast bokar du i {$b->appName}-appen – ladda ner den och sök efter Swing Golf & Lounge. Du får en entrékod i bokningsbekräftelsen och hittar den även i appen. Du kan också boka i webbläsaren.") . ' ' . $link('boka', 'Så bokar du') . '.'],
                ['id' => 'dorrkod', 'q' => 'Hur fungerar dörrkoden?', 'a' => esc("Koden består av {$this->digits()} siffror. Ange koden och avsluta med #. Den gäller {$s->doorCodeWindowMinutes} minuter före till {$s->doorCodeWindowMinutes} minuter efter den bokade starttiden.")],
                ['id' => 'avboka', 'q' => 'Kan jag avboka?', 'a' => esc("Ja, kostnadsfritt fram till {$this->hours()} timmar före start. {$b->manageText}")],
                ['id' => 'appar', 'q' => 'Vilka appar behöver jag?', 'a' => esc("{$b->appName}-appen för att boka och betala din tid. TrackMan Golf-appen om du vill logga in i simulatorn och spara data om dina slag – du kan också spela som gäst.") . ' ' . $link('boka', 'Ladda ner apparna') . '.'],
                ['id' => 'personal', 'q' => 'Finns personal på plats?', 'a' => 'Anläggningen är normalt obemannad. Vid särskilda event kan personal finnas på plats.'],
            ],
            'Priser och betalning' => [
                ['id' => 'vad-kostar-det', 'q' => 'Vad kostar det att spela?', 'a' => esc("Priset börjar på {$from} per simulator och timme.") . ' Se fullständig prislista på sidan ' . $link('priser', 'Priser') . '.'],
                ['id' => 'per-person', 'q' => 'Betalar man per person?', 'a' => 'Nej, du betalar per simulator och timme. Det gör det enkelt att dela kostnaden när ni är flera.'],
                ['id' => 'spelpott', 'q' => 'Vad är en spelpott?', 'a' => 'Förbetalt spelvärde som du använder när du bokar. Våra ordinarie potter ger extra spelvärde. ' . $link('spelpotter', 'Se spelpotter') . '.'],
                ['id' => 'presentkort', 'q' => 'Har ni presentkort?', 'a' => esc("Ja, digitala presentkort som löses in via {$b->provider}.") . ' ' . $link('presentkort', 'Läs om presentkort') . '.'],
                ['id' => 'epassi', 'q' => 'Tar ni Epassi?', 'a' => 'Ja, vi erbjuder friskvårdsalternativ via Epassi. ' . $link('friskvard', 'Friskvård och Epassi') . '.'],
                ['id' => 'friskvard-direkt', 'q' => 'Kan jag köpa friskvård direkt hos er?', 'a' => esc('Ja, du kan köpa en friskvårdspott direkt hos oss och betala med ' . config('Payments')->methodsText() . '.') . ' Ange ditt personnummer i beställningen, så får du kvittot som underlag för friskvårdsbidraget. ' . $link('friskvard', 'Köp friskvårdspott') . '.'],
            ],
            'Spel och utrustning' => [
                ['id' => 'hur-manga', 'q' => 'Hur många kan spela i en simulator?', 'a' => esc("Vi rekommenderar {$s->recommendedPlayers} personer, men det finns ingen fast maxgräns för själva bokningen. Tänk på att alla ska få plats och kunna spela säkert.")],
                ['id' => 'nyborjare', 'q' => 'Kan nybörjare spela?', 'a' => 'Ja, vi välkomnar både nya och erfarna golfare.'],
                ['id' => 'klubbor', 'q' => 'Måste jag ta med klubbor?', 'a' => 'Ta gärna med dina egna rena klubbor. Enstaka klubbor finns att låna, och hela set kan ordnas om du kontaktar oss i förväg.'],
                ['id' => 'skor', 'q' => 'Behöver jag ha rena skor?', 'a' => 'Ja, rena inomhusskor krävs.'],
                ['id' => 'egna-bollar', 'q' => 'Får jag spela med egna golfbollar?', 'a' => 'Nej, använd endast Swings egna bollar och peggar. De finns på plats.'],
                ['id' => 'trackman', 'q' => 'Har alla simulatorer TrackMan iO?', 'a' => esc("Ja, samtliga {$this->count()}.") . ' ' . $link('trackman-io', 'Läs om simulatorerna') . '.'],
                ['id' => 'mat-och-dryck', 'q' => 'Kan man äta och dricka hos er?', 'a' => 'Vi har kaffe, läsk, vatten och snacks i självservering. Mat kan även beställas från ' . partner('Nättraby Vägkrog') . '.'],
            ],
            'Företag och grupper' => [
                ['id' => 'hela-anlaggningen', 'q' => 'Kan företag boka hela anläggningen?', 'a' => 'Ja, kontakta oss för ett upplägg och offert. ' . $link('foretagsevent', 'Företagsevent') . '.'],
                ['id' => 'hur-stora-grupper', 'q' => 'Hur stora grupper kan ni ta emot?', 'a' => esc("Vid event kan cirka {$s->eventGolfers} personer spela golf och upp till {$s->eventMaxGuests} personer vistas i lokalen.")],
            ],
            'Hjälp' => [
                ['id' => 'hjalp', 'q' => 'Hur får jag hjälp?', 'a' => 'Ring oss på ' . $phone . ' eller mejla ' . $mail . '.'],
            ],
        ];
    }

    /** @return list<array{id: string, q: string, a: string}> */
    public function all(): array
    {
        return array_merge(...array_values($this->grouped()));
    }

    /**
     * Urval för andra sidor.
     *
     * @param list<string> $ids
     *
     * @return list<array{id: string, q: string, a: string}>
     */
    public function pick(array $ids): array
    {
        $byId = [];
        foreach ($this->all() as $item) {
            $byId[$item['id']] = $item;
        }

        return array_values(array_filter(array_map(static fn (string $id): ?array => $byId[$id] ?? null, $ids)));
    }

    private function digits(): string
    {
        return [4 => 'fyra', 5 => 'fem', 6 => 'sex'][$this->swing->doorCodeDigits] ?? (string) $this->swing->doorCodeDigits;
    }

    private function hours(): string
    {
        return [2 => 'två', 3 => 'tre', 4 => 'fyra', 24 => '24'][$this->swing->freeCancellationHours] ?? (string) $this->swing->freeCancellationHours;
    }

    private function count(): string
    {
        return [4 => 'fyra'][$this->swing->simulatorCount] ?? (string) $this->swing->simulatorCount;
    }
}
