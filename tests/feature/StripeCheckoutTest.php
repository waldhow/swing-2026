<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\Events\Events;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockEmail;
use CodeIgniter\Test\TestResponse;
use Config\Services;
use Tests\Support\FakeCheckoutGateway;

/**
 * Stripe Checkout (TECH-06, TECH-15): ordern skapas på servern, levereras
 * först när betalningen är bekräftad, webhooksignaturen valideras och
 * dubbla händelser ger aldrig dubbel leverans.
 */
final class StripeCheckoutTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    private const WHSEC = 'whsec_testhemlighet';

    protected $migrate   = true;
    protected $refresh   = true;
    protected $namespace = 'App';

    private FakeCheckoutGateway $gateway;

    /** @var list<array<string, mixed>> skickade mejl i ordning */
    private array $sent = [];

    protected function setUp(): void
    {
        parent::setUp();

        Services::injectMock('email', new MockEmail(config('Email')));
        $this->sent = [];
        Events::on('email', function (array $archive): void { $this->sent[] = $archive; });

        $orders              = config('Orders');
        $orders->enabled     = true;
        $orders->channel     = 'mail';
        $orders->mailTo      = 'order-test@swinggolf.se';
        $orders->swishNumber = '123 217 2559';
        config('Encryption')->key = str_repeat('k', 32);
        Services::resetSingle('encrypter');

        $payments                      = config('Payments');
        $payments->provider            = 'stripe';
        $payments->stripeSecretKey     = 'rk_test_' . str_repeat('x', 24);
        $payments->stripeWebhookSecret = self::WHSEC;

        $this->gateway = new FakeCheckoutGateway();
        Services::injectMock('checkoutGateway', $this->gateway);
        Services::resetSingle('orderGateway');
        Services::resetSingle('orderService');
        Services::resetSingle('checkoutService');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Events::removeAllListeners('email');
        Factories::reset('config');
        Services::reset(true);
    }

    private function body(TestResponse $result): string
    {
        return $result->response()->getBody();
    }

    /** @return array<string, mixed> */
    private function order(): array
    {
        return $this->db->table('orders')->get()->getRowArray();
    }

    private function buyPot(string $pot = 'pott-1000'): TestResponse
    {
        return $this->post('spelpotter', ['produkt' => $pot, 'name' => 'Anna Andersson', 'email' => 'anna@exempel.se', 'villkor' => '1']);
    }

    /** Skickar en webhook signerad som Stripe gör (t=…,v1=HMAC-SHA256 av "t.payload"). */
    private function webhook(string $type, string $sessionId, string $secret = self::WHSEC): TestResponse
    {
        $payload = json_encode([
            'id'     => 'evt_test_' . bin2hex(random_bytes(6)),
            'object' => 'event',
            'type'   => $type,
            'data'   => ['object' => ['id' => $sessionId, 'object' => 'checkout.session']],
        ], JSON_THROW_ON_ERROR);
        $time = time();

        $result = $this->withHeaders(['Stripe-Signature' => 't=' . $time . ',v1=' . hash_hmac('sha256', $time . '.' . $payload, $secret)])
            ->withBody($payload)
            ->post('stripe/webhook');

        $this->withHeaders([])->withBody('');

        return $result;
    }

    /** @return list<array<string, mixed>> mejl till swing-order */
    private function forwardedMails(): array
    {
        return array_values(array_filter($this->sent, static fn (array $m): bool => $m['subject'] === 'Order'));
    }

    // ---------- kassan ----------

    public function testOrderRedirectsToStripeWithoutForwarding(): void
    {
        $result = $this->buyPot();

        $order = $this->order();
        $result->assertRedirectTo('https://checkout.stripe.com/c/pay/cs_test_fake1');
        $this->assertSame(303, $result->response()->getStatusCode());
        $this->assertSame('stripe', $order['payment_provider']);
        $this->assertSame('pending', $order['payment_status']);
        $this->assertSame('cs_test_fake1', $order['stripe_session_id']);
        $this->assertNull($order['forwarded_at'], 'obetald order lämnas inte över');
        $this->assertSame([], $this->sent, 'inga mejl före betalning');

        $call = $this->gateway->created[0];
        $this->assertSame($order['reference'], $call['reference']);
        $this->assertSame('checkout-' . $order['reference'] . '-1', $call['idempotencyKey']);
        $this->assertStringEndsWith('/tack/bestallning/' . $order['public_token'] . '/?session_id={CHECKOUT_SESSION_ID}', $call['successUrl']);
        $this->assertStringEndsWith('?avbruten=1', $call['cancelUrl']);
    }

    public function testPageShowsCheckoutWordingWhenStripeIsOn(): void
    {
        $html = $this->body($this->get('spelpotter/'));

        $this->assertStringContainsString('Gå till betalning', $html);
        $this->assertStringContainsString('Betala med kort eller Swish', $html);
        $this->assertStringNotContainsString('123 217 2559', $html, 'inget Swish-nummer i Stripe-läget');
    }

    // ---------- webhook ----------

    public function testWebhookFulfillsPaidOrderExactlyOnce(): void
    {
        $this->buyPot();
        $this->gateway->pay('cs_test_fake1');

        $this->webhook('checkout.session.completed', 'cs_test_fake1')->assertStatus(200);
        $this->webhook('checkout.session.completed', 'cs_test_fake1')->assertStatus(200);

        $order = $this->order();
        $this->assertSame('paid', $order['payment_status']);
        $this->assertNotNull($order['paid_at']);
        $this->assertSame('pi_test_fake1', $order['stripe_payment_intent']);
        $this->assertNotNull($order['forwarded_at']);

        $forwarded = $this->forwardedMails();
        $this->assertCount(1, $forwarded, 'dubbla händelser ger inte dubbel leverans');
        $this->assertStringStartsWith("type:Spelpott\nprice:1000\nemail:anna@exempel.se\nname:Anna Andersson\n", $forwarded[0]['body']);
        $this->assertMatchesRegularExpression('/^betalning:Stripe \(betald \d{4}-\d\d-\d\d \d\d:\d\d\)$/m', $forwarded[0]['body']);
        $this->assertStringContainsString("stripe:pi_test_fake1\n", $forwarded[0]['body']);

        $receipt = array_values(array_filter($this->sent, static fn (array $m): bool => $m['subject'] !== 'Order'));
        $this->assertCount(1, $receipt);
        $this->assertStringContainsString('BETALNINGEN ÄR KLAR', $receipt[0]['body']);
        $this->assertStringNotContainsString('Swisha', $receipt[0]['body']);
    }

    public function testDelayedPaymentWaitsForAsyncSuccess(): void
    {
        $this->buyPot();
        $this->gateway->pay('cs_test_fake1', 'unpaid');

        $this->webhook('checkout.session.completed', 'cs_test_fake1')->assertStatus(200);
        $this->assertSame('pending', $this->order()['payment_status']);
        $this->assertSame([], $this->forwardedMails());

        $this->gateway->pay('cs_test_fake1', 'paid');
        $this->webhook('checkout.session.async_payment_succeeded', 'cs_test_fake1')->assertStatus(200);

        $this->assertSame('paid', $this->order()['payment_status']);
        $this->assertCount(1, $this->forwardedMails());
    }

    public function testFailedAndExpiredSessionsUpdateStatus(): void
    {
        $this->buyPot();
        $this->webhook('checkout.session.async_payment_failed', 'cs_test_fake1')->assertStatus(200);
        $this->assertSame('failed', $this->order()['payment_status']);

        $this->db->table('orders')->update(['payment_status' => 'pending']);
        $this->webhook('checkout.session.expired', 'cs_test_fake1')->assertStatus(200);
        $this->assertSame('expired', $this->order()['payment_status']);
        $this->assertSame([], $this->forwardedMails());
    }

    public function testExpiredEventForOlderSessionDoesNotTouchNewAttempt(): void
    {
        $this->buyPot();
        $this->db->table('orders')->update(['stripe_session_id' => 'cs_test_nyare']);

        $this->webhook('checkout.session.expired', 'cs_test_fake1')->assertStatus(200);

        $this->assertSame('pending', $this->order()['payment_status']);
    }

    public function testWebhookRejectsInvalidSignature(): void
    {
        $this->buyPot();
        $this->gateway->pay('cs_test_fake1');

        $this->webhook('checkout.session.completed', 'cs_test_fake1', 'whsec_fel')->assertStatus(400);

        $this->assertSame('pending', $this->order()['payment_status']);
        $this->assertSame([], $this->forwardedMails());
    }

    public function testWebhookIsClosedWithoutSecret(): void
    {
        config('Payments')->stripeWebhookSecret = '';

        $this->webhook('checkout.session.completed', 'cs_test_fake1')->assertStatus(503);
    }

    public function testAmountMismatchIsNotFulfilled(): void
    {
        $this->buyPot();
        $this->gateway->pay('cs_test_fake1');
        $this->gateway->tamper('cs_test_fake1', ['amount_total' => 100]);

        $this->webhook('checkout.session.completed', 'cs_test_fake1')->assertStatus(200);

        $this->assertSame('pending', $this->order()['payment_status']);
        $this->assertSame([], $this->forwardedMails());
    }

    public function testStripeErrorAsksForRetry(): void
    {
        $this->webhook('checkout.session.completed', 'cs_test_finnsinte')->assertStatus(500);
    }

    // ---------- tacksidan ----------

    public function testThanksPageConfirmsPaymentBeforeWebhook(): void
    {
        $this->buyPot();
        $order = $this->order();
        $this->gateway->pay('cs_test_fake1');

        $html = $this->body($this->get('tack/bestallning/' . $order['public_token'] . '/?session_id=cs_test_fake1'));

        $this->assertStringContainsString('Betalningen är klar', $html);
        $this->assertStringNotContainsString('123 217 2559', $html);
        $this->assertSame('paid', $this->order()['payment_status']);
        $this->assertCount(1, $this->forwardedMails());

        // Webhooken som kommer efteråt levererar inte igen.
        $this->webhook('checkout.session.completed', 'cs_test_fake1')->assertStatus(200);
        $this->assertCount(1, $this->forwardedMails());
    }

    public function testSessionFromAnotherOrderIsIgnoredOnThanksPage(): void
    {
        $this->buyPot('pott-1000');
        $first = $this->order();
        $this->buyPot('pott-2000');
        $this->gateway->pay('cs_test_fake2');

        $html = $this->body($this->get('tack/bestallning/' . $first['public_token'] . '/?session_id=cs_test_fake2'));

        $this->assertStringNotContainsString('Betalningen är klar', $html);
        $this->assertSame('pending', $this->db->table('orders')->where('id', $first['id'])->get()->getRowArray()['payment_status']);
    }

    public function testCancelledCheckoutOffersRetryThatReusesOpenSession(): void
    {
        $this->buyPot();
        $order = $this->order();

        $html = $this->body($this->get('tack/bestallning/' . $order['public_token'] . '/?avbruten=1'));
        $this->assertStringContainsString('Inga pengar har dragits', $html);
        $this->assertStringContainsString('action="/tack/bestallning/' . $order['public_token'] . '/betala/"', $html);

        $this->post('tack/bestallning/' . $order['public_token'] . '/betala')
            ->assertRedirectTo('https://checkout.stripe.com/c/pay/cs_test_fake1');
        $this->assertCount(1, $this->gateway->created, 'öppen session återanvänds — ingen dubbel kassa');
    }

    public function testRetryAfterExpiryCreatesNewSession(): void
    {
        $this->buyPot();
        $order = $this->order();
        $this->gateway->expire('cs_test_fake1');

        $this->post('tack/bestallning/' . $order['public_token'] . '/betala')
            ->assertRedirectTo('https://checkout.stripe.com/c/pay/cs_test_fake2');

        $order = $this->order();
        $this->assertSame('cs_test_fake2', $order['stripe_session_id']);
        $this->assertSame(2, (int) $order['checkout_attempts']);
        $this->assertSame('checkout-' . $order['reference'] . '-2', $this->gateway->created[1]['idempotencyKey']);
    }

    public function testRetryOnPaidOrderGoesToThanksPage(): void
    {
        $this->buyPot();
        $order = $this->order();
        $this->gateway->pay('cs_test_fake1');
        $this->webhook('checkout.session.completed', 'cs_test_fake1');

        $this->post('tack/bestallning/' . $order['public_token'] . '/betala')
            ->assertRedirectTo(page_path('tack/bestallning/' . $order['public_token']));
        $this->assertCount(1, $this->gateway->created);
    }

    public function testCheckoutErrorKeepsOrderAndShowsHelp(): void
    {
        $this->gateway->failCreate = true;

        $result = $this->buyPot();
        $order  = $this->order();

        $result->assertRedirectTo(page_path('tack/bestallning/' . $order['public_token']) . '?fel=1');
        $html = $this->body($this->get('tack/bestallning/' . $order['public_token'] . '/?fel=1'));
        $this->assertStringContainsString('Betalningen kunde inte startas', $html);
        $this->assertStringContainsString('Försök betala igen', $html);
        $this->assertNull($order['forwarded_at']);
    }

    // ---------- admin och övrigt ----------

    public function testUnpaidOrderCannotBeForwardedManually(): void
    {
        $this->buyPot();
        $order = $this->order();

        $this->expectException(\RuntimeException::class);
        service('orderService')->resend((int) $order['id']);
    }

    public function testUnpaidOrdersAreNotCountedAsFailedForwards(): void
    {
        $this->buyPot();

        $this->assertSame(0, model(\App\Models\OrderModel::class)->needsForward()->countAllResults());
    }

    public function testFriskvardPersonalNumberFollowsPaidOrder(): void
    {
        $this->post('friskvard', ['produkt' => 'pott-1000', 'name' => 'Anna', 'email' => 'anna@exempel.se', 'personnummer' => '19811218-9876', 'villkor' => '1']);
        $order = $this->order();
        $this->assertStringNotContainsString('9876', (string) $order['personal_number_enc']);

        $this->gateway->pay('cs_test_fake1');
        $this->webhook('checkout.session.completed', 'cs_test_fake1');

        $forwarded = $this->forwardedMails();
        $this->assertCount(1, $forwarded);
        $this->assertStringStartsWith("type:Friskvård\nprice:1000\n", $forwarded[0]['body']);
        $this->assertStringContainsString("personnummer:19811218-9876\n", $forwarded[0]['body']);
    }

    public function testStripeStaysOffWithoutCompleteConfig(): void
    {
        config('Payments')->stripeWebhookSecret = '';
        Services::resetSingle('orderService');

        $result = $this->buyPot();
        $order  = $this->order();

        $result->assertRedirectTo(page_path('tack/bestallning/' . $order['public_token']));
        $this->assertSame('swish', $order['payment_provider']);
        $this->assertNotNull($order['forwarded_at'], 'Swish-flödet som vanligt');
        $this->assertSame([], $this->gateway->created);
    }
}
