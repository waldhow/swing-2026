<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->set404Override('App\Controllers\Pages::notFound');

// Indexerbara sidor ur sidregistret (Config\Pages). Sidorna nås med avslutande
// snedstreck — CanonicalUrl-filtret 301:ar /priser → /priser/.
foreach (array_keys(config('Pages')->pages) as $slug) {
    $routes->get($slug === '' ? '/' : $slug, 'Pages::show');
}

// Formulär postar till sin egen sida så att felmeddelanden visas på plats.
foreach (['kontakt', 'foretagsevent', 'gruppbokning', 'tavlingar'] as $slug) {
    $routes->post($slug, 'Inquiries::submit');
}
foreach (['spelpotter', 'presentkort', 'friskvard'] as $slug) {
    $routes->post($slug, 'Orders::submit');
}

$routes->get('tavlingar/(:segment)', 'Competitions::show/$1');
$routes->get('tack/forfragan', 'Inquiries::thanks');
$routes->get('tack/bestallning/(:segment)', 'Orders::thanks/$1');

$routes->get('sitemap.xml', 'Seo::sitemap');
// WordPress/Yoast-sitemaps som Google kan ha registrerade sedan gamla sajten.
foreach (['sitemap_index.xml', 'wp-sitemap.xml', 'page-sitemap.xml', 'post-sitemap.xml'] as $oldSitemap) {
    $routes->addRedirect($oldSitemap, 'sitemap.xml', 301);
}
$routes->get('robots.txt', 'Seo::robots');
$routes->get('llms.txt', 'Seo::llms');

require APPPATH . 'Config/RoutesAdmin.php';
