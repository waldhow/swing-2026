<?php

namespace App\Controllers;

use App\Libraries\Payments\PaymentException;
use App\Models\CampaignModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Beställning av spelpotter, presentkort och friskvårdspotter. Betalning med
 * Swish (swing-order verifierar) eller Stripe Checkout (Config\Payments);
 * swing-order levererar koden när betalningen är klar. Beloppen valideras alltid mot Config\Swing eller
 * en aktiv kampanj — aldrig mot värden från formuläret.
 */
class Orders extends BaseController
{
    public function submit(): RedirectResponse|string
    {
        $slug   = trim($this->request->getUri()->getPath(), '/');
        $config = config('Orders');

        if ($slug === 'friskvard' && ! $config->canSellWellnessDirect()) {
            return redirect()->to(page_path($slug));
        }

        if ($this->isSpam()) {
            return redirect()->to(page_path($slug), 303);
        }

        $values = $this->values();

        if (! $config->isOperational()) {
            return $this->fail($slug, $values, ['form' => 'Beställningar via webben är tillfälligt stängda. Kontakta oss så hjälper vi dig.']);
        }

        $rules = [
            'name'    => ['label' => 'namn', 'rules' => 'required|max_length[120]'],
            'email'   => ['label' => 'e-post', 'rules' => 'required|valid_email|max_length[190]'],
            'villkor' => ['label' => 'köpvillkoren', 'rules' => 'required', 'errors' => ['required' => 'Du behöver godkänna köpvillkoren.']],
        ];

        $product = match ($slug) {
            'spelpotter'  => $this->creditProduct($values['produkt']),
            'friskvard'   => $this->wellnessProduct($values['produkt']),
            'presentkort' => $this->giftCardProduct($values['belopp']),
            default       => null,
        };

        if ($slug === 'friskvard') {
            $rules['personnummer'] = ['label' => 'personnummer', 'rules' => 'required|personnummer'];
        }
        if ($slug === 'presentkort') {
            $rules['mottagare'] = ['label' => 'namn på presentkortet', 'rules' => 'permit_empty|max_length[60]'];
            if ($config->supportsGiftCardDesign()) {
                $rules['design'] = ['label' => 'design', 'rules' => 'required|in_list[' . implode(',', array_keys(config('Swing')->giftCardDesigns)) . ']'];
            }
        }

        $valid  = $this->validateData($values, $rules);
        $errors = $valid ? [] : $this->validator->getErrors();

        if ($product === null) {
            $errors[$slug === 'presentkort' ? 'belopp' : 'produkt'] = $slug === 'presentkort'
                ? 'Välj ett belopp mellan 100 och 1 000 kr.'
                : 'Välj vilken pott du vill beställa.';
        }

        if ($errors !== []) {
            return $this->fail($slug, $values, $errors);
        }

        // Spärren räknar bara beställningar som faktiskt läggs — inte rättade valideringsfel.
        if ($this->isThrottled('order', 10)) {
            return $this->fail($slug, $values, ['form' => 'Du har gjort flera beställningar på kort tid. Kontakta oss om något inte fungerar.']);
        }

        $order = service('orderService')->place($product + [
            'buyer_name'       => $values['name'],
            'buyer_email'      => mb_strtolower($values['email']),
            'personal_number'  => $slug === 'friskvard' ? $values['personnummer'] : null,
            'gift_card_name'   => $slug === 'presentkort' && $values['mottagare'] !== '' ? $values['mottagare'] : null,
            'gift_card_design' => $slug === 'presentkort' && $config->supportsGiftCardDesign() ? $values['design'] : null,
        ]);

        if ($order['payment_provider'] === 'stripe') {
            return $this->toCheckout($order);
        }

        return redirect()->to(page_path('tack/bestallning/' . $order['public_token']), 303);
    }

    /** Nytt betalförsök för en Stripe-order som inte är betald (t.ex. efter avbrutet köp). */
    public function retry(string $token): RedirectResponse
    {
        $order = model(OrderModel::class)->findByToken($token);

        if ($order === null || $order['payment_provider'] !== 'stripe' || $order['payment_status'] === 'paid'
            || ! config('Payments')->stripeEnabled() || strtotime($order['created_at']) < strtotime('-1 day')) {
            return redirect()->to(page_path('tack/bestallning/' . $token), 303);
        }

        return $this->toCheckout($order);
    }

