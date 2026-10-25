<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\I18n\Time;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Pages;

/**
 * Alla sidor i sidregistret: laddar direkt som URL, unik metadata, en H1,
 * kanonisk URL, giltig JSON-LD och inga platshållare eller ogrundade
 * påståenden (AI-spec 6 och 8).
 */
final class PagesTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $namespace = 'App';

    /** Påståenden som inte får finnas på sajten (STR-01, CONTENT-05, B01, B10, TECH-06). */
    private const FORBIDDEN = [
        'Swing-app', 'Swing App', 'Stripe', 'Apple Pay', 'serveringstillstånd', 'alkoholservering',
        'över 250', '250 banor', 'millimeter', 'Swing Challenge', 'Swing Exclusive', 'Nicklastorp',
        'TODO', 'lorem', 'example.com', 'href="#"', 'javascript:void', 'aG Locker', '2002',
    ];

    protected function tearDown(): void
    {
        parent::tearDown();
        Factories::reset('config');
    }

    /** @return iterable<string, array{string}> */
    public static function provideSlugs(): iterable
    {
        foreach (array_keys((new Pages())->pages) as $slug) {
            yield $slug === '' ? 'start' : $slug => [$slug];
        }
    }

    private function fetch(string $slug): string
    {
        $result = $this->get($slug === '' ? '/' : $slug . '/');
        $result->assertStatus(200);

        // TestResponse::getBody() går via DOM-parsern (entiteter) — läs råsvaret.
        return $result->response()->getBody();
    }

    /** WCAG 2.5.3: pottkortens länknamn är den synliga texten (versaler bara via CSS). */
    public function testHomePotCardsUseVisibleTextAsName(): void
    {
        $html = $this->fetch('');
        preg_match_all('#<a class="offer[^"]*" href="/spelpotter/\#pott-\d+"[^>]*>#', $html, $links);
        $this->assertCount(count(config('Swing')->creditPots), $links[0]);
        foreach ($links[0] as $tag) {
            $this->assertStringNotContainsString('aria-label', $tag);
        }
        $this->assertStringContainsString('<span class="name">Swing 1000</span>', $html);
    }

    /** Utan mätnings-ID: ingen statistik, ingen samtyckesruta (TECH-03). */
    public function testNoAnalyticsWithoutMeasurementId(): void
    {
        $html = $this->fetch('');

        $this->assertStringNotContainsString('id="consent"', $html);
        $this->assertStringNotContainsString('data-consent-open', $html);
        $this->assertStringNotContainsString('googletagmanager', $html);
        $this->assertStringContainsString('inga cookies för statistik eller marknadsföring', strip_tags($this->fetch('integritetspolicy')));
    }

    /** Med mätnings-ID: samtyckesruta och sidfotslänk, men Google Analytics laddas aldrig av servern. */
    public function testAnalyticsOnlyAfterConsent(): void
    {
        config('Analytics')->ga4Id = 'G-TEST12345';
        $html = $this->fetch('');

        $this->assertMatchesRegularExpression('#<section class="consent" id="consent" aria-labelledby="consent-title" data-ga="G-TEST12345" data-days="365" hidden>#', $html);
        $this->assertStringContainsString('data-consent="denied">Neka</button>', $html);
        $this->assertStringContainsString('data-consent="granted">Godkänn statistik</button>', $html);
        $this->assertStringContainsString('<button type="button" class="foot-link" data-consent-open>Cookie-inställningar</button>', $html);
        $this->assertStringNotContainsString('googletagmanager', $html, 'skriptet laddas bara av site.js efter ja');

        $policy = strip_tags($this->fetch('integritetspolicy'));
        foreach (['Statistik med Google Analytics, bara om du godkänner det.', 'Google Analytics sparar inte din IP-adress', 'swing_consent', 'dra tillbaka ditt samtycke'] as $must) {
            $this->assertStringContainsString($must, $policy);
        }
        $this->assertStringNotContainsString('inga spårningsverktyg', $policy);
    }

    /** Gamla WordPress-sitemaps (registrerade i Search Console) leder till den nya. */
    public function testOldSitemapsRedirectToNewSitemap(): void
    {
        foreach (['sitemap_index.xml', 'wp-sitemap.xml', 'page-sitemap.xml', 'post-sitemap.xml'] as $old) {
            $result = $this->get($old);
            $result->assertStatus(301);
            $this->assertSame(site_url('sitemap.xml'), $result->getRedirectUrl(), $old);
        }
    }

    /** Brödsmulans första länk är beskrivande (Lighthouse link-text). */
    public function testBreadcrumbStartsWithDescriptiveLink(): void
    {
        $this->assertMatchesRegularExpression('#<nav class="crumbs"[^>]*>[\s\S]*?<a href="[^"]*">Startsida</a>#', $this->fetch('priser'));
    }

    /** Integritetspolicyn: riktiga fakta från konfigurationen, rätt myndighet, tillgänglig översikt. */
    public function testPrivacyPolicyIsCompleteAndCorrect(): void
    {
        $html = $this->fetch('integritetspolicy');
        $text = strip_tags($html);

        $this->assertStringNotContainsString('Datainspektionen', $text, 'myndigheten heter IMY sedan 2021');
        $this->assertStringContainsString('Integritetsskyddsmyndigheten (IMY)', $text);
        $this->assertStringContainsString(config('Swing')->legalName . ', org.nr ' . config('Swing')->orgNumber, $text);
        $this->assertStringContainsString('raderas där efter ' . config('Inquiries')->retentionMonths . ' månader', $text);
        $this->assertStringContainsString('raderas efter ' . config('Orders')->retentionDays . ' dagar', $text);
        $this->assertStringContainsString('3 kap. 10 § dataskyddslagen', $text);
        foreach (['Loopia', 'Google Workspace', 'Vercel och Supabase', 'Slack', 'Epassi', 'Data Privacy Framework', 'dataportabilitet', 'Kamerabevakning i lokalen', 'skyltat på plats', 'inom EU (Irland)'] as $must) {
            $this->assertStringContainsString($must, $text, "integritetspolicyn ska nämna {$must}");
        }
        $this->assertStringContainsString('<table class="dtable dtable-text" role="table" aria-labelledby="oversikt">', $html);
        $this->assertSame(7, substr_count($html, 'data-label="Hur länge"'), 'varje rad har etiketter för mobilvisningen');
        $this->assertStringNotContainsString('marknadsföring och logistik', $text, 'den gamla policyns delning för marknadsföring stämmer inte');
    }

    /**
     * Alla länkar till andra webbplatser öppnas i ny flik (ägarens önskemål).
     * Undantag: data-same-tab (Swish-länken öppnar appen).
     *
     * @dataProvider provideSlugs
     */
    public function testExternalLinksOpenInNewTab(string $slug): void
    {
        $html = preg_replace('#<script\b.*?</script>#is', '', $this->fetch($slug));
        preg_match_all('#<a\b[^>]*\bhref="(https?://[^"]+)"[^>]*>#i', $html, $links, PREG_SET_ORDER);

        foreach ($links as [$tag, $href]) {
            if (! is_external_url(html_entity_decode($href)) || str_contains($tag, 'data-same-tab')) {
                continue;
            }
            $this->assertStringContainsString('target="_blank"', $tag, "{$slug}: {$href} öppnas inte i ny flik");
            $this->assertMatchesRegularExpression('/rel="[^"]*noopener/', $tag, "{$slug}: {$href} saknar rel=noopener");
        }
    }

    /**
     * @dataProvider provideSlugs
     */
    public function testPageHasSeoBasicsAndNoForbiddenContent(string $slug): void
    {
        $html  = $this->fetch($slug);
        $pages = config('Pages');
        $page  = $pages->find($slug);

        $this->assertSame(1, preg_match_all('/<h1\b/', $html), 'exakt en H1');
        $this->assertStringContainsString('<html lang="sv">', $html);
        $this->assertStringContainsString('<title>' . esc($pages->fill($page['title'])) . '</title>', $html);
        $this->assertStringContainsString('<meta name="description" content="' . esc($pages->fill($page['description'])) . '"', $html);
        $this->assertStringContainsString('<link rel="canonical" href="' . page_url($slug) . '"', $html);
        $this->assertMatchesRegularExpression('#<link rel="canonical" href="[^"]*/"#', $html, 'kanonisk URL med avslutande snedstreck');

        foreach (self::FORBIDDEN as $phrase) {
            $this->assertStringNotContainsStringIgnoringCase($phrase, $html, "{$slug}: får inte innehålla \"{$phrase}\"");
        }

        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $blocks);
        foreach ($blocks[1] as $json) {
            $this->assertIsArray(json_decode($json, true), 'giltig JSON-LD');
            $this->assertStringNotContainsString('aggregateRating', $json);
            $this->assertStringNotContainsString('"review"', $json);
        }
        if ($slug !== '') {
            $this->assertStringContainsString('"BreadcrumbList"', $html);
        }
    }

    /**
     * Länkkontroll (TECH-13, QA-04): varje intern länk på varje sida går till
     * en sida som svarar 200, och varje ankare (#…) finns på sidan.
     */
    public function testAllInternalLinksAndAnchorsResolve(): void
    {
        $checked = [];

        foreach (array_keys(config('Pages')->pages) as $slug) {
            $html = $this->fetch($slug);

            $this->assertDoesNotMatchRegularExpression('/href=""/', $html, "{$slug}: tom länk");

            preg_match_all('/href="(\/[^"#?]*)(#[^"]*)?"/', $html, $links, PREG_SET_ORDER);
            foreach ($links as $link) {
                $path = $link[1];
                if (str_starts_with($path, '/assets/') || preg_match('/\.(ico|svg|png|jpg|webp|css|js)$/', $path) || isset($checked[$path])) {
                    continue;
                }
                $checked[$path] = true;
                $this->assertSame(200, $this->get($path)->response()->getStatusCode(), "{$slug}: intern länk {$path} svarar inte 200");
            }

            preg_match_all('/href="#([^"]+)"/', $html, $anchors);
            foreach (array_unique($anchors[1]) as $id) {
                $this->assertStringContainsString('id="' . $id . '"', $html, "{$slug}: ankaret #{$id} saknas");
            }
        }

        $this->assertGreaterThanOrEqual(count(config('Pages')->pages), count($checked), 'alla sidor ska nås via interna länkar');
    }

    /** UX-02: varje innehållssida nås från huvudmenyn (villkorssidorna ligger i sidfoten). */
    public function testEveryContentPageIsInTheMainMenu(): void
    {
        $pages   = config('Pages');
        $inMenu  = [];
        foreach ($pages->mainNav as $item) {
            $inMenu[] = $item['slug'];
            array_push($inMenu, ...($item['children'] ?? []));
        }
        $missing = array_diff(array_keys($pages->pages), $inMenu, ['', 'kopvillkor', 'integritetspolicy']);
        $this->assertSame([], array_values($missing), 'sidor som saknas i menyn');

        $html = $this->fetch('');
        foreach (array_unique($inMenu) as $slug) {
            $this->assertMatchesRegularExpression('#<nav class="nav-links"[\s\S]*href="/' . preg_quote($slug, '#') . '/"[\s\S]*</nav>#', $html, "{$slug} saknas i menyns HTML");
        }
    }

    public function testBreadcrumbsReflectHierarchy(): void
    {
        $html = $this->fetch('trackman-io');
        $this->assertMatchesRegularExpression('#<nav class="crumbs"[\s\S]*>Startsida</a>[\s\S]*Inomhusgolf[\s\S]*TrackMan iO &amp; simulatorer#', $html);
        $this->assertStringContainsString('"position":2,"name":"Inomhusgolf"', $html);
    }

    public function testTitlesAndDescriptionsAreUnique(): void
    {
        $pages        = config('Pages');
        $titles       = array_map(static fn (array $p): string => $pages->fill($p['title']), $pages->pages);
        $descriptions = array_map(static fn (array $p): string => $pages->fill($p['description']), $pages->pages);

        $this->assertSame(count($titles), count(array_unique($titles)));
        $this->assertSame(count($descriptions), count(array_unique($descriptions)));
    }

    public function testBookingCtaPointsToVerifiedSweetspotUrl(): void
    {
        $html = $this->fetch('');

        $this->assertStringContainsString('href="https://book.sweetspot.io/clubs/swing-golf-lounge"', $html);
        $this->assertStringContainsString('Boka tid', $html);
        $this->assertStringContainsString('href="/spelpotter/"', $html, 'sekundär CTA Köp spelpott');
    }

    public function testBookingLinksOpenInNewTab(): void
    {
        $found = 0;
        foreach (array_keys(config('Pages')->pages) as $slug) {
            $html = $this->fetch($slug);
            preg_match_all('#<a [^>]*href="https://book\.sweetspot\.io[^"]*"[^>]*>(.*?)</a>#s', $html, $links, PREG_SET_ORDER);
            foreach ($links as [$tag, $text]) {
                $found++;
                $this->assertStringContainsString('target="_blank" rel="noopener"', $tag, "{$slug}: bokningslänk utan ny flik");
                $this->assertStringContainsString('(öppnas i ny flik)', $text, "{$slug}: bokningslänk utan skärmläsartext");
            }
        }
        $this->assertGreaterThan(30, $found);
    }

    public function testPartnerLinksOpenInNewTab(): void
    {
        foreach (['inomhusgolf', 'foretagsevent', 'om-oss', 'vanliga-fragor'] as $slug) {
            $this->assertStringContainsString(
                '<a href="https://www.nattrabyvagkrog.se/" target="_blank" rel="noopener">Nättraby Vägkrog<span class="sr-only"> (öppnas i ny flik)</span></a>',
                $this->fetch($slug),
                "{$slug}: Nättraby Vägkrog ska länkas i ny flik",
            );
        }
    }

    public function testSweetspotAppIsRecommendedFirst(): void
    {
        $home = $this->fetch('');
        $this->assertLessThan(strpos($home, 'aria-labelledby="valkommen"'), strpos($home, 'aria-labelledby="appar"'), 'appsektionen ligger direkt efter heron');
        $this->assertStringContainsString('Boka enklast i Sweetspot-appen', $home);

        // Sök inom <main> — menyns förklaringar innehåller också "Så bokar du".
        $boka = substr($this->fetch('boka'), strpos($this->fetch('boka'), '<main'));
        $this->assertLessThan(strpos($boka, '<h2>Så bokar du</h2>'), strpos($boka, 'Boka i Sweetspot-appen'), 'appen före webbokningen');
        $this->assertStringContainsString('Ladda ner Sweetspot-appen och sök efter Swing Golf &amp; Lounge.', $boka);
    }

    public function testAppCardsLinkToVerifiedStores(): void
    {
        foreach (['', 'boka', 'infor-besoket'] as $slug) {
            $html = $this->fetch($slug);
            $this->assertStringContainsString('href="https://apps.apple.com/se/app/sweetspot/id1234606187"', $html);
            $this->assertStringContainsString('href="https://play.google.com/store/apps/details?id=com.hisweetspot.sweetspot"', $html);
            $this->assertStringContainsString('href="https://apps.apple.com/se/app/trackman-golf/id1245413916"', $html);
            $this->assertStringContainsString('dk.TrackMan.Range', $html);
            $this->assertStringContainsString('alt="Ladda ned Sweetspot i App Store"', $html);
        }
    }

    public function testFactsAreConsistent(): void
    {
        foreach (['', 'kontakt', 'priser', 'infor-besoket'] as $slug) {
            $html = $this->fetch($slug);
            $this->assertStringContainsString('tel:+46455323401', $html);
            $this->assertStringContainsString('info@swinggolf.se', $html);
            $this->assertStringContainsString('Idrottsvägen 2B', $html);
        }

        $prices = $this->fetch('priser');
        foreach (['Alla dagar 06.00–08.00', 'Måndag–fredag 08.00–15.00', 'Måndag–fredag 15.00–00.00', 'Lördag–söndag 08.00–00.00'] as $label) {
            $this->assertStringContainsString($label, $prices);
        }
        $this->assertStringContainsString("37,50\u{00A0}kr", $prices);
        $this->assertStringContainsString("62,50\u{00A0}kr", $prices);

        $visit = $this->fetch('infor-besoket');
        $this->assertStringContainsString('<strong>#</strong>', $visit);
        $this->assertStringContainsString('20 minuter före och 20 minuter efter bokningens starttid', $visit);
    }

    public function testEpassiAndOrdinaryPotsAreShownSeparately(): void
    {
        $html = $this->fetch('friskvard');
        $this->assertStringContainsString('Epassi-alternativ', $html);
        $this->assertStringContainsString("2\u{00A0}500\u{00A0}kr", $html);
        $this->assertStringContainsString("2\u{00A0}700\u{00A0}kr", $html);
        $this->assertStringNotContainsString("2\u{00A0}200\u{00A0}kr", $html, 'gamla felaktiga Epassi-värdet');
        $this->assertStringNotContainsString('Ordinarie spelpotter', $html);

        $credits = $this->fetch('spelpotter');
        foreach ([["1\u{00A0}000", "1\u{00A0}150"], ["2\u{00A0}000", "2\u{00A0}400"], ["3\u{00A0}000", "3\u{00A0}750"]] as [$pay, $value]) {
            $this->assertStringContainsString('<span class="amt">' . $value . '</span>', $credits);
            $this->assertStringContainsString('<span class="v">' . $pay . "\u{00A0}kr</span>", $credits);
        }
        $this->assertSame(3, substr_count($credits, 'class="pot-radio"'), 'korten är själva valet i beställningen');
        $this->assertStringContainsString('Den är personlig och kan inte överlåtas.', $credits);
        $this->assertStringNotContainsString('kan överlåtas', $credits);
        $this->assertStringContainsString('Spelpotten är personlig och kan inte överlåtas.', $this->fetch('kopvillkor'));
        $this->assertStringNotContainsString("2\u{00A0}700\u{00A0}kr", $credits);
    }

    public function testFaqPageHasFaqMarkupMatchingVisibleQuestions(): void
    {
        $html = $this->fetch('vanliga-fragor');
        $this->assertStringContainsString('"FAQPage"', $html);
        $this->assertStringContainsString('Hur fungerar dörrkoden?', $html);
    }

    public function testPromotionShowsUnderHeroOnlyWhileValid(): void
    {
        foreach (['2026-10-25 08:00' => true, '2026-10-30 23:30' => true, '2026-10-24 23:59' => false, '2026-10-31 00:01' => false] as $now => $visible) {
            Time::setTestNow($now, 'Europe/Stockholm');
            $html = $this->fetch('');
            if ($visible) {
                $this->assertMatchesRegularExpression('#</header>(?:\s|<!--.*?-->)*<section class="sec promo" aria-labelledby="erbjudande-hostlov-2026">#', $html, "{$now}: direkt under hero");
                $this->assertStringContainsString('<code>swing-hostlov</code>', $html);
                $this->assertStringContainsString('Lovet blev just <em>varmare.</em>', $html);
            } else {
                $this->assertStringNotContainsString('swing-hostlov', $html, "{$now}: erbjudandet ska vara dolt");
            }
        }
        Time::setTestNow();
    }

    public function testTrackmanGuideShowsWholeGuideWithArticleAndFaqMarkup(): void
    {
        $html = $this->fetch('trackman-guide');

        $this->assertSame(33, substr_count($html, 'class="tmg-sec"'));
        $this->assertSame(1, substr_count($html, '<h1'));
        $this->assertStringContainsString('"@type":"Article"', $html);
        $this->assertStringContainsString('"FAQPage"', $html);
        $this->assertStringContainsString('Vad är en bra Smash Factor med driver?', html_entity_decode($html));
        $this->assertMatchesRegularExpression('#<nav class="crumbs"[\s\S]*TrackMan iO &amp; simulatorer[\s\S]*TrackMan-guide#', $html);

        // Nivåval, innehållsrad och kalkylatorns knappar kräver JS – dolda utan.
        $this->assertMatchesRegularExpression('#class="tmg-level"[^>]*data-js-only hidden#', $html);
        $this->assertMatchesRegularExpression('#class="tmg-bar" data-js-only hidden#', $html);
        $this->assertStringContainsString('<span class="tmg-calc-value" data-value>1,48</span>', $html);

        // Alla interna ankare i guiden leder till ett avsnitt som finns.
        preg_match_all('#href="\#(s\d+|[a-z-]+)"#', $html, $anchors);
        foreach (array_unique($anchors[1]) as $id) {
            $this->assertStringContainsString('id="' . $id . '"', $html, "ankaret #{$id} saknas");
        }
    }

    public function testHomeAndTrackmanPageLeadToTheGuide(): void
    {
        $home = $this->fetch('');
        $this->assertStringContainsString('<h2 id="guide">Förstå dina TrackMan-siffror</h2>', $home);
        preg_match_all('#href="/trackman-guide/\#s\d+"#', $home, $metrics);
        $this->assertCount(8, $metrics[0], 'startsidan visar de åtta siffrorna');

        $this->assertStringContainsString('href="/trackman-guide/"', $this->fetch('trackman-io'));
    }

    public function testTrailingSlashRedirect(): void
    {
        $result = $this->get('priser');
        $result->assertStatus(301);
        $this->assertSame(page_url('priser'), $result->getRedirectUrl());
    }

    /** @return iterable<string, array{string, string}> */
    public static function provideLegacyUrls(): iterable
    {
        yield 'priser-medlemskap' => ['priser-medlemskap/', 'priser'];
        yield 'vara-simulatorer' => ['vara-simulatorer/', 'trackman-io'];
        yield 'trackman-artikel' => ['trackman-io-gjord-for-inomhusgolf/', 'trackman-io'];
        yield 'epassi' => ['epassi-swing-golf-lounge/', 'friskvard'];
        yield 'veckogolf' => ['veckogolf/', 'tavlingar'];
        yield 'juniortouren' => ['juniortouren-2026/', 'tavlingar'];
        yield 'bekraftelse' => ['bekraftelse-kop/', 'spelpotter'];
        yield 'foretag' => ['foretag', 'foretagsevent'];
        yield 'kopvillkor-pdf' => ['wp-content/uploads/2024/01/Kopvillkor-for-Swing-Golf-Lounge-2023-12-15.pdf', 'kopvillkor'];
    }

    /**
     * @dataProvider provideLegacyUrls
     */
    public function testLegacyUrlsRedirectInOneHop(string $old, string $target): void
    {
        $result = $this->get($old);
        $result->assertStatus(301);
        $this->assertSame(page_url($target), $result->getRedirectUrl());
    }

    public function testUnknownPageGives404WithHelpfulLinks(): void
    {
        $result = $this->get('finns-inte/');
        $result->assertStatus(404);
        $result->assertSee('Sidan hittades inte');
        $this->assertStringContainsString('<meta name="robots" content="noindex', $result->response()->getBody());
    }

    public function testNotIndexableByDefault(): void
    {
        $this->get('robots.txt')->assertSee('Disallow: /');
        $this->assertStringContainsString('<meta name="robots" content="noindex, nofollow"', $this->fetch(''));
    }

    public function testIndexableInProduction(): void
    {
        config('Seo')->indexable = true;

        $robots = $this->get('robots.txt');
        $robots->assertSee('Sitemap: ' . base_url('sitemap.xml'));
        $robots->assertSee('Disallow: /admin/');
        $this->assertStringContainsString('<meta name="robots" content="index, follow"', $this->fetch(''));
    }

    public function testSitemapListsAllPagesAndNothingPrivate(): void
    {
        $xml = $this->get('sitemap.xml')->response()->getBody();
        $this->assertStringStartsWith('<?xml', $xml);
        $this->assertNotFalse(simplexml_load_string($xml));

        foreach (array_keys(config('Pages')->pages) as $slug) {
            $this->assertStringContainsString('<loc>' . page_url($slug) . '</loc>', $xml);
        }
        $this->assertStringNotContainsString('/tack/', $xml);
        $this->assertStringNotContainsString('/admin', $xml);
    }

    public function testCompetitionsAreFilteredAndGetEventMarkup(): void
    {
        $future = date('Y-m-d 18:00:00', strtotime('+10 days'));
        $past   = date('Y-m-d 18:00:00', strtotime('-10 days'));
        $rows   = [
            ['slug' => 'kommande', 'title' => 'Kommande tävling', 'status' => 'published', 'starts_at' => $future],
            ['slug' => 'utkast', 'title' => 'Utkasttävling', 'status' => 'draft', 'starts_at' => $future],
            ['slug' => 'installd', 'title' => 'Inställd tävling', 'status' => 'cancelled', 'starts_at' => $future],
            ['slug' => 'passerad', 'title' => 'Passerad tävling', 'status' => 'published', 'starts_at' => $past],
            ['slug' => 'utan-datum', 'title' => 'Tävling utan datum', 'status' => 'published', 'starts_at' => null],
        ];
        foreach ($rows as $row) {
            $this->db->table('competitions')->insert($row + ['visibility' => 'public', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $html = $this->fetch('tavlingar');
        $this->assertStringContainsString('Kommande tävling', $html);
        $this->assertStringContainsString('Tävling utan datum', $html);
        $this->assertStringContainsString('Datum meddelas', $html);
        $this->assertStringNotContainsString('Utkasttävling', $html);
        $this->assertStringNotContainsString('Inställd tävling', $html);
        $this->assertStringContainsString('Tidigare tävlingar', $html);
        $this->assertSame(1, substr_count($html, '"SportsEvent"'), 'Event-markup bara för publicerad tävling med datum');

        $this->fetch('tavlingar/kommande');
        $this->get('tavlingar/utkast/')->assertStatus(404);
        $this->assertStringContainsString(page_url('tavlingar/kommande'), $this->get('sitemap.xml')->response()->getBody());
    }

    public function testCampaignsRespectStatusAndValidity(): void
    {
        $now  = date('Y-m-d H:i:s');
        $base = ['summary' => null, 'terms' => 'Gäller så länge kampanjen pågår.', 'show_on_home' => 1, 'sort_order' => 0, 'created_at' => $now, 'updated_at' => $now];
        $this->db->table('campaigns')->insertBatch([
            $base + ['title' => 'Höstkampanj', 'status' => 'published', 'pay_amount' => 2500, 'value_amount' => 4000, 'valid_from' => date('Y-m-d', strtotime('-1 day')), 'valid_until' => date('Y-m-d', strtotime('+5 days'))],
            $base + ['title' => 'Utkastkampanj', 'status' => 'draft', 'pay_amount' => 2500, 'value_amount' => 4000, 'valid_from' => null, 'valid_until' => date('Y-m-d', strtotime('+5 days'))],
            $base + ['title' => 'Utgången kampanj', 'status' => 'published', 'pay_amount' => 2500, 'value_amount' => 4000, 'valid_from' => null, 'valid_until' => date('Y-m-d', strtotime('-1 day'))],
        ]);

        $credits = $this->fetch('spelpotter');
        $this->assertStringContainsString('Höstkampanj', $credits);
        $this->assertStringNotContainsString('Utkastkampanj', $credits);
        $this->assertStringNotContainsString('Utgången kampanj', $credits);

        $this->assertStringContainsString('Höstkampanj', $this->fetch(''));
    }
}
