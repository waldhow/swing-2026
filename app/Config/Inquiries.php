<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Offert- och kontaktförfrågningar (TECH-04). Förfrågan sparas alltid i
 * databasen och mejlas till $notifyTo. Misslyckas mejlet syns förfrågan
 * ändå i admin, markerad som ej mejlad.
 */
class Inquiries extends BaseConfig
{
    public bool $enabled = true;

    /** Mottagare av nya förfrågningar. Sätts i .env: inquiries.notifyTo */
    public string $notifyTo = 'info@swinggolf.se';

    /** Förfrågningar raderas automatiskt efter så här många månader (integritetspolicyn). */
    public int $retentionMonths = 12;

    /** Max antal förfrågningar per IP och timme (spamskydd). */
    public int $maxPerHour = 5;
}
