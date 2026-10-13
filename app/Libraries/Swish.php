<?php

namespace App\Libraries;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

/**
 * Förifylld Swish-betalning enligt Swish utvecklarguide (Generate QR codes):
 *   https://app.swish.nu/1/p/sw/?sw=<nummer>&amt=<belopp>&msg=<meddelande>
 *
 * Samma adress fungerar som länk i mobilen (öppnar Swish-appen med allt
 * ifyllt, annars Swish nedladdningssida) och som QR-kod på datorn (mobilkameran
 * eller Swish-appens skanner). Utan edit-parametern är nummer, belopp och
 * meddelande låsta, så kunden kan inte ändra beloppet eller glömma ordernumret.
 */
final class Swish
{
    public static function paymentUrl(string $number, int $amount, string $message): string
    {
        return 'https://app.swish.nu/1/p/sw/?' . http_build_query([
            'sw'  => preg_replace('/\D/', '', $number),
            'amt' => number_format($amount, 2, '.', ''),
            'msg' => $message,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    /** QR-koden som inbäddad SVG. Vyn ritar den ljusa bakgrunden runt koden. */
    public static function qrSvg(string $url, string $label): string
    {
        $svg = (new QRCode(new QROptions([
            'outputInterface'  => QRMarkupSVG::class,
            'outputBase64'     => false,
            'svgAddXmlHeader'  => false,
            'eccLevel'         => EccLevel::M,
            'addQuietzone'     => true,
            'quietzoneSize'    => 2,
            'drawLightModules' => false,
            'connectPaths'     => true,
            'cssClass'         => 'swish-qr-code',
        ])))->render($url);

        return preg_replace('/^<svg /', '<svg role="img" aria-label="' . esc($label) . '" ', ltrim($svg), 1);
    }
}
