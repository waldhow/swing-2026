<?php

use CodeIgniter\Router\RouteCollection;

/**
 * Admin (/admin/). Inloggning krävs via AdminAuth-filtret, utom för login och
 * första uppsättningen. CSRF-skydd på alla POST (Config\Filters).
 *
 * @var RouteCollection $routes
 */
$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], static function (RouteCollection $routes): void {
    $routes->get('/', 'Dashboard::index');

    $routes->get('login', 'Auth::login');
    $routes->post('login', 'Auth::attempt');
    $routes->post('logout', 'Auth::logout');
    $routes->get('setup', 'Setup::index');
    $routes->post('setup', 'Setup::run');
    $routes->get('konto', 'Auth::account');
    $routes->post('konto', 'Auth::updateAccount');

    foreach (['tavlingar' => 'Competitions', 'kampanjer' => 'Campaigns'] as $path => $controller) {
        $routes->get($path, "{$controller}::index");
        $routes->get("{$path}/ny", "{$controller}::new");
        $routes->post("{$path}/ny", "{$controller}::create");
        $routes->get("{$path}/(:num)", "{$controller}::edit/\$1");
        $routes->post("{$path}/(:num)", "{$controller}::update/\$1");
        $routes->post("{$path}/(:num)/radera", "{$controller}::delete/\$1");
    }

    $routes->get('forfragningar', 'Inquiries::index');
    $routes->get('forfragningar/(:num)', 'Inquiries::show/$1');
    $routes->post('forfragningar/(:num)/status', 'Inquiries::status/$1');
    $routes->post('forfragningar/(:num)/radera', 'Inquiries::delete/$1');

    $routes->get('bestallningar', 'Orders::index');
    $routes->get('bestallningar/(:num)', 'Orders::show/$1');
    $routes->post('bestallningar/(:num)/skicka-igen', 'Orders::resend/$1');
});
