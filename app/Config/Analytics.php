<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Besöksstatistik med Google Analytics 4 – bara efter samtycke (TECH-03,
 * lagen om elektronisk kommunikation 9 kap. 28 §).
 *
 * Utan mätnings-ID finns ingen statistik och ingen samtyckesruta. Sätts i .env:
 *   analytics.ga4Id = G-XXXXXXXXXX
 */
class Analytics extends BaseConfig
{
    /** GA4-mätnings-ID (G-…). Tomt = av. */
    public string $ga4Id = '';

    /** Hur länge besökarens val sparas innan vi frågar igen. */
    public int $consentDays = 365;

    public function enabled(): bool
    {
        return preg_match('/^G-[A-Z0-9]{6,12}$/', $this->ga4Id) === 1;
    }
}
