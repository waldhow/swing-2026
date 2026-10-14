<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

/**
 * Vilka presentkortsdesigner kunderna kan välja. Samma regler som swingbooking och
 * swing-order (utils/giftCardDesigns.ts): Jul, Mors dag och Fars dag säljs från två
 * månader före högtiden till och med tredje dagen efter, Standard alltid.
 * Jul = 24 dec, mors dag = sista söndagen i maj, fars dag = andra söndagen i november.
 * Alla fönster ligger inom ett kalenderår, så det räcker att titta på dagens år.
 */
final class GiftCardDesigns
{
    private const TIMEZONE = 'Europe/Stockholm';

    /**
     * Designnycklarna i Swing::$giftCardDesigns som går att välja den dagen (svensk tid).
     *
     * @return list<string>
     */
    public static function available(?DateTimeInterface $now = null): array
    {
        return array_values(array_filter(
            array_keys(config('Swing')->giftCardDesigns),
            static fn (string $design): bool => self::inSeason($design, $now),
        ));
    }

    public static function inSeason(string $design, ?DateTimeInterface $now = null): bool
    {
        $today   = self::swedishDay($now ?? Time::now());
        $year    = (int) $today->format('Y');
        $holiday = match ($design) {
            'JUL'     => self::date($year, 12, 24),
            'MORSDAG' => self::mothersDay($year),
            'FARSDAG' => self::fathersDay($year),
            default   => null,
        };

        if ($holiday === null) {
            return true;
        }

        return $today >= $holiday->modify('-2 months') && $today < $holiday->modify('+4 days');
    }

    /** Sista söndagen i maj. */
    public static function mothersDay(int $year): DateTimeImmutable
    {
        $may31 = self::date($year, 5, 31);

        return $may31->modify('-' . $may31->format('w') . ' days');
    }

    /** Andra söndagen i november. */
    public static function fathersDay(int $year): DateTimeImmutable
    {
        $nov1 = self::date($year, 11, 1);

        return $nov1->modify('+' . ((7 - (int) $nov1->format('w')) % 7 + 7) . ' days');
    }

    private static function swedishDay(DateTimeInterface $now): DateTimeImmutable
    {
        $local = DateTimeImmutable::createFromInterface($now)->setTimezone(new DateTimeZone(self::TIMEZONE));

        return self::date((int) $local->format('Y'), (int) $local->format('n'), (int) $local->format('j'));
    }

    private static function date(int $year, int $month, int $day): DateTimeImmutable
    {
        return (new DateTimeImmutable('now', new DateTimeZone(self::TIMEZONE)))->setDate($year, $month, $day)->setTime(0, 0);
    }
}
