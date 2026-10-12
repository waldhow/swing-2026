# Kravstatus – Swing AI implementation report

Gäller kodomgången 2026-10-11. Krav-ID enligt `krav/ai/data/requirements.json` (51 krav). Följer `krav/ai/IMPLEMENTATION_STATUS_TEMPLATE.md`.

## 1. Byggt nu

- Ny publik sajt i CodeIgniter 4.7 + MariaDB med prototypens design (pub/Chesterfield): 16 sidor ur kravens sidstruktur, köpvillkor, integritetspolicy, tävlingsdetaljer, tacksidor och 404.
- Central, validerad affärskonfiguration (`Config\Swing`, `Config\Booking`, `Config\Pages`) och en utbytbar bokningsprovider.
- Offert- och kontaktformulär med databas och mejl. Beställning av spelpott och presentkort med Swish via swing-order, både mejlkanal och förberedd API-kanal.
- Admin för tävlingar, kampanjer, förfrågningar och beställningar.
- Teknisk SEO: unik metadata, kanoniska URL:er, JSON-LD, sitemap, robots, llms.txt och 301 från WordPress.
- Tester: 118 PHPUnit-tester och 40 Playwright-tester. Bygg för Loopia.

**Inte byggt:** Stripe, eget bokningssystem, Golfguiden, Team Swing-profiler, engelska. Se `BLOCKERS.md`.

## 2. Kravbevis

