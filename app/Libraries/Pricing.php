<?php

namespace App\Libraries;

use Config\Swing;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Prisregler för simulatortid — pris per simulator och timme (UX-03).
 *
 * Bygger typade perioder ur Config\Swing och validerar att de täcker
 * öppettiden 06–24 varje dag utan glapp eller överlapp, så att ett skrivfel i
 * konfigurationen aldrig når sajten.
 */
final class Pricing
{
    private const OPEN_MINUTE  = 6 * 60;
    private const CLOSE_MINUTE = 24 * 60;

    /** @var list<PricePeriod> */
    private array $periods;

    public function __construct(private readonly Swing $config)
    {
        $this->periods = array_map(
            static fn (array $row): PricePeriod => PricePeriod::fromArray($row),
            $config->pricePeriods,
        );
        $this->validate();
    }

    /** @return list<PricePeriod> */
    public function periods(): array
    {
        return $this->periods;
    }

    public function lowestPrice(): int
    {
        return min(array_map(static fn (PricePeriod $p): int => $p->price, $this->periods));
    }

    /**
     * Timpriset för en simulator vid en given starttid, eller null när stängt.
     */
    public function priceAt(DateTimeInterface $time): ?int
    {
        $day    = (int) $time->format('N');
        $minute = (int) $time->format('G') * 60 + (int) $time->format('i');

        foreach ($this->periods as $period) {
            if ($period->covers($day, $minute)) {
                return $period->price;
            }
        }

        return null;
    }

    /**
     * Pris per person när sällskapet delar på en simulator — endast som räkneexempel.
     */
    public function perPerson(int $pricePerHour, ?int $players = null): float
    {
        $players ??= $this->config->priceExamplePlayers;
        if ($players < 1) {
            throw new InvalidArgumentException('Antal spelare måste vara minst 1.');
        }

        return round($pricePerHour / $players, 2);
    }

    /**
     * Unika timpriser i stigande ordning (för räkneexemplet).
     *
     * @return list<int>
     */
    public function distinctPrices(): array
    {
        $prices = array_values(array_unique(array_map(static fn (PricePeriod $p): int => $p->price, $this->periods)));
        sort($prices);

        return $prices;
    }

    private function validate(): void
    {
        if ($this->periods === []) {
            throw new InvalidArgumentException('Prislistan är tom.');
        }

        for ($day = 1; $day <= 7; $day++) {
            $ranges = [];

            foreach ($this->periods as $period) {
                if (in_array($day, $period->days, true)) {
                    $ranges[] = [$period->fromMinute, $period->toMinute];
                }
            }

            usort($ranges, static fn (array $a, array $b): int => $a[0] <=> $b[0]);

            $cursor = self::OPEN_MINUTE;

            foreach ($ranges as [$from, $to]) {
                if ($from !== $cursor) {
                    throw new InvalidArgumentException(sprintf('Prisperioderna för dag %d har glapp eller överlapp vid %s.', $day, self::clock($cursor)));
                }
                $cursor = $to;
            }

            if ($cursor !== self::CLOSE_MINUTE) {
                throw new InvalidArgumentException(sprintf('Prisperioderna för dag %d täcker inte öppettiden fram till midnatt.', $day));
            }
        }
    }

    private static function clock(int $minute): string
    {
        return sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60);
    }
}
