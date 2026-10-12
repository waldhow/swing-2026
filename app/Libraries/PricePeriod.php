<?php

namespace App\Libraries;

use InvalidArgumentException;

/**
 * En prisperiod: vilka veckodagar (1 = måndag … 7 = söndag), tidsintervall och
 * pris per simulator och timme. 'to' kan vara '24:00' för midnatt.
 */
final readonly class PricePeriod
{
    /**
     * @param list<int> $days
     */
    public function __construct(
        public string $label,
        public array $days,
        public int $fromMinute,
        public int $toMinute,
        public int $price,
    ) {
        if ($days === [] || array_diff($days, range(1, 7)) !== []) {
            throw new InvalidArgumentException("Ogiltiga veckodagar i prisperioden {$label}.");
        }
        if ($fromMinute >= $toMinute) {
            throw new InvalidArgumentException("Prisperioden {$label} slutar innan den börjar.");
        }
        if ($price <= 0) {
            throw new InvalidArgumentException("Prisperioden {$label} saknar pris.");
        }
    }

    public static function fromArray(array $row): self
    {
        foreach (['label', 'days', 'from', 'to', 'price'] as $key) {
            if (! array_key_exists($key, $row)) {
                throw new InvalidArgumentException("Prisperioden saknar fältet {$key}.");
            }
        }

        return new self(
            (string) $row['label'],
            array_map('intval', (array) $row['days']),
            self::toMinute((string) $row['from']),
            self::toMinute((string) $row['to']),
            (int) $row['price'],
        );
    }

    /** Täcker perioden en starttid (halvöppet intervall: from ≤ t < to)? */
    public function covers(int $day, int $minute): bool
    {
        return in_array($day, $this->days, true)
            && $minute >= $this->fromMinute
            && $minute < $this->toMinute;
    }

    private static function toMinute(string $clock): int
    {
        if (! preg_match('/^(\d{2}):(\d{2})$/', $clock, $m) || (int) $m[1] > 24 || (int) $m[2] > 59) {
            throw new InvalidArgumentException("Ogiltig tid i prisperioden: {$clock}");
        }

        $minute = (int) $m[1] * 60 + (int) $m[2];
        if ($minute > 24 * 60) {
            throw new InvalidArgumentException("Ogiltig tid i prisperioden: {$clock}");
        }

        return $minute;
    }
}
