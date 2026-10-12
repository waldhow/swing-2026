<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tävlingar. Publikt visas bara status 'published' (AI-spec 4.4): utkast och
 * inställda filtreras bort från "aktuella", liksom tävlingar som passerat.
 */
class CompetitionModel extends Model
{
    public const STATUSES     = ['draft' => 'Utkast', 'published' => 'Publicerad', 'cancelled' => 'Inställd'];
    public const VISIBILITIES = ['public' => 'Öppen för alla', 'private' => 'Privat (inbjudna)'];

    protected $table         = 'competitions';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'slug', 'title', 'visibility', 'status', 'format', 'summary', 'description',
        'starts_at', 'ends_at', 'registration_info', 'registration_url', 'price_info',
        'terms', 'terms_url', 'results_url',
    ];

    protected $validationRules = [
        // id behöver en egen regel för att {id}-platshållaren i is_unique ska fungera i CI 4.7.
        'id'               => 'permit_empty|is_natural_no_zero',
        'title'            => 'required|max_length[160]',
        'slug'             => 'required|max_length[160]|regex_match[/^[a-z0-9]+(?:-[a-z0-9]+)*$/]|is_unique[competitions.slug,id,{id}]',
        'visibility'       => 'required|in_list[public,private]',
        'status'           => 'required|in_list[draft,published,cancelled]',
        'starts_at'        => 'permit_empty|valid_date',
        'ends_at'          => 'permit_empty|valid_date',
        'registration_url' => 'permit_empty|valid_url_strict[https]|max_length[500]',
        'terms_url'        => 'permit_empty|valid_url_strict[https]|max_length[500]',
        'results_url'      => 'permit_empty|valid_url_strict[https]|max_length[500]',
    ];

    /**
     * Publicerade tävlingar som inte är avslutade: kommande, pågående eller
     * utan fastställt datum. Sorteras med närmast datum först.
     */
    public function current(?string $now = null): array
    {
        $now ??= date('Y-m-d H:i:s');

        return $this->where('status', 'published')
            ->groupStart()
                ->where('starts_at', null)
                ->orWhere('COALESCE(ends_at, starts_at) >=', $now)
            ->groupEnd()
            ->orderBy('starts_at IS NULL', '', false)
            ->orderBy('starts_at', 'ASC')
            ->findAll();
    }

    /** Publicerade tävlingar som har passerat — senaste först. */
    public function past(int $limit = 12, ?string $now = null): array
    {
        $now ??= date('Y-m-d H:i:s');

        return $this->where('status', 'published')
            ->where('starts_at IS NOT NULL', null, false)
            ->where('COALESCE(ends_at, starts_at) <', $now)
            ->orderBy('starts_at', 'DESC')
            ->findAll($limit);
    }

    public function findPublished(string $slug): ?array
    {
        return $this->where('slug', $slug)->where('status', 'published')->first();
    }

    /** Alla publicerade (för sitemap). */
    public function allPublished(): array
    {
        return $this->select('slug, updated_at')->where('status', 'published')->findAll();
    }

    /** "Kommande", "Pågår", "Avslutad" eller "Datum meddelas" — beräknat från datumen. */
    public static function timeState(array $competition, ?string $now = null): string
    {
        $now ??= date('Y-m-d H:i:s');
        $start = $competition['starts_at'] ?? null;
        $end   = $competition['ends_at'] ?? null ?: $start;

        if ($start === null) {
            return 'Datum meddelas';
        }
        if ($end < $now) {
            return 'Avslutad';
        }

        return $start <= $now ? 'Pågår' : 'Kommande';
    }
}
