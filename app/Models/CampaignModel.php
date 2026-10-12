<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Kampanjer med giltighetsperiod. Inga ständigt aktiva erbjudanden: en
 * publicerad kampanj måste ha ett slutdatum (AI-spec 4.4) och syns bara
 * mellan valid_from och valid_until.
 */
class CampaignModel extends Model
{
    public const STATUSES = ['draft' => 'Utkast', 'published' => 'Publicerad', 'unpublished' => 'Avpublicerad'];

    protected $table         = 'campaigns';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'title', 'summary', 'terms', 'pay_amount', 'value_amount', 'valid_from',
        'valid_until', 'status', 'show_on_home', 'sort_order',
    ];

    protected $validationRules = [
        'title'        => 'required|max_length[160]',
        'summary'      => 'permit_empty|max_length[300]',
        'status'       => 'required|in_list[draft,published,unpublished]',
        'pay_amount'   => 'permit_empty|is_natural_no_zero|less_than_equal_to[100000]',
        'value_amount' => 'permit_empty|is_natural_no_zero|less_than_equal_to[100000]',
        'valid_from'   => 'permit_empty|valid_date[Y-m-d]',
        'valid_until'  => 'campaign_end[status]',
        'show_on_home' => 'permit_empty|in_list[0,1]',
        'sort_order'   => 'permit_empty|integer',
    ];

    /** Publicerade kampanjer som gäller idag. */
    public function activeNow(?string $today = null): array
    {
        $today ??= date('Y-m-d');

        return $this->where('status', 'published')
            ->where('valid_until IS NOT NULL', null, false)
            ->where('valid_until >=', $today)
            ->groupStart()
                ->where('valid_from', null)
                ->orWhere('valid_from <=', $today)
            ->groupEnd()
            ->orderBy('sort_order', 'ASC')
            ->orderBy('valid_until', 'ASC')
            ->findAll();
    }

    /** Aktiva kampanjer som är beställningsbara spelpotter (har belopp). */
    public function activePots(?string $today = null): array
    {
        return array_values(array_filter(
            $this->activeNow($today),
            static fn (array $c): bool => (int) $c['pay_amount'] > 0 && (int) $c['value_amount'] > 0,
        ));
    }

    public function forHome(?string $today = null): array
    {
        return array_values(array_filter($this->activeNow($today), static fn (array $c): bool => (bool) $c['show_on_home']));
    }

    public function findActivePot(int $id, ?string $today = null): ?array
    {
        foreach ($this->activePots($today) as $campaign) {
            if ((int) $campaign['id'] === $id) {
                return $campaign;
            }
        }

        return null;
    }
}
