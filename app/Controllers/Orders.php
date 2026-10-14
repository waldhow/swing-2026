<?php

namespace App\Controllers;

use App\Libraries\GiftCardDesigns;
use App\Models\CampaignModel;
use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Beställning av spelpotter, presentkort och (när B04 är beslutat)
 * friskvårdspotter. Betalning sker med Swish; swing-order levererar koden när
 * betalningen är verifierad. Beloppen valideras alltid mot Config\Swing eller
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
            'villkor' => ['label' => 'köpvillkoren', 'rules' => 'required', 'errors' => ['required' => lang('Forms.acceptTerms')]],
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
                $rules['design'] = [
                    'label'  => 'design',
                    'rules'  => 'permit_empty|in_list[' . implode(',', GiftCardDesigns::available()) . ']',
                    'errors' => ['in_list' => 'Det här kortet säljs inte just nu.'],
                ];
            }
        }

        $valid  = $this->validateData($values, $rules);
        $errors = $valid ? [] : $this->validator->getErrors();

        if ($product === null) {
            $errors[$slug === 'presentkort' ? 'belopp' : 'produkt'] = $slug === 'presentkort'
                ? lang('Forms.pickAmount', ['min' => kr(config('Swing')->giftCardMin), 'max' => kr(config('Swing')->giftCardMax)])
                : lang('Forms.pickPot');
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
            // Utan val (t.ex. ett formulär från före designvalet) blir det Standard.
            'gift_card_design' => $slug === 'presentkort' && $config->supportsGiftCardDesign() ? ($values['design'] ?: 'STANDARD') : null,
        ]);

        return redirect()->to(page_path('tack/bestallning/' . $order['public_token']), 303);
    }

    /** Tacksidan med Swish-instruktioner. Nås bara med orderns hemliga token. */
    public function thanks(string $token): string
    {
        $order = model(OrderModel::class)->findByToken($token);

        if ($order === null || strtotime($order['created_at']) < strtotime('-7 days')) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->response->setHeader('Cache-Control', 'no-store');

        return view('pages/tack_bestallning', [
            'order'     => $order,
            'meta'      => ['title' => 'Tack för din beställning | Swing Golf & Lounge', 'description' => 'Så betalar du din beställning med Swish.', 'canonical' => null, 'robots' => 'noindex'],
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
