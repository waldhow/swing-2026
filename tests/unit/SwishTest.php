<?php

namespace Tests\Unit;

use App\Libraries\Swish;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Förifylld Swish-betalning enligt Swish utvecklarguide (app.swish.nu/1/p/sw).
 */
final class SwishTest extends CIUnitTestCase
{
    public function testPaymentUrlIsPrefilledAndLocked(): void
    {
        $url = Swish::paymentUrl('123 217 2559', 1000, 'W1042');

        $this->assertSame('https://app.swish.nu/1/p/sw/?sw=1232172559&amt=1000.00&msg=W1042', $url);
        $this->assertStringNotContainsString('edit=', $url, 'utan edit är nummer, belopp och meddelande låsta');
    }

    public function testMessageIsUrlEncoded(): void
    {
        $this->assertStringEndsWith('&msg=Order%20%C3%85%2F1', Swish::paymentUrl('1232172559', 300, 'Order Å/1'));
    }

    public function testQrIsAccessibleInlineSvg(): void
    {
        $svg = Swish::qrSvg(Swish::paymentUrl('1232172559', 1000, 'W1042'), 'QR-kod för Swish');

        $this->assertStringStartsWith('<svg role="img" aria-label="QR-kod för Swish" ', $svg);
        $this->assertStringNotContainsString('<?xml', $svg);
        $this->assertStringContainsString('viewBox=', $svg);
    }
}
