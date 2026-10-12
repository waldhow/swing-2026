<?php

namespace Tests\Unit;

use App\Libraries\Orders\MailOrderGateway;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Mejlformatet måste matcha swing-orders parser (server/lib/mailIngestion.ts).
 */
final class MailOrderGatewayTest extends CIUnitTestCase
{
    private function order(array $overrides = []): array
    {
        return $overrides + [
            'reference'      => 'W1042',
            'order_type'     => 'SPELPOTT',
            'product_name'   => 'Swing 1000',
            'pay_amount'     => 1000,
            'value_amount'   => 1150,
            'buyer_name'     => 'Anna Andersson',
            'buyer_email'    => 'anna@exempel.se',
            'gift_card_name' => null,
        ];
    }

    public function testSpelpottBodyStartsWithParserFields(): void
    {
        $lines = explode("\n", MailOrderGateway::body($this->order()));

        $this->assertSame(['type:Spelpott', 'price:1000', 'email:anna@exempel.se', 'name:Anna Andersson'], array_slice($lines, 0, 4));
        $this->assertContains('referens:W1042', $lines);
    }

    public function testGiftCardIncludesNameOnCard(): void
    {
        $body = MailOrderGateway::body($this->order(['order_type' => 'PRESENTKORT', 'pay_amount' => 500, 'value_amount' => 500, 'gift_card_name' => 'Pappa']));

        $this->assertStringContainsString("type:Presentkort\nprice:500\n", $body);
        $this->assertStringContainsString("name on giftcard:Pappa\n", $body);
    }

    public function testFriskvardIncludesPersonalNumber(): void
    {
        $body = MailOrderGateway::body($this->order(['order_type' => 'FRISKVARD', 'personal_number' => '19900101-1234']));

        $this->assertStringContainsString("type:Friskvård\n", $body);
        $this->assertStringContainsString("personnummer:19900101-1234\n", $body);
    }

    public function testNewlinesInCustomerDataCannotInjectFields(): void
    {
        $body = MailOrderGateway::body($this->order(['buyer_name' => "Eva\nprice:1"]));

        $this->assertStringContainsString('name:Eva price:1', $body);
        $this->assertSame(1, substr_count($body, "\nprice:"));
    }
}
