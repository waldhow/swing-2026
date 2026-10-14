<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Config\Booking;
use Config\Orders;
use Config\Swing;

/**
 * Låsta, ägarbekräftade fakta (AI-spec 2) och feature-flaggor. Testerna gör
 * att en tyst ändring av belopp eller länkar syns direkt.
 */
final class ContentConfigTest extends CIUnitTestCase
{
    public function testOrdinaryCreditPots(): void
    {
        $pots = array_map(static fn (array $p): array => [$p['pay'], $p['value']], (new Swing())->creditPots);

        $this->assertSame([[1000, 1150], [2000, 2400], [3000, 3750]], $pots);
    }

    public function testEpassiPotsIncludingConfirmedOddValue(): void
    {
        $pots = array_map(static fn (array $p): array => [$p['pay'], $p['value']], (new Swing())->epassiPots);

        // B03: 2 500 → 2 700, rättat av ägaren 2026-10-11 (tidigare utkast angav 2 200).
        $this->assertSame([[500, 500], [1000, 1000], [2000, 2150], [2500, 2700], [3000, 3350]], $pots);
    }

    public function testEpassiAndOrdinaryPotsAreSeparateLists(): void
    {
        $swing = new Swing();
        $this->assertNotSame($swing->creditPots, $swing->epassiPots);
    }

    public function testGiftCardRules(): void
    {
        $swing = new Swing();
        $this->assertSame([100, 1000, 100, 12], [$swing->giftCardMin, $swing->giftCardMax, $swing->giftCardStep, $swing->giftCardValidityMonths]);
    }

    public function testVisitRules(): void
    {
        $swing = new Swing();
        $this->assertSame(4, $swing->doorCodeDigits);
        $this->assertSame(20, $swing->doorCodeWindowMinutes);
        $this->assertSame(3, $swing->freeCancellationHours);
        $this->assertSame('+46455323401', $swing->phoneE164);
        $this->assertSame('info@swinggolf.se', $swing->email);
    }

    public function testBookingUrlIsVerifiedSweetspotLink(): void
    {
        $booking = new Booking();
        $this->assertSame('Sweetspot', $booking->provider);
        $this->assertTrue($booking->hasBookingUrl());
        $this->assertStringStartsWith('https://book.sweetspot.io/', $booking->ctaUrl());
    }

    public function testBookingFallsBackToInternalPageWithoutUrl(): void
    {
        $booking             = new Booking();
        $booking->bookingUrl = '';

        $this->assertFalse($booking->hasBookingUrl());
        $this->assertStringEndsWith('/boka/', $booking->ctaUrl());
    }

    public function testOrdersOperationalOnlyWhenChannelIsConfigured(): void
    {
        $orders         = new Orders();
        $orders->mailTo = '';
        $this->assertFalse($orders->isOperational());

        $orders->mailTo = 'order@swinggolf.se';
        $this->assertTrue($orders->isOperational());

        $orders->enabled = false;
        $this->assertFalse($orders->isOperational());

        $api          = new Orders();
        $api->channel = 'api';
        $api->apiUrl  = 'http://insecure.test';
        $api->apiToken = 'x';
        $this->assertFalse($api->isOperational(), 'API-kanalen kräver https');
        $api->apiUrl = 'https://crew.swinggolf.se/api/integrations/orders';
        $this->assertTrue($api->isOperational());
        $this->assertTrue($api->supportsGiftCardDesign());
        $this->assertTrue($orders->supportsGiftCardDesign(), 'swing-order läser raden design: i mejlet');
        $orders->mailCarriesGiftCardDesign = false;
        $this->assertFalse($orders->supportsGiftCardDesign());
    }

    public function testFriskvardDirectIsOpenButNeedsEncryptionKey(): void
    {
        $orders         = new Orders();
        $orders->mailTo = 'order@swinggolf.se';
        $this->assertTrue($orders->friskvardDirect, 'öppnat av ägaren 2026-10-11');

        $key = config('Encryption')->key;
        config('Encryption')->key = '';
        $this->assertFalse($orders->canSellWellnessDirect(), 'personnummer får aldrig sparas okrypterat');
        config('Encryption')->key = 'nyckel';
        $this->assertTrue($orders->canSellWellnessDirect());
        config('Encryption')->key = $key;
    }
}