| Krav | Status | Bevis |
|---|---|---|
| STR-01 | DONE | `Config\Booking::$bookingUrl` (verifierad, svarar 200). `PagesTest::testBookingCtaPointsToVerifiedSweetspotUrl`, och FORBIDDEN-listan stoppar "Swing App". |
| STR-02 | DONE | Provider, länk, bokningsapp och instruktioner i `app/Config/Booking.php`, som används på alla sidor. Appkort för Sweetspot och TrackMan Golf på start, boka och inför besöket. |
| STR-03 | DONE | `site.css` från prototypen orörd. Påståenden om app, Stripe, 250 banor och millimeter är borttagna (testat). |
| UX-01 | DONE | "Boka tid" i sidhuvudet på mobil (`.nav-cta-m`) och desktop, "Köp spelpott" som sekundär CTA i hero. E2E: "Boka tid syns utan att öppna menyn". |
| UX-02 | DONE | Priser, Företag & grupper och Kontakt i toppmenyn. Inför besöket i sidfoten och länkat från start, boka och FAQ. Länkkontrollen visar att alla 18 sidor nås. |
| UX-03 | DONE | `partials/price_table.php`, räkneexempel märkt "exempel, 4 pers.". `PricingTest`. |
| UX-04 | DONE | Dörrkoden byggs ur `Config\Swing` på boka, inför besöket och FAQ. `PagesTest::testFactsAreConsistent`. |
| UX-05 | IN_PROGRESS | axe-core: 0 fel på alla sidor vid 320/375/768/1024/1440 px. Tangentbordstester i e2e. Manuell skärmläsartest återstår (X11). |
| UX-06 | DONE | Bara svenska publiceras. |
| CONTENT-01 | DONE | Positioneringen följer kravens texter, med bilder på både golf och lounge. |
| CONTENT-02 | DONE | After Work beskrivs som offert. Challenge och Exclusive nämns inte (testat). |
| CONTENT-03 | DONE | Presentkort digitala, 100–1 000 kr i steg om 100, ett år, inlöses i Sweetspot. Betalsätt är Swish enligt dagens liveflöde. `FormsTest`. |
| CONTENT-04 | DONE | Separata tabeller. `ContentConfigTest` och `PagesTest::testEpassiAndOrdinaryPotsAreShownSeparately`. |
| CONTENT-05 | DONE | Ingen alkoholservering utlovas (testat). Catering "efter överenskommelse". |
| CONTENT-06 | DONE | `/infor-besoket/` stämmer med regelbladet. Länk från Sweetspots bokningsbekräftelse görs i Sweetspot. |
| CONTENT-07 | DONE | `competitions`-tabell, admin, lista, detaljsida och tomt läge. `PagesTest::testCompetitionsAreFilteredAndGetEventMarkup`. |
| CONTENT-08 | DONE | Generell Team Swing-sida, inga profiler eller belopp. Profiler: X10. |
| CONTENT-09 | DONE | `Config\Images`, med bilder godkända av ägaren 2026-10-11. |
| SEO-01 | DONE | Egna serverrenderade URL:er med avslutande snedstreck, canonical och H1 (`PagesTest`). |
| SEO-02 | DONE | SEO-matrisen i `Config\Pages`. `testTitlesAndDescriptionsAreUnique`. |
| SEO-03 | IN_PROGRESS | En källa för NAP och öppettider. Jämförelse med Google-företagsprofilen återstår (X05). |
| SEO-04 | DONE | Faktarutor, FAQ i vanlig HTML, `llms.txt` med samma fakta, inget dolt innehåll. |
| SEO-05 | IN_PROGRESS | SportsActivityLocation, BreadcrumbList, Event (bara publicerade med datum) och FAQPage, inga betyg (testat). Validering i Googles Rich Results-test kräver publik URL. |
| SEO-06 | NOT_STARTED | P1, inga sakgranskade artiklar ännu (X09). |
| SEO-07 | DONE | `Config\Redirects` och `CanonicalUrl`, 301 i ett hopp (`testLegacyUrlsRedirectInOneHop`, e2e). Smoke test i produktion efter lansering. |
| TECH-01 | DONE | Allt innehåll i initial HTML. Logotyp och ikoner renderas på servern, JS bara för meny och fokus. |
| TECH-02 | IN_PROGRESS | WebP med srcset, förladdad LCP-bild, lazy loading, egna typsnitt, sidcache. Lighthouse labb (simulerad mobil, `php -S` utan gzip): prestanda 95–97, LCP 2,4–2,7 s, CLS 0, TBT 0 ms. Fältdata finns först efter lansering. |
| TECH-03 | DONE | Inga cookies på publika sidor (verifierat med curl). Sessionscookie bara i admin. Kartan är en extern länk. Integritetspolicyn beskriver detta. |
| TECH-04 | DONE | Validering, honeypot, gräns per IP, databas, mejl och tacksida. `FormsTest`. |
| TECH-05 | DONE | Admin med status, giltighet och avpublicering. Sidcachen töms vid sparande (`AdminTest`). |
| TECH-06 | BLOCKED | Ingen Stripe och ingen ofullständig kassa. Köp går via det verifierade Swish-flödet. Stripe väntar på B02. |
| QA-01 | BLOCKED | Kräver att Swing godkänner texterna (B14). Inga utkastmarkeringar syns publikt. |
| QA-02 | IN_PROGRESS | Testat lokalt och i produktionsbygget. Test i målmiljön (Loopia) efter uppladdning. |
| AGENT-01 | DONE | Inventering av prototyp, gammal WordPress-sajt och swing-order redovisad före kod. |
| AGENT-02 | DONE | Prototypens tema, ikoner, galleri och mönster återanvända. Borttaget bara det som strider mot kraven (app, Stripe-kassa, API-öppettider, engelska). |
| AGENT-03 | DONE | `BLOCKERS.md` skiljer bekräftat från väntande. Konfigurationen kommenterar bekräftade värden. |
| AGENT-04 | DONE | Inga falska boknings-, Stripe-, Epassi- eller voucherflöden. Swish-ordern går till befintliga swing-order. |
| AGENT-05 | DONE | `Orders::isOperational()` och `Booking::hasBookingUrl()`. Tester visar att tom adress ger reservväg (`ContentConfigTest`, `FormsTest::testNoOrderFormWhenChannelIsNotConfigured`). |
| AGENT-06 | DONE | Denna rapport, testresultat och skärmbilder från QA-körningen. |
| TECH-07 | DONE | `Config\Swing` med validerade prisperioder (`Pricing`), som används på alla sidor. |
| TECH-08 | DONE | `booking_url()` och `Config\Booking`. Inga hårdkodade Sweetspot-adresser i vyerna. |
| TECH-09 | DONE | `SecretsTest`: inga nycklar i kod, `.env.example` utan värden, `.env` gitignorerad. |
| TECH-10 | DONE | E2E: mobilmeny med Enter, Tab och Escape, skip-länk, FAQ med tangentbord, 320/375/768/1280 px. Synlig fokus i `swing.css`. |
| TECH-11 | IN_PROGRESS | Från formulär till databas och mejl testat (`FormsTest` med MockEmail). Riktig leverans via Loopia-SMTP verifieras vid lansering (X04). |
| TECH-12 | DONE | Separata pottlistor. Kampanjer och tävlingar har status och giltighet, inaktiva och utgångna visas inte (testat). |
| TECH-13 | DONE | Intern länk- och ankarkontroll (`testAllInternalLinksAndAnchorsResolve`), externa länkar verifierade (`external-links.spec.js`). Unik title och OG per sida. Sitemap bara med indexerbara sidor. |
| TECH-14 | DONE | Tävlingar visas bara när de är publicerade, utan datum står "Datum meddelas". Inga påhittade data. |
| TECH-15 | NOT_STARTED | P1, Stripe är inte byggt. Gränssnittet `OrderGateway` och idempotensnyckeln finns förberedda. |
| QA-03 | DONE | Enhetstester (pris, config, orderformat) och e2e för start, pris, mobilmeny, boknings-CTA, FAQ och 404. |
| QA-04 | DONE | Inga döda knappar eller platshållare (`PagesTest` FORBIDDEN), inga JS-fel i QA-körningen, inga brutna länkar. |
| QA-05 | DONE | Blockerade beslut är separerade i `BLOCKERS.md`. Inga påståenden om datum, kollektivtrafik, alkoholtillstånd, partnerloggor eller Stripe. |

