<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

/**
 * Offert- och kontaktförfrågningar (TECH-04, AI-spec 4.3). Formuläret postar
 * till sin egen sida; vid fel visas samma sida med meddelanden på plats.
 */
class Inquiries extends BaseController
{
    /** Telefonnummer: siffror, mellanslag, +, parenteser och bindestreck (även i webbläsaren, data-pattern). */
    public const PHONE_PATTERN = '[0-9 +()\-]{6,}';

    public const MAX_PARTICIPANTS = 500;

    /** Sida → förfrågningstyp. */
    private const TYPES = [
        'kontakt'       => 'kontakt',
        'foretagsevent' => 'foretag',
        'gruppbokning'  => 'grupp',
        'tavlingar'     => 'tavling',
    ];

    public function submit(): RedirectResponse|string
    {
        $slug = trim($this->request->getUri()->getPath(), '/');
        $type = self::TYPES[$slug] ?? null;

        if ($type === null || ! config('Inquiries')->enabled) {
            return redirect()->to(page_path($slug));
        }

        if ($this->isSpam()) {
            return redirect()->to(page_path('tack/forfragan'), 303);
        }

        $values = $this->values();

        if (! $this->validateData($values, $this->rules($type))) {
            return $this->fail($slug, $values, $this->validator->getErrors());
        }

        // Spärren räknar bara förfrågningar som faktiskt skickas — inte rättade valideringsfel.
        if ($this->isThrottled('inquiry', config('Inquiries')->maxPerHour)) {
            return $this->fail($slug, $values, ['form' => 'Du har skickat flera förfrågningar på kort tid. Ring eller mejla oss i stället, så hjälper vi dig.']);
        }

        service('inquiryService')->submit([
            'type'             => $type,
            'name'             => $values['name'],
            'email'            => mb_strtolower($values['email']),
            'phone'            => $values['phone'] ?: null,
            'organisation'     => $values['organisation'] ?: null,
            'participants'     => $values['participants'] !== '' ? (int) $values['participants'] : null,
            'preferred_date'   => $values['preferred_date'] ?: null,
            'package_interest' => $values['package_interest'] ?: null,
            'wants_food'       => $values['wants_food'] === '1' ? 1 : 0,
            'message'          => $values['message'],
        ]);

        return redirect()->to(page_path('tack/forfragan'), 303);
    }

    public function thanks(): string
    {
        return view('pages/tack_forfragan', [
            'meta'      => ['title' => 'Tack för din förfrågan | Swing Golf & Lounge', 'description' => 'Vi har tagit emot din förfrågan.', 'canonical' => null, 'robots' => 'noindex'],
            'navActive' => '',
            'trail'     => [],
            'jsonLd'    => [],
        ]);
    }

    /** @return array<string, string> trimmade fältvärden */
    private function values(): array
    {
        $fields = ['name', 'email', 'phone', 'organisation', 'participants', 'preferred_date', 'package_interest', 'wants_food', 'message'];
        $values = [];
        foreach ($fields as $field) {
            $value = (string) $this->request->getPost($field);
            // Kontrolltecken bort ur enradiga fält (de hamnar i mejlhuvuden); meddelandet behåller radbrytningar.
            $values[$field] = trim($field === 'message'
                ? (preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '')
                : (preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? ''));
        }

        return $values;
    }

    private function rules(string $type): array
    {
        $group = in_array($type, ['foretag', 'grupp', 'tavling'], true);

        return [
            'name'             => ['label' => 'namn', 'rules' => 'required|max_length[120]'],
            'email'            => ['label' => 'e-post', 'rules' => 'required|valid_email|max_length[190]'],
            'phone'            => ['label' => 'telefon', 'rules' => 'permit_empty|max_length[40]|regex_match[/^' . self::PHONE_PATTERN . '$/]', 'errors' => ['regex_match' => lang('Forms.phone')]],
            'organisation'     => ['label' => 'företag eller förening', 'rules' => 'permit_empty|max_length[160]'],
            'participants'     => ['label' => 'antal personer', 'rules' => 'permit_empty|is_natural_no_zero|less_than_equal_to[' . self::MAX_PARTICIPANTS . ']', 'errors' => ['is_natural_no_zero' => lang('Forms.participants', ['max' => self::MAX_PARTICIPANTS]), 'less_than_equal_to' => lang('Forms.participants', ['max' => self::MAX_PARTICIPANTS])]],
            'preferred_date'   => ['label' => 'önskad dag och tid', 'rules' => 'permit_empty|max_length[120]'],
            'package_interest' => ['label' => 'upplägg', 'rules' => 'permit_empty|in_list[after-work,tavling,hela-lokalen,vet-ej]'],
            'wants_food'       => ['label' => 'mat', 'rules' => 'permit_empty|in_list[1]'],
            'message'          => ['label' => 'meddelande', 'rules' => ($group ? 'permit_empty' : 'required') . '|max_length[3000]'],
        ];
    }

    private function fail(string $slug, array $values, array $errors): string
    {
        $this->response->setStatusCode(422);

        return service('pageRenderer')->render($slug, ['form' => ['values' => $values, 'errors' => $errors]]);
    }
}
