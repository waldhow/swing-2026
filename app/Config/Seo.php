<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Sökmotorinställningar. Indexering är avstängd tills den uttryckligen slås
 * på i produktions-.env (seo.indexable = true) — då kan en testdomän aldrig
 * råka hamna i Google. När false: robots.txt spärrar allt och varje sida får
 * <meta name="robots" content="noindex">.
 */
class Seo extends BaseConfig
{
    public bool $indexable = false;
}