## 3. Filer och designbeslut

Se `README.md` ("Var ändrar jag vad?" och "Struktur"). De viktigaste besluten:
- **Svenska URL:er med avslutande snedstreck**, samma som WordPress-sajten. 301 sköts i PHP, så reglerna gäller både lokalt och på Loopia.
- **Fakta i konfiguration** i stället för i databasen. Admin hanterar bara det som ändras ofta (tävlingar, kampanjer).
- **Beställningar via mejlformatet som swing-order redan läser.** API-kanalen är förberedd men kräver ändring i swing-order.
- **Inga tredjepartsskript och inga cookies** på publika sidor: egna typsnitt, ingen kartinbäddning, ingen statistik.
- **`partial()` för delvyer**, eftersom CI:s delade renderare annars låter variabler läcka mellan fält (regressionstest finns).

## 4. Testresultat

| Kontroll | Resultat | Notering |
|---|---|---|
| `composer lint` | OK | |
| `composer test` | OK, 109 tester, 1 820 assertions | SQLite i minnet |
| E2E (`tests/e2e`, Playwright) | 15/15 gröna | mot produktionsbygget (`bin/build-loopia.sh`) |
| Mobil 320/375/768/1024/1440 | Ingen horisontell scroll | QA-skript och e2e |
| Tillgänglighet | axe-core: 0 fel, alla sidor, fem bredder | WCAG 2.2 AA plus best practice |
| Metadata/SSR | Unik title, description, canonical, en H1, giltig JSON-LD | `PagesTest` |
| Lighthouse (labb) | Prestanda 95–97, tillgänglighet 100, bästa praxis 100 | SEO-poängen är låg bara för att `seo.indexable = false` |

## 5. Integrationer och driftsättning

- Bokningslänken till Sweetspot är verifierad (200).
- Kontaktleveransen är testad med mock. Riktig SMTP verifieras på Loopia.
- Stripe är inte live och inte byggt.
- Ingen driftsättning är gjord, det kräver ditt uttryckliga klartecken. Se `docs/drift-loopia.md`.

## 6. Blockers

Se `BLOCKERS.md` (B01–B14 och X01–X11).

## 7. Nästa säkra steg

Sätt `orders.mailTo`, SMTP och databasuppgifter i en Loopia-`.env` och gör en testuppladdning på en testdomän med `seo.indexable = false`.
