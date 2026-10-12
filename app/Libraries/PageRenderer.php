<?php

namespace App\Libraries;

use App\Models\CampaignModel;
use App\Models\CompetitionModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Pages;
use Throwable;

/**
 * Renderar en sida ur sidregistret med metadata, brödsmulor, JSON-LD och de
 * data sidan behöver. Används av både sid- och formulärcontrollers så att en
 * sida med valideringsfel ser ut exakt som originalet.
 */
final class PageRenderer
{
    public function __construct(private readonly Pages $pages)
    {
    }

    /**
     * @param array<string, mixed> $extra t.ex. ['form' => ['values' => [], 'errors' => []]]
     */
    public function render(string $slug, array $extra = []): string
    {
        $page = $this->pages->find($slug);
        if ($page === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $schema = service('structuredData');
        $trail  = $this->trail($slug);
        $jsonLd = [];

        if ($slug !== '') {
            $jsonLd[] = $schema->breadcrumbs($trail);
        }
        if (($page['schema'] ?? '') === 'business') {
            $jsonLd[] = $schema->business();
        }

        $data = $this->dataFor($slug) + $extra;

        if (($page['schema'] ?? '') === 'faq') {
            $jsonLd[] = $schema->faq(service('faq')->all());
        }
        if (! empty($data['currentCompetitions'])) {
            array_push($jsonLd, ...$schema->events($data['currentCompetitions']));
        }

        return view('pages/' . $page['view'], $data + [
            'page'      => $page,
            'meta'      => [
                'title'       => $this->pages->fill($page['title']),
                'description' => $this->pages->fill($page['description']),
                'canonical'   => page_url($slug),
            ],
            'navActive' => $page['nav'] ?? '',
            'trail'     => $trail,
            'jsonLd'    => $jsonLd,
        ]);
    }

    /**
     * Brödsmulor från Start till sidan.
     *
     * @return list<array{label: string, url: string}>
     */
    public function trail(string $slug, array $tail = []): array
    {
        $trail = [['label' => 'Start', 'url' => page_url()]];
        if (isset($this->pages->parents[$slug])) {
            $parent  = $this->pages->parents[$slug];
            $trail[] = ['label' => $this->pages->label($parent), 'url' => page_url($parent)];
        }
        if ($slug !== '') {
            $trail[] = ['label' => $this->pages->label($slug), 'url' => page_url($slug)];
        }

        return array_merge($trail, $tail);
    }

    /**
     * Sidspecifika data. Databasen läses defensivt: är den nere visas sidan
     * utan dynamiskt innehåll i stället för ett serverfel.
     */
    private function dataFor(string $slug): array
    {
        return match ($slug) {
            ''               => [
                'homeCampaigns'       => $this->safe(static fn () => model(CampaignModel::class)->forHome()),
                'currentCompetitions' => $this->safe(static fn () => array_slice(model(CompetitionModel::class)->current(), 0, 3)),
                'faqItems'            => service('faq')->pick(['hur-bokar-jag', 'per-person', 'dorrkod', 'klubbor', 'var-ligger-swing']),
            ],
            'spelpotter'     => [
                'campaignPots' => $this->safe(static fn () => model(CampaignModel::class)->activePots()),
            ],
            'tavlingar'      => [
                'currentCompetitions' => $this->safe(static fn () => model(CompetitionModel::class)->current()),
                'pastCompetitions'    => $this->safe(static fn () => model(CompetitionModel::class)->past()),
            ],
            'vanliga-fragor' => ['faqGroups' => service('faq')->grouped()],
            'boka'           => ['faqItems' => service('faq')->pick(['dorrkod', 'avboka', 'skor', 'egna-bollar'])],
            'priser'         => ['faqItems' => service('faq')->pick(['per-person', 'hur-manga', 'spelpott', 'avboka'])],
            default          => [],
        };
    }

    private function safe(callable $query): array
    {
        try {
            return $query();
        } catch (Throwable $e) {
            log_message('error', 'Databasfel vid sidrendering: {msg}', ['msg' => $e->getMessage()]);

            return [];
        }
    }
}
