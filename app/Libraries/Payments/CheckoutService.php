<?php

namespace App\Libraries\Payments;

use App\Libraries\Orders\OrderService;
use App\Models\OrderModel;

/**
 * Stripe Checkout för beställningar, enligt Stripes rekommenderade flöde:
 *  - start(): ordern finns redan sparad; återanvänder en öppen session
 *    eller skapar en ny med idempotensnyckel per försök.
 *  - fulfill(): idempotent leverans. Anropas från webhook
 *    (checkout.session.completed / async_payment_succeeded) OCH från
 *    tacksidan (om webhooken är försenad). Hämtar sessionen från Stripe,
 *    kontrollerar belopp, valuta och payment_status, och låter bara ett
 *    anrop vinna (OrderModel::claimPaid) innan ordern lämnas till swing-order.
 *  - markEnded(): utgången eller misslyckad betalning.
 */
final class CheckoutService
{
    public function __construct(
        private readonly OrderModel $orders,
        private readonly OrderService $orderService,
        private readonly CheckoutGateway $gateway,
    ) {
    }

    /**
     * @return string Stripes kassaadress
     *
     * @throws PaymentException
     */
    public function start(array $order): string
    {
        $base = page_url('tack/bestallning/' . $order['public_token']);

        // En öppen session återanvänds, så att kunden aldrig kan betala två
        // kassor för samma order. Avbryt i Stripe stänger inte sessionen.
        if (! empty($order['stripe_session_id'])) {
            $previous = $this->gateway->retrieveSession($order['stripe_session_id']);

            if ($previous['status'] === 'open' && $previous['url'] !== null) {
                return $previous['url'];
            }
            if ($previous['status'] === 'complete') {
                return $base . '?session_id=' . rawurlencode($previous['id']);
            }
        }

        $attempt = (int) ($order['checkout_attempts'] ?? 0) + 1;

        $session = $this->gateway->createSession(
            $order,
            $base . '?session_id={CHECKOUT_SESSION_ID}',
            $base . '?avbruten=1',
            'checkout-' . $order['reference'] . '-' . $attempt,
        );

        $this->orders->update($order['id'], [
            'stripe_session_id' => $session['id'],
            'checkout_attempts' => $attempt,
            'payment_status'    => 'pending',
        ]);

        return $session['url'];
    }

    /**
     * @return array<string, mixed>|null ordern efter försöket, null om sessionen inte hör till någon order
     *
     * @throws PaymentException
     */
    public function fulfill(string $sessionId): ?array
    {
        $session = $this->gateway->retrieveSession($sessionId);
        $order   = $session['client_reference_id'] !== null
            ? $this->orders->where('reference', $session['client_reference_id'])->first()
            : null;

        if ($order === null || $order['payment_provider'] !== 'stripe') {
            log_message('warning', 'Stripe: session {id} matchar ingen Stripe-order.', ['id' => $sessionId]);

            return null;
        }

        // Skydd mot manipulation: betalt belopp och valuta måste stämma med ordern.
        if ($session['currency'] !== 'sek' || (int) $session['amount_total'] !== (int) $order['pay_amount'] * 100) {
            log_message('error', 'Stripe: belopp/valuta stämmer inte för {ref} (session {id}).', ['ref' => $order['reference'], 'id' => $sessionId]);

            return $order;
        }

        if (in_array($session['payment_status'], ['paid', 'no_payment_required'], true)) {
            if ($this->orders->claimPaid((int) $order['id'], $session['payment_intent'])) {
                $this->orders->update($order['id'], ['stripe_session_id' => $session['id']]);
                $this->orderService->fulfillPaid($this->orders->find($order['id']));
            } elseif ($session['payment_intent'] !== null && $order['stripe_payment_intent'] !== null
                && $session['payment_intent'] !== $order['stripe_payment_intent']) {
                log_message('critical', 'Stripe: order {ref} är betald två gånger ({a} och {b}) — återbetala en.', ['ref' => $order['reference'], 'a' => $order['stripe_payment_intent'], 'b' => $session['payment_intent']]);
            }
        }

        return $this->orders->find($order['id']);
    }

    /** Utgången eller misslyckad session — bara om ordern fortfarande väntar på just den sessionen. */
    public function markEnded(string $sessionId, string $status): void
    {
        $this->orders->where('stripe_session_id', $sessionId)
            ->where('payment_status', 'pending')
            ->set(['payment_status' => $status])
            ->update();
    }
}
