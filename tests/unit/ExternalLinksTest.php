<?php

namespace Tests\Unit;

use App\Filters\ExternalLinks;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * Skyddsnätet som öppnar alla externa länkar i ny flik.
 */
final class ExternalLinksTest extends CIUnitTestCase
{
    private function filter(string $html, string $type = 'text/html; charset=UTF-8'): string
    {
        helper('swing');
        $response = service('response')->setHeader('Content-Type', $type)->setBody($html);
        (new ExternalLinks())->after(service('request'), $response);

        return $response->getBody();
    }

    public function testExternalLinkGetsNewTabAndHint(): void
    {
        $this->assertSame(
            '<a href="https://www.facebook.com/swing/" rel="noopener" target="_blank">FB<span class="sr-only"> (öppnas i ny flik)</span></a>',
            $this->filter('<a href="https://www.facebook.com/swing/">FB</a>'),
        );
    }

    public function testExistingRelIsKept(): void
    {
        $this->assertSame(
            '<a href="https://maps.google.com/" rel="nofollow noopener" target="_blank">Karta<span class="sr-only"> (öppnas i ny flik)</span></a>',
            $this->filter('<a href="https://maps.google.com/" rel="nofollow">Karta</a>'),
        );
    }

    /** @return iterable<string, array{string}> */
    public static function provideUntouched(): iterable
    {
        yield 'intern' => ['<a href="/kontakt/">Kontakt</a>'];
        yield 'egen absolut adress' => ['<a href="' . rtrim((string) config('App')->baseURL, '/') . '/priser/">Priser</a>'];
        yield 'e-post' => ['<a href="mailto:info@swinggolf.se">Mejl</a>'];
        yield 'telefon' => ['<a href="tel:+46455323401">Ring</a>'];
        yield 'har redan target' => ['<a href="https://a.se/" target="_blank" rel="noopener">A</a>'];
        yield 'öppnar app (Swish)' => ['<a href="https://app.swish.nu/1/p/sw/?sw=1232172559&amp;amt=1000.00&amp;msg=W1042" data-same-tab>Betala med Swish</a>'];
        yield 'innehåll i script' => ['<script>var s = \'<a href="https://evil.example/">x</a>\';</script>'];
        yield 'ord i adressen' => ['<a href="https://a.se/" target="_self">A</a>'];
    }

    /**
     * @dataProvider provideUntouched
     */
    public function testLeavesOtherLinksAlone(string $html): void
    {
        $this->assertSame($html, $this->filter($html));
    }

    public function testOnlyHtmlResponses(): void
    {
        $json = '{"html":"<a href=\\"https://a.se/\\">A</a>"}';
        $this->assertSame($json, $this->filter($json, 'application/json'));
    }
}
