<?php

namespace Tests\Unit;

use App\Libraries\Pricing;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Swing;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Prisregler: dygnsgränser, vardag/helg och räkneexempel per person (AI-spec 8).
 */
final class PricingTest extends CIUnitTestCase
{
    private Pricing $pricing;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricing = new Pricing(new Swing());
    }

    /**
     * 2026-10-12 är en måndag, 2026-10-17 en lördag, 2026-10-18 en söndag.
     *
     * @return iterable<string, array{string, int|null}>
     */
    public static function provideTimes(): iterable
    {
        yield 'före öppning' => ['2026-10-12 05:59', null];
        yield 'mån öppning' => ['2026-10-12 06:00', 150];
        yield 'mån sista morgonminuten' => ['2026-10-12 07:59', 150];
        yield 'mån 08 dagpris' => ['2026-10-12 08:00', 250];
        yield 'mån 14.59' => ['2026-10-12 14:59', 250];
        yield 'mån 15 kvällspris' => ['2026-10-12 15:00', 280];
        yield 'mån 23.59' => ['2026-10-12 23:59', 280];
        yield 'fre 12' => ['2026-10-16 12:00', 250];
        yield 'lör morgon' => ['2026-10-17 07:30', 150];
        yield 'lör 08 helgpris' => ['2026-10-17 08:00', 280];
        yield 'lör lunch helgpris' => ['2026-10-17 12:00', 280];
        yield 'sön 23.30' => ['2026-10-18 23:30', 280];
        yield 'midnatt stängt' => ['2026-10-18 00:00', null];
    }

    /**
     * @dataProvider provideTimes
     */
    public function testPriceAt(string $time, ?int $expected): void
    {
        $this->assertSame($expected, $this->pricing->priceAt(new DateTimeImmutable($time)));
    }

    public function testPerPersonExamplesForFourPlayers(): void
    {
        $this->assertSame(37.5, $this->pricing->perPerson(150));
        $this->assertSame(62.5, $this->pricing->perPerson(250));
        $this->assertSame(70.0, $this->pricing->perPerson(280));
        $this->assertSame('37,50 kr', str_replace("\u{00A0}", ' ', kr($this->pricing->perPerson(150))));
        $this->assertSame('70 kr', str_replace("\u{00A0}", ' ', kr($this->pricing->perPerson(280))));
    }

    public function testDistinctAndLowestPrice(): void
    {
        $this->assertSame([150, 250, 280], $this->pricing->distinctPrices());
        $this->assertSame(150, $this->pricing->lowestPrice());
    }

    public function testRejectsGapInPeriods(): void
    {
        $config               = new Swing();
        $config->pricePeriods = array_slice($config->pricePeriods, 1);

        $this->expectException(InvalidArgumentException::class);
        new Pricing($config);
    }

    public function testRejectsOverlap(): void
    {
        $config                 = new Swing();
        $config->pricePeriods[] = ['label' => 'Dubbel', 'days' => [1], 'from' => '07:00', 'to' => '09:00', 'price' => 99];

        $this->expectException(InvalidArgumentException::class);
        new Pricing($config);
    }

    public function testRejectsInvalidPeriod(): void
    {
        $config                           = new Swing();
        $config->pricePeriods[0]['price'] = 0;

        $this->expectException(InvalidArgumentException::class);
        new Pricing($config);
    }
}
