<?php

namespace App\Controllers\Admin;

use App\Models\CampaignModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Kampanjer (TECH-05): titel, villkor, giltighetsperiod och status. Med
 * belopp blir kampanjen en beställningsbar spelpott på /spelpotter/.
 */
class Campaigns extends BaseAdminController
{
    private const FIELDS = [
        'title', 'summary', 'terms', 'pay_amount', 'value_amount',
        'valid_from', 'valid_until', 'status', 'sort_order',
    ];

    public function index(): string
    {
        return $this->render('campaigns/index', [
            'title'     => 'Kampanjer',
            'campaigns' => model(CampaignModel::class)->orderBy('status', 'ASC')->orderBy('valid_until', 'DESC')->findAll(),
            'activeIds' => array_column(model(CampaignModel::class)->activeNow(), 'id'),
        ], 'kampanjer');
    }

    public function new(): string
    {
        return $this->form(['status' => 'draft', 'show_on_home' => 0, 'sort_order' => 0]);
    }

    public function create(): string|RedirectResponse
    {
        $data   = $this->input();
        $model  = model(CampaignModel::class);
        $errors = $this->amountErrors($data);

        if ($errors !== [] || ! $model->insert($data)) {
            return $this->form($data, $errors + $model->errors());
        }

        $this->clearPageCache();

        return $this->back('kampanjer', "Kampanjen “{$data['title']}” är sparad.");
    }

    public function edit(int $id): string
    {
        return $this->form($this->find($id), [], $id);
    }

    public function update(int $id): string|RedirectResponse
    {
        $this->find($id);
        $data   = $this->input();
        $model  = model(CampaignModel::class);
        $errors = $this->amountErrors($data);

        if ($errors !== [] || ! $model->update($id, $data)) {
            return $this->form($data, $errors + $model->errors(), $id);
        }

        $this->clearPageCache();

        return $this->back('kampanjer', "Kampanjen “{$data['title']}” är uppdaterad.");
    }

    public function delete(int $id): RedirectResponse
    {
        $campaign = $this->find($id);
        model(CampaignModel::class)->delete($id);
        $this->clearPageCache();

        return $this->back('kampanjer', "Kampanjen “{$campaign['title']}” är raderad.");
    }

    private function form(array $campaign, array $errors = [], ?int $id = null): string
    {
        return $this->render('campaigns/form', [
            'title'    => $id === null ? 'Ny kampanj' : 'Redigera kampanj',
            'campaign' => $campaign,
            'errors'   => $errors,
            'id'       => $id,
        ], 'kampanjer');
    }

    private function find(int $id): array
    {
        $campaign = model(CampaignModel::class)->find($id);

        if ($campaign === null) {
            throw PageNotFoundException::forPageNotFound('Kampanjen finns inte.');
        }

        return $campaign;
    }

    private function input(): array
    {
        $data = $this->nullify($this->request->getPost(self::FIELDS));

        $data['title'] ??= '';
        $data['status'] ??= 'draft';
        $data['sort_order']   = (int) ($data['sort_order'] ?? 0);
        $data['show_on_home'] = $this->request->getPost('show_on_home') === '1' ? 1 : 0;

        return $data;
    }

    /**
     * Belopp: båda eller inget, och spelvärdet får inte vara lägre än priset.
     *
     * @return array<string, string>
     */
    private function amountErrors(array $data): array
    {
        $pay   = $data['pay_amount'];
        $value = $data['value_amount'];

        if (($pay === null) !== ($value === null)) {
            return ['value_amount' => 'Ange både pris och spelvärde — eller inget av dem.'];
        }

        if ($pay !== null && ctype_digit((string) $pay) && ctype_digit((string) $value) && (int) $value < (int) $pay) {
            return ['value_amount' => 'Spelvärdet bör inte vara lägre än priset.'];
        }

        if (($data['valid_from'] ?? null) !== null && ($data['valid_until'] ?? null) !== null && $data['valid_until'] < $data['valid_from']) {
            return ['valid_until' => 'Slutdatumet ligger före startdatumet.'];
        }

        return [];
    }
}
