<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Inga hemligheter i kod eller publika filer (TECH-09). .env är gitignorerad;
 * .env.example får bara innehålla variabelnamn och säkra platshållare.
 */
final class SecretsTest extends CIUnitTestCase
{
    private const PATTERNS = [
        '/sk_(live|test)_[0-9a-zA-Z]{10,}/'      => 'Stripe-nyckel',
        '/whsec_[0-9a-zA-Z]{10,}/'                => 'webhookhemlighet',
        '/-----BEGIN (RSA |EC )?PRIVATE KEY-----/' => 'privat nyckel',
        '/hex2bin:[0-9a-f]{32,}/'                 => 'krypteringsnyckel',
        '/xox[baprs]-[0-9A-Za-z-]{10,}/'          => 'Slack-token',
    ];

    public function testNoSecretsInSourceOrPublicFiles(): void
    {
        foreach ([APPPATH, FCPATH, ROOTPATH . 'bin', ROOTPATH . 'docs'] as $dir) {
            if (! is_dir($dir)) {
                continue;
            }
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
            foreach ($files as $file) {
                if (! preg_match('/\\.(php|js|css|html|md|sh|json|txt)$/', $file->getFilename()) || str_contains($file->getPathname(), '/assets/photos/')) {
                    continue;
                }
                $content = file_get_contents($file->getPathname());
                foreach (self::PATTERNS as $pattern => $label) {
                    $this->assertDoesNotMatchRegularExpression($pattern, $content, "{$label} i {$file->getPathname()}");
                }
            }
        }
    }

    public function testEnvExampleHasNoSecretValues(): void
    {
        $example = file_get_contents(ROOTPATH . '.env.example');

        foreach (['database.default.password', 'encryption.key', 'email.SMTPPass', 'orders.apiToken', 'admin.setupToken'] as $key) {
            $this->assertMatchesRegularExpression('/^#?\\s*' . preg_quote($key, '/') . '\\s*=\\s*$/m', $example, "{$key} ska vara tom i .env.example");
        }
    }

    public function testEnvIsGitIgnored(): void
    {
        $this->assertMatchesRegularExpression('/^\\.env$/m', file_get_contents(ROOTPATH . '.gitignore'));
    }
}
