<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Team Swing – spelarna och deras egna svar (underlag från spelarna 2026-11-01,
 * språkgranskat). Bilden ligger i Config\Images under nyckeln "team-<slug>".
 * Ordning: efternamn, förnamn.
 */
class TeamSwing extends BaseConfig
{
    /** Rubrikerna för spelarnas svar, i den ordning de visas. */
    public array $questions = [
        'indoor'  => 'Mål med inomhusgolfen',
        'outdoor' => 'Mål med utomhusgolfen',
        'why'     => 'Därför passar inomhusgolf mig',
        'memory'  => 'Bästa golfminnet',
    ];

    public array $players = [
        [
            'slug'    => 'wilhelm-ahlgren',
            'name'    => 'Wilhelm Ahlgren',
            'club'    => 'Kristianstads GK',
            'tagline' => 'Siktar på topp 5 i Nordic League',
            'answers' => [
                'indoor'  => ['Mitt mål med inomhusgolfen är att förbereda mig inför nästa säsong på Nordic League och spela Next Tour-tävlingar under vintern. Det är ett bra sätt att hålla sig konkurrenskraftig och fortsätta utvecklas även under offseason.'],
                'outdoor' => ['Mitt mål för utomhussäsongen 2027 är att nå en topp 5-placering på Nordic Leagues ranking.'],
                'why'     => ['Inomhusgolf passar mig eftersom det är en rolig och effektiv träningsform under vintern. Det går snabbt att spela och man får direkt feedback via TrackMan, vilket gör det lätt att utvecklas och bli bättre hela tiden.'],
                'memory'  => ['När jag kvalade in till min första HotelPlanner Tour-tävling. Jag gjorde eagle på hål 18 för att ta mig till särspel – och sedan hole in one på första särspelshålet för att ta mig in i tävlingen.'],
            ],
        ],
        [
            'slug'    => 'ola-johansson',
            'name'    => 'Ola Johansson',
            'club'    => 'Trummenäs GK',
            'tagline' => 'Vann Nordea Tour i Kristianstad 2016',
            'answers' => [
                'indoor'  => ['Mitt mål med inomhusgolfen är att fortsätta utveckla mitt spel och jobba på de delar som behöver förbättras. Då får jag de bästa förutsättningarna för att vara med och slåss om bra placeringar i simulatorgolftävlingarna.'],
                'outdoor' => ['Med tanke på familjesituationen och heltidsjobbet är det svårt att ha någon målsättning med utomhusgolfen just nu.'],
                'why'     => ['Inomhusgolfen passar mig väldigt bra eftersom det går mycket snabbare att spela. Det gör det också lättare att kombinera med familjelivet.'],
                'memory'  => ['Det finns många härliga minnen genom åren, men måste jag välja ett blir det när jag vann Nordea Tour i Kristianstad 2016.'],
            ],
        ],
        [
            'slug'    => 'oskar-johansson',
            'name'    => 'Oskar Johansson',
            'club'    => 'Trummenäs GK',
            'tagline' => 'Småbarnspappa med sikte på Next Tour',
            'answers' => [
                'indoor'  => ['Mitt mål med inomhusgolfen är att kvala in till Next Tour och tävla mot golfare från hela världen.'],
                'outdoor' => ['Med utomhusgolfen har jag inget tydligt mål, jag spelar mest för skojs skull. Ibland är jag med och tävlar, och det är roligt när det väl händer. Jag hoppas att det blir mer av det i framtiden.'],
                'why'     => ['Jag är småbarnspappa, och inomhusgolfen gör det lättare att få tid både till att träna och till att tävla. Jag behöver inte åka långt för att vara med på en tävling – det är bara att köra till Swing. Det passar mig perfekt.'],
                'memory'  => [
                    'Jag vet inte riktigt vilket som är mitt bästa golfminne, men förhoppningsvis bjuder den här vintern på några nya, spännande och minnesvärda ögonblick.',
                    'Ett minne som sticker ut är från i höstas, när jag för andra gången fick vara caddie åt en kompis på Omega European Masters i Crans-Montana i Schweiz. Han hade vunnit ett wildcard till tävlingen genom simulatorgolfen. Det var riktigt häftigt att komma så nära proffsen och se hur allt fungerar på den nivån – och det gav mig extra motivation att själv satsa mer på simulatorgolfen.',
                ],
            ],
        ],
        [
            'slug'    => 'wilhelm-johansson',
            'name'    => 'Wilhelm Johansson',
            'club'    => 'Carlskrona GK',
            'tagline' => 'Drömmer om collegegolf i USA',
            'answers' => [
                'indoor'  => ['Att träna hårt på alla delar av spelet och komma väl förberedd när utomhussäsongen drar igång igen på våren.'],
                'outdoor' => ['Att ta nästa steg och spela jämnare på en ännu högre nivå under 2027 och framåt – med målet att förhoppningsvis kunna åka till USA och spela collegegolf.'],
                'why'     => ['Det är ett väldigt behagligt sätt att göra större förändringar i spelet. Man får dessutom riktigt bra feedback genom siffror och annan data, vilket gör det lättare att förstå och utveckla sitt spel. Och framför allt: bekvämligheten och tillgången som finns hos Swing!'],
                'memory'  => ['Det var sjukt kul och lärorikt att få hänga med som caddie på svenska touren vid två tillfällen. Utöver det har årets tävlingar varit riktigt roliga, och golfresan till Marbella med kompisar i september var också ett minne!'],
            ],
        ],
        [
            'slug'    => 'anton-nilsson',
            'name'    => 'Anton Nilsson',
            'club'    => 'Kristianstads GK',
            'tagline' => 'Två gånger till Omega European Masters via simulatorn',
            'answers' => [
                'indoor'  => ['Mitt huvudsakliga mål är att bli en av de bästa simulatorgolfspelarna i världen. Jag vill också ge mig själv chanser att tävla på hög nivå utomhus genom att vinna wildcards och andra spelrättigheter i inomhustävlingar.'],
                'outdoor' => ['Just nu arbetar jag heltid som lärare, vilket inte ger mig chansen att satsa fullt ut på utomhusgolfen. Därför är det svårt att ha någon tydlig målsättning – men det kanske ändras i framtiden.'],
                'why'     => ['Eftersom jag arbetar heltid som lärare passar det mig utmärkt att tävla professionellt i inomhusgolf, som inte kräver några resor för att tävla internationellt.'],
                'memory'  => ['Helt klart mina två resor till Omega European Masters, som jag fått göra tack vare två vunna kval i simulatorn. Även om det inte gått toppenbra i Schweiz har det varit otroliga minnen.'],
            ],
        ],
    ];
}
