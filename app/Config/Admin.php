<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Admin (/admin/). Sätts per miljö i .env:
 *   admin.setupToken = <lång slumpsträng>
 *
 * Setup-sidan (/admin/setup/?token=…) kör databasmigrationer och skapar den
 * första admin-användaren — Loopia saknar SSH, så det här ersätter
 * `php spark migrate`. Tom token = setup är avstängd.
 */
class Admin extends BaseConfig
{
    /** Hemlig token för /admin/setup/. Minst 24 tecken, annars avstängd. */
    public string $setupToken = '';

    /** Utloggning efter så här lång inaktivitet. */
    public int $sessionTimeoutMinutes = 120;

    /** Max misslyckade inloggningsförsök per IP och 15 minuter. */
    public int $maxLoginAttempts = 5;

    /** Minsta lösenordslängd. */
    public int $minPasswordLength = 12;

    public function setupEnabled(): bool
    {
        return strlen($this->setupToken) >= 24;
    }
}
