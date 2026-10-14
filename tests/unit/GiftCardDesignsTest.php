<?php

namespace Tests\Unit;

use App\Libraries\GiftCardDesigns;
use CodeIgniter\I18n\Time;
use CodeIgniter\Test\CIUnitTestCase;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Presentkortsdesignernas säsonger — samma fall som i swingbooking och swing-order.
 */
final class GiftCardDesignsTest extends CIUnitTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        Time::setTestNow();
    }

    private static function sweden(string $when): DateTimeImmutable
    {
        return new DateTimeImmutable($when, new DateTimeZone('Europe/Stockholm'));
    }

    public function testMothersDayIsLastSundayOfMay(): void
    {
        $this->assertSame('2026-05-31', GiftCardDesigns::mothersDay(2026)->format('Y-m-d'));
        $this->assertSame('2027-05-30', GiftCardDesigns::mothersDay(2027)->format('Y-m-d'));
        $this->assertSame('2028-05-28', GiftCardDesigns::mothersDay(2028)->format('Y-m-d'));
    }

    public function testFathersDayIsSecondSundayOfNovember(): void
    {
        $this->assertSame('2026-11-08', GiftCardDesigns::fathersDay(2026)->format('Y-m-d'));
        $this->assertSame('2027-11-14', GiftCardDesigns::fathersDay(2027)->format('Y-m-d'));
        $this->assertSame('2028-11-12', GiftCardDesigns::fathersDay(2028)->format('Y-m-d'));
    }

    /** @return iterable<string, array{string, string, bool}> */
    public static function provideSeasonEdges(): iterable
    {
        yield 'jul dagen före' => ['JUL', '2026-10-23 23:59', false];
        yield 'jul första dagen' => ['JUL', '2026-10-24 00:00', true];
        yield 'jul julafton' => ['JUL', '2026-12-24 12:00', true];
        yield 'jul sista dagen' => ['JUL', '2026-12-27 23:59', true];
        yield 'jul dagen efter' => ['JUL', '2026-12-28 00:00', false];
        yield 'jul sommar' => ['JUL', '2026-07-15 12:00', false];
        yield 'mors dag dagen före' => ['MORSDAG', '2026-03-30 23:59', false];
        yield 'mors dag första dagen' => ['MORSDAG', '2026-03-31 00:00', true];
        yield 'mors dag sista dagen' => ['MORSDAG', '2026-06-03 23:59', true];
        yield 'mors dag dagen efter' => ['MORSDAG', '2026-06-04 00:00', false];
        yield 'fars dag dagen före' => ['FARSDAG', '2027-09-13 23:59', false];
        yield 'fars dag första dagen' => ['FARSDAG', '2027-09-14 00:00', true];
        yield 'fars dag sista dagen' => ['FARSDAG', '2027-11-17 23:59', true];
        yield 'fars dag dagen efter' => ['FARSDAG', '2027-11-18 00:00', false];
        yield 'standard nyår' => ['STANDARD', '2026-01-01 12:00', true];
        yield 'standard sommar' => ['STANDARD', '2026-07-15 12:00', true];
    }

    /** @dataProvider provideSeasonEdges */
    public function testSeasonRunsFromTwoMonthsBeforeToThreeDaysAfter(string $design, string $when, bool $expected): void
    {
        $this->assertSame($expected, GiftCardDesigns::inSeason($design, self::sweden($when)));
    }

    public function testSwedishDateCountsNotUtc(): void
    {
        // 23 okt 22:30 UTC är 24 okt 00:30 i Sverige — julkortet har börjat säljas.
        $this->assertTrue(GiftCardDesigns::inSeason('JUL', new DateTimeImmutable('2026-10-23 22:30', new DateTimeZone('UTC'))));
    }

    public function testAvailableFollowsTheDate(): void
    {
        $this->assertSame(['STANDARD'], GiftCardDesigns::available(self::sweden('2026-07-15 12:00')));
        $this->assertSame(['STANDARD', 'FARSDAG'], GiftCardDesigns::available(self::sweden('2026-10-14 12:00')));
        $this->assertSame(['STANDARD', 'FARSDAG', 'JUL'], GiftCardDesigns::available(self::sweden('2026-10-25 12:00')));
        $this->assertSame(['STANDARD', 'MORSDAG'], GiftCardDesigns::available(self::sweden('2026-04-25 12:00')));
    }

    public function testDefaultsToTheCurrentTime(): void
    {
        Time::setTestNow('2026-12-01 12:00', 'Europe/Stockholm');

        $this->assertSame(['STANDARD', 'JUL'], GiftCardDesigns::available());
    }
}
