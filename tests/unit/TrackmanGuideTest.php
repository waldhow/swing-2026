<?php

namespace Tests\Unit;

use App\Libraries\TrackmanGuide;
use CodeIgniter\Test\CIUnitTestCase;
use RuntimeException;

/** TrackMan-guidens innehåll: avsnitt, hänvisningar, nivåer och Smash Factor-omdömet. */
final class TrackmanGuideTest extends CIUnitTestCase
{
    private TrackmanGuide $guide;

    protected function setUp(): void
    {
        parent::setUp();
        $this->guide = new TrackmanGuide();
    }

    /** @return list<array<string, mixed>> */
    private function sections(): array
    {
        return array_merge(...array_column($this->guide->parts(), 'sections'));
    }

    public function testSectionsHaveUniqueIdsAndHeadings(): void
    {
        $sections = $this->sections();
        $ids      = array_column($sections, 'id');

        $this->assertCount(33, $sections);
        $this->assertSame($ids, array_unique($ids), 'avsnitts-id måste vara unika');
        foreach ($sections as $s) {
            $this->assertNotSame('', trim($s['title']), "{$s['id']} saknar rubrik");
            $this->assertNotSame([], $s['blocks'], "{$s['id']} saknar innehåll");
        }
    }

    public function testKeyMetricsAndFocusPointToExistingSections(): void
    {
        $ids     = array_column($this->sections(), 'id');
        $metrics = $this->guide->keyMetrics();

        $this->assertCount(8, $metrics);
        foreach ($metrics as $m) {
            $this->assertContains('s' . $m['ref'], $ids, "{$m['name']} hänvisar till ett avsnitt som saknas");
        }

        $levels = array_column($this->guide->levels(), 0);
        $this->assertSame('all', $levels[0]);
        foreach ($this->sections() as $s) {
            $this->assertSame([], array_diff($s['focusIn'], $levels), "{$s['id']} har okänd fokusnivå");
            if (isset($s['lvl'])) {
                $this->assertContains($s['lvl'], $levels);
            }
        }
    }

    public function testFaqHasQuestionsAndAnswers(): void
    {
        $faq = $this->guide->faq();
        $this->assertCount(9, $faq);
        foreach ($faq as $qa) {
            $this->assertStringEndsWith('?', $qa['q']);
            $this->assertNotSame('', $qa['a']);
        }
    }

    public function testSmashVerdictFollowsReferenceTable(): void
    {
        $this->assertSame('Ofta stor förbättringspotential', TrackmanGuide::smashVerdict(1.30));
        $this->assertSame('Träffen eller energiöverföringen kan förbättras', TrackmanGuide::smashVerdict(1.35));
        $this->assertSame('Träffen eller energiöverföringen kan förbättras', TrackmanGuide::smashVerdict(1.42));
        $this->assertSame('Bra nivå för många amatörer', TrackmanGuide::smashVerdict(1.45));
        $this->assertSame('Mycket effektiv bollträff', TrackmanGuide::smashVerdict(1.48));
        $this->assertSame('Nära typiskt maximalt effektiv nivå med driver', TrackmanGuide::smashVerdict(1.50));
    }

    public function testMissingContentFailsLoudly(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'tmg');
        file_put_contents($file, '{"parts":[]}');

        try {
            $this->expectException(RuntimeException::class);
            new TrackmanGuide($file);
        } finally {
            unlink($file);
        }
    }
}
