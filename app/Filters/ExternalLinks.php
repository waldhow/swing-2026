<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Alla länkar till andra webbplatser öppnas i ny flik (ägarens önskemål).
 *
 * Vyerna sätter det själva via external_attrs(); filtret är skyddsnätet för
 * länkar som skrivs direkt i vyer eller i admin. Länkar som redan har target,
 * eller som är märkta data-same-tab (t.ex. Swish, som öppnar appen), lämnas
 * orörda. Innehåll i <script> och <style> rörs aldrig.
 */
class ExternalLinks implements FilterInterface
{
    private const HINT = '<span class="sr-only"> (öppnas i ny flik)</span>';

    public function before(RequestInterface $request, $arguments = null)
    {
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $body = $response->getBody();
        if (! is_string($body) || $body === '' || ! str_contains($response->getHeaderLine('Content-Type'), 'text/html')) {
            return null;
        }

        $html = preg_replace_callback('#<(script|style)\b.*?</\1>|<a\b([^>]*)>(.*?)</a>#is', static function (array $m): string {
            if ($m[1] !== '') {
                return $m[0];
            }

            [$all, , $attrs, $content] = $m;
            if (preg_match('/\s(target|data-same-tab)(\s*=|\s|$)/i', $attrs) || ! preg_match('/\bhref="([^"]*)"/i', $attrs, $href)
                || ! is_external_url(html_entity_decode($href[1], ENT_QUOTES | ENT_HTML5))) {
                return $all;
            }

            $attrs = preg_match('/\brel="([^"]*)"/i', $attrs)
                ? preg_replace_callback('/\brel="([^"]*)"/i', static fn (array $r): string => 'rel="' . trim(str_contains($r[1], 'noopener') ? $r[1] : $r[1] . ' noopener') . '"', $attrs)
                : $attrs . ' rel="noopener"';

            return '<a' . $attrs . ' target="_blank">' . $content . (str_contains($content, 'ny flik') ? '' : self::HINT) . '</a>';
        }, $body);

        if (is_string($html) && $html !== $body) {
            $response->setBody($html);
        }

        return null;
    }
}
