<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        // Hård sidcachning: hela sidan cachas i en timme och serveras
        // från cache utan att controllern körs. Rensa med `php spark cache:clear`.
        $this->cachePage(HOUR);

        return view('home/index', [
            'title' => 'Swing Golf & Lounge',
        ]);
    }
}
