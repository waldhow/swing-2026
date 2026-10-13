# Besöksstatistik (Google Analytics 4)

- **Mätnings-ID:** `G-LTZ703WN8H` (flödet swinggolf.se), satt med `analytics.ga4Id` i `.env`. Utan ID finns ingen statistik och ingen cookie-ruta.
- **Uppsatt:** 2026-10-13.

## Samtycke (TECH-03)

- Google Analytics laddas **bara efter ja** i cookie-rutan, med Google signals och annonsfunktioner avstängda. Se `app/Views/partials/consent.php` och avsnittet om samtycke i `public/assets/site.js`.
- Valet sparas i den nödvändiga cookien `swing_consent` (`granted`/`denied`) i 12 månader (`Analytics::$consentDays`).
- "Cookie-inställningar" i sidfoten öppnar rutan igen. Ett nej i efterhand raderar `_ga`-cookies och laddar om sidan.
- Integritetspolicyn beskriver Google Analytics automatiskt när `analytics.ga4Id` är satt.

## Händelser

Alla händelser skickas från `public/assets/site.js` och bara efter samtycke. Inga namn, e-postadresser eller ordernummer skickas.

| Händelse | När | Parametrar |
|---|---|---|
| `klick_boka` | Klick på länk till Sweetspot, på webben eller i App Store/Google Play | `placering`, `mal` (webb, app_store, google_play), `sida` |
| `klick_kop_spelpott` | Klick på `.btn` eller `.offer` som leder till `/spelpotter/` | `placering`, `pott` (t.ex. pott-2000), `sida` |
| `klick_begar_offert` | Klick på `.btn` som leder till `/foretagsevent/#offert` | `placering`, `sida` |
| `generate_lead` | Tacksidan för förfrågningar har laddats | `formular` (kontakt, foretag, grupp, tavling) |
| `bestallning` | Orderns tacksida har laddats, en gång per order | `produkt`, `value`, `currency` |

Hur klicken räknas:

- **Placering** avgörs av var länken sitter: `meny` (`.nav`), `hero` (`.hero`, `.phero`), `cta` (`.cta-band`), `appar` (`.apps`), `pottkort` (`.offer`), annars `sida`.
- **Undantag:** länkar i menyernas undersidor (`.nav-group`), brödsmulor och sidfot räknas inte.
- **Regeln** utgår från länkens adress. Nya knappar som pekar på samma mål mäts därför automatiskt.

## I Google Analytics

- **Anpassade dimensioner** (omfattning Händelse): `placering`, `mal`, `pott`, `formular` och `produkt`. De är skapade 2026-10-13.
- **Viktiga händelser:** `generate_lead`, `bestallning` och `klick_boka`.
- **Datalagring:** 14 månader, som integritetspolicyn lovar ("högst 14 månader").
- **Google signals och personliga annonser:** avstängda.

## Tester

- `tests/feature/PagesTest.php`: rutan och sidfotslänken finns bara när ett mätnings-ID är satt, och Google-skriptet finns aldrig i serverns HTML.
- `tests/feature/FormsTest.php`: markeringarna på tacksidorna innehåller inga personuppgifter.
- `tests/e2e/critical.spec.js`:
  - Samtyckesflödet, och att händelser skickas med och inte skickas utan samtycke.
  - Anrop till Google stoppas och navigeringen hindras, så att inget skickas.
  - Testerna hoppas över om sajten saknar mätnings-ID. Lokalt kan de köras mot en server som startats med `analytics.ga4Id` satt som miljövariabel.