    private function toCheckout(array $order): RedirectResponse
    {
        try {
            return redirect()->to(service('checkoutService')->start($order), 303);
        } catch (PaymentException) {
            return redirect()->to(page_path('tack/bestallning/' . $order['public_token']) . '?fel=1', 303);
        }
    }

    /** Tacksidan: Swish-instruktioner eller Stripe-betalningens läge. Nås bara med orderns hemliga token. */
    public function thanks(string $token): string
    {
        $order = model(OrderModel::class)->findByToken($token);

        if ($order === null || strtotime($order['created_at']) < strtotime('-7 days')) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->response->setHeader('Cache-Control', 'no-store');

        // Tillbaka från Stripe: bekräfta betalningen direkt (webhooken kan vara försenad).
        $sessionId = (string) $this->request->getGet('session_id');
        if ($order['payment_provider'] === 'stripe' && $order['payment_status'] !== 'paid' && preg_match('/^cs_[A-Za-z0-9_]+$/', $sessionId)) {
            try {
                $fulfilled = service('checkoutService')->fulfill($sessionId);
                if ($fulfilled !== null && $fulfilled['reference'] === $order['reference']) {
                    $order = $fulfilled;
                }
            } catch (PaymentException) {
                // Webhooken levererar ändå — sidan visar "behandlas".
            }
        }

        return view('pages/tack_bestallning', [
            'order'     => $order,
            'returned'  => $sessionId !== '',
            'cancelled' => $this->request->getGet('avbruten') === '1',
            'failed'    => $this->request->getGet('fel') === '1',
            'meta'      => ['title' => 'Tack för din beställning | Swing Golf & Lounge', 'description' => 'Din beställning hos Swing Golf & Lounge.', 'canonical' => null, 'robots' => 'noindex'],
            'navActive' => '',
            'trail'     => [],
            'jsonLd'    => [],
        ]);
    }

    /** @return array<string, string> */
    private function values(): array
    {
        $values = [];
        foreach (['name', 'email', 'produkt', 'belopp', 'mottagare', 'design', 'personnummer', 'villkor'] as $field) {
            // Kontrolltecken bort — namn och e-post hamnar i mejlhuvuden.
            $values[$field] = trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', (string) $this->request->getPost($field)) ?? '');
        }

        return $values;
    }

    /** "pott-1000" → ordinarie spelpott, "kampanj-5" → aktiv kampanjpott. */
    private function creditProduct(string $choice): ?array
    {
        if (preg_match('/^kampanj-(\d+)$/', $choice, $m)) {
            $campaign = model(CampaignModel::class)->findActivePot((int) $m[1]);

            return $campaign === null ? null : [
                'order_type'   => 'SPELPOTT',
                'product_name' => $campaign['title'],
                'pay_amount'   => (int) $campaign['pay_amount'],
                'value_amount' => (int) $campaign['value_amount'],
                'campaign_id'  => (int) $campaign['id'],
            ];
        }

        return $this->potProduct('SPELPOTT', config('Swing')->creditPots, $choice);
    }

    private function wellnessProduct(string $choice): ?array
    {
        return $this->potProduct('FRISKVARD', config('Swing')->wellnessPots, $choice);
    }

    private function potProduct(string $type, array $pots, string $choice): ?array
    {
        foreach ($pots as $pot) {
            if ($choice === 'pott-' . $pot['pay']) {
                return [
                    'order_type'   => $type,
                    'product_name' => $pot['name'],
                    'pay_amount'   => $pot['pay'],
                    'value_amount' => $pot['value'],
                ];
            }
        }

        return null;
    }

    private function giftCardProduct(string $amount): ?array
    {
        $swing = config('Swing');

        if (! ctype_digit($amount)) {
            return null;
        }

        $value = (int) $amount;
        if ($value < $swing->giftCardMin || $value > $swing->giftCardMax || $value % $swing->giftCardStep !== 0) {
            return null;
        }

        return [
            'order_type'   => 'PRESENTKORT',
            'product_name' => 'Presentkort ' . $value . ' kr',
            'pay_amount'   => $value,
            'value_amount' => $value,
        ];
    }

    private function fail(string $slug, array $values, array $errors): string
    {
        unset($values['personnummer']);
        $this->response->setStatusCode(422);

        return service('pageRenderer')->render($slug, ['form' => ['values' => $values, 'errors' => $errors]]);
    }
}
