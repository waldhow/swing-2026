# Swing Golf & Lounge – publik webbplats

Ny publik webbplats för Swing Golf & Lounge i Nättraby, byggd i **CodeIgniter 4** (PHP) med **MariaDB** och driftad på **Loopia**. Designen kommer från prototypen i `swingbooking/apps/public-web` (pub/Chesterfield-temat). Innehåll, SEO och krav följer kravdokumenten i `krav/`.

Bokning sker i **Sweetspot** tills det egna bokningssystemet lanseras (planerat hösten 2027). Sajten lovar ingen egen app och har ingen egen kassa. Spelpotter och presentkort beställs med Swish och hanteras i **swing-order**.

## Snabbstart

Krav: PHP 8.2+ med `intl`, `mbstring` och `mysqli`, Composer och MariaDB.

```bash
composer install
cp .env.example .env          # sätt CI_ENVIRONMENT = development, app.baseURL, databas
php spark migrate
php spark serve --port 8120   # http://localhost:8120
```

Lokalt behövs ingen SMTP. Mejlen loggas eller misslyckas tyst, och förfrågningar och ordrar sparas ändå i databasen. Sätt `orders.mailTo` om du vill se beställningsformulären.

Första admin-användaren skapas på `/admin/setup/?token=<admin.setupToken>`.

## Var ändrar jag vad?

| Vad | Fil |
|---|---|
| Adress, telefon, öppettider, priser, spelpotter, Epassi-belopp, presentkortsregler, partners | `app/Config/Swing.php` |
| Bokningsprovider, bokningslänk, bokningsappen, bokningssteg och voucher-instruktioner (bytet 2027) | `app/Config/Booking.php` |
| TrackMan Golf-appen (text och butikslänkar) | `app/Config/Swing.php` (`$trackmanApp`) |
| Sidornas URL, SEO-titel, meta description, meny och sidfot | `app/Config/Pages.php` |
| Beställningskanal mot swing-order (mejl/API), Swish-nummer | `app/Config/Orders.php` och `.env` |
| Godkända bilder och alt-texter | `app/Config/Images.php` |
| 301-omdirigeringar från gamla WordPress-URL:er | `app/Config/Redirects.php` |
| Vanliga frågor (FAQ-sidan, startsidan och FAQ-markup) | `app/Libraries/Faq.php` |
| Sidtexter | `app/Views/pages/*.php` |
| Tävlingar och kampanjer | Admin, `/admin/` |

Alla fakta läses från konfigurationen, så samma uppgift visas likadant på varje sida, i JSON-LD och i `llms.txt` (SEO-03). Prisperioderna valideras när sajten startar: de måste täcka 06–24 varje dag utan glapp eller överlapp.

## Struktur

```
app/Config/           Swing, Booking, Pages, Orders, Images, Redirects, Seo, Inquiries, Admin
app/Controllers/      Pages, Competitions, Inquiries, Orders, Seo + Admin/*
app/Filters/          CanonicalUrl (301 + avslutande snedstreck), AdminAuth
app/Libraries/        Pricing, Faq, StructuredData, PageRenderer, InquiryService, Orders/*
app/Models/           Competition, Campaign, Inquiry, Order, AdminUser
app/Views/layouts/    main.php (SEO-huvud, navigering, sidfot)
app/Views/partials/   återanvändbara komponenter (hero, pristabell, FAQ, formulär …)
app/Views/pages/      en vy per sida
public/assets/        site.css (prototypens tema, orört), swing.css (tillägg), site.js, bilder
bin/build-loopia.sh   bygger uppladdningspaket för Loopia
docs/                 drift, swing-order-kontraktet, bilder, kravstatus
krav/                 kravspecifikationerna
```

Delvyer renderas med `partial()` i stället för `view()`, så att variabler inte läcker mellan anropen.

## Tester och kvalitet

```bash
composer test    # PHPUnit: prisregler, låsta fakta, orderformat, alla sidors SEO, formulär, admin
composer lint
```

PHP-testerna kör SQLite i minnet och behöver ingen MariaDB.

Webbläsartester (Playwright) för mobilmeny, tangentbord, boknings-CTA, FAQ, formulärfel, 404 och horisontell scroll i 320–1280 px:

```bash
cd tests/e2e && npm install
BASE_URL=http://localhost:8120 npx playwright test           # kör helst mot produktionsläge, debugfältet stör i development
CHECK_EXTERNAL=1 BASE_URL=… npx playwright test               # även externa länkar
```

Kravstatus och testbevis finns i `docs/kravstatus.md`.

## Driftsättning

Se [`docs/drift-loopia.md`](docs/drift-loopia.md). I korthet: `composer build:loopia`, ladda upp `dist/loopia/public_html/` till webbroten och `dist/loopia/swing-app/` bredvid den, skapa `.env`, kör `/admin/setup/`.

## Öppna beslut

Se [`BLOCKERS.md`](BLOCKERS.md). Sajten publicerar bara bekräftad information, och det som väntar på beslut är avstängt eller utelämnat.
