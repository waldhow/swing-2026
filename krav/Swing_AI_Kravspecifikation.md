# Swing Golf & Lounge — AI Build Spec

**Version 2.0 · 11 oktober 2026 · Leveransform: styrande instruktioner för AI-kodagent.**

> **Uppdrag:** Bygg/vidareutveckla Swing Golf & Lounges publika webbplats utifrån den befintliga prototypen och den medföljande innehållshandoffen. Detta är en kravspecifikation för en **AI som skriver kod**, inte en allmän visionstext. Börja inte med att skriva om allt. Inspektera befintlig kod först. **Ingen tidsplan ska skapas.**

## 0. Startinstruktion till AI-kodagenten

Du är senior frontend-/fullstackutvecklare, UX-implementatör, teknisk SEO-specialist och QA-ansvarig för Swing Golf & Lounge. Du har tillgång till webbplatsens källkodsrepo, detta dokument, befintlig designprototyp och tidigare innehållshandoff. Ditt ansvar är att implementera verifierbara webbplatssidor med hög kvalitet, inte att improvisera en ny produkt.

**Innan du kodar:**

1. Inspektera befintligt repo: ramverk, package scripts, routning, deploy, befintlig design, komponenter, innehåll, bildanvändning, API-integrationer och testuppsättning.
2. Läs `AI_BUILD_SPEC.md`, `data/requirements.json`, `data/pages.json`, `data/content_drafts.json` och `references/Swing_Golf_Lounge_Handoff_Kravspecifikation.html`. Läs också regelbladet. Om något i underlaget motsäger ägarens senare bekräftade svar gäller ägarens senast bekräftade uppgift; oklarheter är **blockers**, inte en licens att gissa.
3. Rapportera *nuläge, planerade ändringar, beroenden, blockerade beslut* i ett kort meddelande. Fortsätt därefter med den första säkra, fungerande implementationen.
4. Implementera minsta sammanhängande leverans med P0-krav först; återanvänd komponenter och befintlig styling. Koda i små verifierbara steg, testa efter varje steg.
5. Ge slutrapport med krav-ID, vilka filer som ändrades, kommandon/testresultat, skärmbilder om möjligt och kvarstående blockerade beslut. Rapportera aldrig ofullständiga funktioner som färdiga.

**AI:n ska inte:** hitta på priser/erbjudanden, anlita eller skapa leverantörstjänster, lansera en ny bokningsplattform av sig själv, konfigurera livebetalning utan nycklar och godkännande, eller publicera referensbilder som saknar bildgodkännande.

---

## 1. Mål och produktgräns

**Huvudmål:** Få fler att boka golftid. **Sekundärt mål:** Öka köp av spelpotter. Andra mål: förfrågningar om företagsevent, presentkort, friskvård och tydlig information inför besöket.

**Positionering:** Social golf, vänner och gemenskap; ett klassiskt skotskt klubbrum/pubkänsla med modern TrackMan iO-teknik. Varmt, kunnigt, välkomnande. Vi skriver **”vi på Swing”**. Undvik falska påståenden som ”bemannad bar”, ”öppen alkoholservering”, ”personlig instruktör alltid på plats” eller ”eget bokningssystem live”.

**Ändra inte designriktningen:** Utgångspunkt är prototypen `https://swing-public-staging.up.railway.app/` (referens, inte verifierad teknisk källa). Prioritera att återskapa dess känsla och återanvända kod där repo finns. Bilderna i paketet är endast referenser, **inte godkända produktionsbilder**. Bygg inga nya funktioner enbart för att de syns i prototypen.

### Funktionella gränser

| Område | NU / bekräftat | SENARE / ej aktivt | Krav på AI |
|---|---|---|---|
| Bokning | **Sweetspot** används under 2026 | Eget bokningssystem planerat 2027 | Gör bokningsprovider konfigurerbar. Hitta inte på intern boknings-API. |
| Simulering | Fyra **TrackMan iO** | Gamla aG Locker är föråldrat | Använd TrackMan Golf och korrekta instruktioner. |
| Spelpotter | Finns och används via Sweetspot voucher | Stripe-kassa på egna sajten planerad | Äkta fungerande köp-/bokningslänkar eller tydligt ej aktivt. |
| Presentkort | Digitala och löses in via Sweetspot | Stripe-upplägg föreslaget | Säkerställ riktig köpväg; bygg ingen låtsascheckout. |
| Företagsevent | Offert och catering kan ordnas | Alkoholserveringstillstånd ej färdigt | Visa inte serveringstillstånd som beviljat. |
| Tävlingar | Öppna och privata tävlingar | CMS/event-API ännu obekräftat | Implementera endast verifierade, publicerade event. |
| Team Swing | Team finns, nytt team presenteras senare | Medlemsprofiler saknar godkänt material | Publicera inte påhittade spelare eller deras avtal. |

---

## 2. Source of truth och sanningsnivåer

Källprioritet för verksamhetsuppgifter:

1. Senaste uttryckliga, bekräftade besked från verksamhetsägaren i detta projekt.
2. `data/site-config.EXAMPLE.json` **endast som initial strukturell sammanställning** – kontrollera operativa länkar, särskilt `null`.
3. `references/Regler & information - org.docx` för instruktioner; den gamla sidan om aG Locker är **föråldrad**.
4. `references/Swing_Golf_Lounge_Handoff_Kravspecifikation.html` och `data/content_drafts.json` för copy, URL-översikt och krav. Det är **utkast**, inte automatiskt godkänt innehåll.
5. Designprototypen och referensbilderna för visuell riktning, **inte** för verksamhetsfakta.

Markera oklarheter i `BLOCKERS.md` eller slutrapport. Använd följande status internt: `CONFIRMED`, `DRAFT_REQUIRES_APPROVAL`, `PLANNED_NOT_LIVE`, `UNKNOWN_BLOCKING`. På offentlig sajt syns enbart bekräftad information.

### Låsta fakta att återanvända

- **Företag:** Swing Golf & Lounge. **Adress:** Idrottsvägen 2B, Nättraby, Karlskrona, Blekinge. **Telefon:** 0455-32 34 01. **Mejl:** info@swinggolf.se. **Öppet:** alla dagar 06.00–00.00, normalt obemannat.
- **Anläggning:** cirka 250 m². Fyra TrackMan iO-simulatorer; simulator 1 & 3 slagdukar cirka 5,5 × 3 m, simulator 2 & 4 cirka 4,5 × 3 m. Rekommenderat 3–4 spelare/simulator, **ingen fast angiven maxgräns per simulator**. Evenemang cirka 16–20 golfare och upp till 25 totalt i lokalen.
- **Miljö:** mörk, mysig skotsk klubbhus-/pubkänsla, skinnsoffor och fåtöljer, loungemöbler, bardisk, kaffe, dryck och snacks, TV. Självservering. Mat kan beställas från Nättraby Vägkrog.
- **Tillträde:** bokning ger fyrsiffrig dörrkod, anges med avslutande `#`. Koden fungerar 20 min före och 20 min efter bokningens **start** (inte slut). Kostnadsfri avbokning till 3 timmar före start. Bokande person måste närvara under hela hyrtiden.
- **Hygien/skydd:** egna klubbor ska vara rena; rena inomhusskor; endast Swings bollar och peggar; undvik att luta bagar/klubbor mot tygväggar. Enstaka klubbor finns att låna; helt set kan ordnas vid förfrågan i förväg.
- **Golfpris:** pris **per simulator och timme, ej per person**. Alla dagar 06–08 150 kr. Mån–fre 08–15 250 kr. Mån–fre 15–00 280 kr. Lör–sön 08–00 280 kr. Vid 4 personer blir det 37,50 / 62,50 / 70 kr var beroende på timpriset.
- **Vanliga spelpotter** (1 års giltighet, kopplade till Sweetspotprofil via mejl, personliga men överlåtbara): betala 1 000→få 1 150; 2 000→2 400; 3 000→3 750 kr. I kassan välj `+ Använd voucher`.
- **Epassi/friskvård** (personligt; separata produkter): betala 500→500, 1 000→1 000, 2 000→2 150, **2 500→2 200**, 3 000→3 350 kr. Dessa belopp är **uttryckligen bekräftade av ägaren**, inklusive det ovanliga 2 500→2 200. Ändra INTE tyst. Regler för Epassi-bonus och skattemässig friskvårdshantering behöver granskas innan köpvillkor publiceras.
- **Presentkort:** endast digitalt, 100–1 000 kr i steg om 100 kr; tillfällen: Fars dag, Mors dag, födelsedag, generellt; giltiga ett år. Sweetspot > Mer (…) > Vouchers > + > skriv presentkortsnummer; i kassan `+ Använd voucher`.
- **Företag:** offert efter förfrågan, möjlig personal vid event, catering ordnas. Ägaren gillade **Swing After Work** som pakettanke; de andra namnen Swing Challenge/Exclusive kräver godkännande. Varken offentligt pris eller garanterad alkoholförsäljning är klar.
- **Aktiviteter:** öppna och stängda/privata tävlingar. Verksamheten har uppgett veckotävlingar, ligor, tourer, närmast hål och längsta drive, men **inga datum** får hittas på.
- **Partners uppgivna:** Trummenäs GK, Nicklas Torp GK (namnform kräver kontroll), Carlskrona GK, Aspero Gymnasium, Nättraby Vägkrog. Publicera inte partnerlogotyper utan godkännande.
- **Historik:** Mats och Pontus skapade Swing utifrån golfintresset. Startår uppgavs 2002 (möjligen skrivfel), byte från AboutGolf till TrackMan omkring 2023/2024; publicera inga år innan ägaren bekräftar.

---

## 3. Sidstruktur, URL:er och innehåll

**Krav:** separata indexerbara sidor för sökintentioner; startsidan ska inte ersätta alla undersidor. Återanvänd exakt grundstruktur i `data/pages.json` och utkast i `data/content_drafts.json`. Varje page har unik title, meta description, en synlig H1 och brödtext i serverrenderad/prerenderad HTML.

| Sida | URL | Vikt / viktigast |
|---|---|---|
| Startsida | `/` | P0 · Boka tid / Köp spelpott |
| Inomhusgolf | `/inomhusgolf/` | P0 · Boka simulator |
| Priser | `/priser/` | P0 · Boka tid / Se spelpotter |
| Boka golftid | `/boka/` | P0 · Öppna Sweetspot-bokningen |
| TrackMan iO & simulatorer | `/trackman-io/` | P0 · Boka en TrackMan-simulator |
| Företagsevent | `/foretagsevent/` | P0 · Begär offert |
| Gruppbokning | `/gruppbokning/` | P1 · Fråga om gruppbokning |
| Spelpotter | `/spelpotter/` | P0 · Köp spelpott |
| Friskvård & Epassi | `/friskvard/` | P0 · Öppna Swing hos Epassi |
| Presentkort | `/presentkort/` | P0 · Köp presentkort |
| Tävlingar & aktiviteter | `/tavlingar/` | P0 · Se aktuella tävlingar / Fråga om egen tävling |
| Team Swing | `/team-swing/` | P1 · Se Team Swing |
| Om oss | `/om-oss/` | P0 · Boka tid / Kontakta oss |
| Kontakt & hitta hit | `/kontakt/` | P0 · Ring / E-posta / Hitta hit |
| Vanliga frågor | `/vanliga-fragor/` | P0 · Boka tid / Kontakta oss |
| Inför ditt besök | `/infor-besoket/` | P0 · Boka simulator / Ring vid problem |

**Utöver ovan:** Golfguiden planeras under `/golfguiden/`, men långa artiklar är inte färdigskrivna. Skapa bara faktagranskade, kompletta guider – inga tunnformat-mallar eller massgenererade ortssidor. Föreslagna guideämnen och URL:er finns i `data/pages.json`.

**Navigation:** Enkel och tydlig, med Boka tid synlig på mobil. Rekommenderad toppnavigation: Anläggningen, Priser, Spelpotter, Företag & grupper, Tävlingar, Friskvård, Kontakt, **Boka tid**. Footer med Kontakt, Inför ditt besök, Vanliga frågor, villkor och integritetspolicy (lagtexter endast om verifierade). Presentkort och Team Swing ska vara lätta att hitta via relevanta sektioner/footer. Undvik för många toppnivålänkar på mobil.

### Upplevelse – startsida

1. Hero med golf/lounge-känsla, geografisk kontext, stark tydlig H1.
2. **Primär CTA:** Boka golftid → verifierad Sweetspot-väg. **Sekundär CTA:** Köp spelpott → verifierad köpväg; om ej verifierad, visa saklig sida för spelpotter utan falsk checkout.
3. Fyra simulatorer/TrackMan iO + öppettider.
4. Miljö/loungen + social gemenskap.
5. Prissnabbvy per simulator och timme, länk till full prislista.
6. Tre vanliga spelpotter (aldrig Epassi inblandat).
7. Företagsevent med offert-CTA.
8. Verifierade tävlingar/aktivitet utan fiktiva datum.
9. Praktisk FAQ och Nättraby-adress.

### Återanvändbara UI-komponenter (så långt befintlig arkitektur tillåter)

`SiteHeader`, `MobileNav`, `SiteFooter`, `BookingCTA`, `SectionHeading`, `Hero`, `SimulatorFeatures`, `PricingTable`, `CreditPotCard`, `GiftCardCard`, `EventCard`, `FAQAccordion`, `Breadcrumbs`, `ContactDetails`, `EventInquiryForm` (endast när backend är verifierad), `SEOHead`/motsvarande. Data ska vara separerad från komponenter. Behåll inte dessa namn om befintliga namngivningskonventioner skiljer sig.

---

## 4. Funktionell byggspecifikation

### 4.1 Bokning: Sweetspot nu, nytt senare

- Alla CTA:er går via en central config/adapter, exempelvis `booking.openBooking()` eller en resolver av `bookingUrl`. AI:n måste **be om eller verifiera exakt fungerande Sweetspot-URL** innan aktiv bokningsknapp pekar externt.
- Beskriv nuvarande flöde: Sweetspot-app/bokning, betalning där, fyrsiffrig dörrkod och regler. Ingen inloggning på nya Swing-sajten behövs för informationssidorna.
- Gör bytespunkter för provider, instruktionscopy och länkar. **Implementera inte eget bokningssystem**, kalendrar, betalningar eller API-integration på spekulation.
- Ingen hårdkodad automatiskt framtida lansering. Ägare aktiverar framtida system efter verifiering.

### 4.2 Spelpotter, friskvård och presentkort

- Priserna ligger i typad datastruktur. Visa två separata tabeller med ordinarie respektive Epassi-värden. Prisexempel måste tydligt ange personpris som exempel, inte biljettpris.
- Endast köpknapp om det finns produktionsverifierad URL/checkout och leveransflöde. Använd ej `#`, `javascript:void(0)`, test-Stripe eller mockad voucher som publik köp-CTA.
- Epassi-länk för information: `https://services.epassi.se/ui/merchant-search/site/183651` (måste fortfarande verifieras i browser). Ingen falsk integrerad Epassi checkout.
- Digitala presentkort med temaval och 100–1 000 kr i 100-kronorssteg **om köpflödet är godkänt och aktivt**. Annars presentera information och verifierad existerande köplänk.
- Framtida Stripe kräver serverbaserad order, webhookvalidering, idempotens, e-postleverans, personuppgiftsskydd, hantering av fel/retur och **formellt API-/produktgodkännande**. Detta ligger utanför den nuvarande P0-leveransen.

### 4.3 Företags- och gruppförfrågan

- Tillåt offertförfrågan med kontaktuppgifter, föreslagen dag/tid, antal personer, paketintresse, önskemål om mat och meddelande. Lägg inte in fiktiva priser.
- Om mejl-/formbackend saknas får AI:n använda synlig verifierad e-postkontakt `info@swinggolf.se` i stället för dött formulär. För formulär: tillgänglig felvalidering, spam-skydd, bekräftelse på verklig leverans och rätt dataskydd.
- Swing After Work får lyftas som skräddarsytt offertupplägg; andra paketalternativ som förslag, inte fastställda produkter.

### 4.4 Tävlingar / Team Swing / kampanjer

- Datamodell för tävling: titel, id/slug, `public|private`, beskrivning, status, datum (endast känt), registreringslänk (endast verifierad), resultatlänk, publiceringsflagga. Filtrera bort ej godkända, avbokade eller utgångna från ”aktuella”.
- Kan visa en generell tävlingslandningssida utan aktuella event; ingen falsk kalender.
- Kampanjer: titel, villkor, giltighetsperiod, publicerad/avpublicerad. Ingen ständigt aktiv ”Oktober-special” som om erbjudandet gäller året runt.
- Team Swing: generell sida tills spelarprofiler/bilder och godkännanden finns.

### 4.5 Kontakt, öppettider, hitta hit

- Telefonlänk `tel:+46455323401` och e-postlänk `mailto:info@swinggolf.se` ska fungera.
- Visa korrekt lokal adress och öppettider. Visa inte specificerad busslinje/hållplats, barriärfrihet eller parkeringsantal utan verifiering.
- Karta kan vara extern kartlänk; undvik att tvinga in spårande kartinbäddning utan integritetshantering.

---

## 5. Design och användbarhet

- **Designkälla:** befintlig kod + visuella förlagan. Klubbhus, skinn, trä, värme, mörkgrönt/kol och dämpade accentfärger. Använd original logotyp och produktionsgodkända bilder först när de finns. Referensbilder får **inte publiceras per automatik**.
- Mobil först; minst 320, 375, 768, 1024, 1440 px. Typografi och CTA tydliga; stabila kort och prissammanställningar; inga horisontella sid-scrolls.
- Tangentbord: navigering, nav-stängning med Esc, fokusindikatorer, tydliga formulärlabel, konsekvent rubrikhierarki, kontrast, alt-texter, respekt för `prefers-reduced-motion`.
- Inga jättelånga bildtunga sidor som döljer den egentliga köp-/bokningsvägen. Optimera bilder (responsive srcset, modern format, lazy load under första vy, hero prioriteras).
- Ingen märklig turistkopiering/keyword stuffing; naturlig lokal SEO och diskret länkstruktur.

---

## 6. SEO och AI-sök: tekniska acceptanskriterier

- Serverrenderat/prerenderat huvudinnehåll. Ingen viktig text endast efter klienthydrering.
- En H1/sida, unik `<title>`, meta description och canonical. Open Graph-bild/text där bildgodkännande finns. Svenska `lang="sv"`.
- `robots.txt` och `sitemap.xml` med faktiska indexerbara URL:er, inga preview-/draft-sidor. Gamla relevanta URL:er omdirigeras med 301 utifrån URL-inventering.
- `LocalBusiness` / `SportsActivityLocation` enligt faktiskt innehåll; endast verifierade attribut (adress, telefon, openingHours). `BreadcrumbList` på undersidor, `Event` **endast för verkliga publicerade event**. Aldrig fejkrating, fejkrecensioner eller påhittade erbjudanden.
- Beskriv sakers betydelse direkt i HTML, ex. ”Vad kostar det?”, ”Hur får man kod?”, ”Hur använder man presentkort?”. FAQ-markup endast där det är lämpligt och följer gällande sökmotorriktlinjer; ingen garanti om rich results.
- Lokal användbarhet: Nättraby, Karlskrona och Blekinge när naturligt; inte artificiella likvärdiga sidor per ort.
- Mål på mobil i fält där mätning finns: LCP ≤ 2,5 s, INP ≤ 200 ms, CLS ≤ 0,1 vid p75. Redovisa faktiskt mätverktyg/miljö; fejka inga Lighthouse-poäng.

---

## 7. Teknik, säkerhet och dataskydd

**Ramverk:** utgå från befintligt repo. AI:n får inte obemärkt byta Vue/Nuxt/React/Next, deploymentsätt eller hosting. Om repo saknas eller tekniken är olämplig: presentera kort val av arkitektur med motivering och be om godkännande före irreversibla beslut.

- TypeScript om befintligt projekt stödjer det; central runtime-/schema-validering för offentlig config.
- Separera environment config och hemligheter. Skapa `.env.example`, aldrig verkliga nycklar i källkod eller rapport.
- Alla externa integrationer behandlas som beroenden som måste verifieras och kunna fallera snyggt.
- Inga klient-anrop med hemliga betaltoken. Stripe-checkout kräver server och webhook-verifiering.
- Skydda formulär mot injektion/spam och logga inte personuppgifter i klientkonsol i produktion. Samla bara nödvändiga uppgifter, sätt lagrings-/åtkomstkrav innan formulär driftsätts.
- Statistik/cookies och inbäddningar får inte aktiveras utan motsvarande integritetsgenomgång/samtyckeshantering där så krävs.
- Fel- och tomtillstånd ska vara användbara. Inga 500-sidor för en trasig extern katalog.

---

## 8. Kravmatris och verifiering

**P0** måste vara korrekt innan den berörda funktionen kan markeras färdig. **P1** är viktig utbyggnad som får stå kvar som ej implementerad, men inte utges som klar. `data/requirements.json` är maskinläsbart register med **alla ursprungliga krav plus AI-specifika krav** och individuella acceptanskriterier. AI:n ska använda ID:n vid commits, checklistor och leveransrapport.

### P0: separat bevis på kvalitet

| Kontroll | Godkänt när |
|---|---|
| Build | `build` fungerar utan compile-/typfel. |
| Startsida och 16 sidor | Alla överenskomna P0-sidor laddar direkt som URL, inte bara via SPA-navigation. P1-status redovisas. |
| Mobil | Meny, CTA, tabeller och formulär fungerar i 320/375/768 px. |
| Navigering | Internt länkade sidor fungerar, favicon/metadata korrekt där tillgångar finns. |
| Bokning | Aktiv CTA har verifierad rätt Sweetspot-URL och förklarar faktiskt flöde. Saknas URL: blockera aktiv CTA, redovisa hinder. |
| Pris | Dygnsintervall och helger/vardagar korrekta; pris **per simulator**; räkneexempel testade. |
| Spelpotter | Ordinarie/Epassi separerade; det ovanliga Epassi-beloppet ändras inte tyst. |
| Hjälp | Kod inklusive `#`, 20 min före/efter start, avbokning 3 h, rena klubbor och skor korrekt. |
| SEO | Sidunik metadata, SSR/prerender, canonical, indexability, sitemap, robots. |
| WCAG | Manuell tangentbordskontroll plus automatiserad a11y-kontroll utan allvarliga fel. |
| Trust | Inga ogrundade startår, licenser, recensioner, tekniska garantier, levande Stripe-länkar. |
| Säkra externa flöden | Bruten betal-/offertväg visas inte som fungerande. |

### Minimikrav på testautomatik

- Enhetstest av tid/prisregler (dygnsgräns, vardag/helg), pris/person-exempel, spelpottslistor och config/feature flags.
- E2E för navigation till priser, information om kod, FAQ, offertkontakt, CTA mot rätt extern domain (utan att skicka riktig bokning), mobilmeny och 404.
- Länkgranskning och test av SEO metadata via HTML-rendering; kontrollera att inga falska placeholders `TODO`, `lorem`, `example.com`, `href="#"` nås som publik CTA.
- Vid lansering: smoke test på produktionsdomän inklusive redirect och `/sitemap.xml`. Detta kräver deployåtkomst; rapportera om saknas.
- Visuell QA med skärmbilder på mobil och desktop. Jämför mot designprototypen och notera avsiktliga avvikelser.

---

## 9. Arbetsordning för AI:n (utan tidplan)

**Steg A – Inventera:** Läs repo, URL:er, befintligt innehåll och bildtillgångar. Lista blockers. Gör ingen destruktiv refaktorering.

**Steg B – Grund:** Förankra layout, design tokens, tillgänglig navigation, sidroutning, metadata, config och verifierad Sweetspot-CTA.

**Steg C – Kärninnehåll:** Startsida, Inomhusgolf, Priser, Boka, Simulatorer, Spelpotter, Företagsevent, Kontakt, Inför besöket, Vanliga frågor. Samma sanning på varje sida.

**Steg D – Fler sidor:** Gruppbokning, Friskvård, Presentkort, Tävlingar, Team Swing, Om oss, och guider först när artiklar är sakgranskade. Inga påhittade event eller medlemsprofiler.

**Steg E – Integrationer (endast efter klartecken):** Offertform, publicering av event, Stripe-försäljning, eget bokningssystem. Avgränsa och använd riktiga kontrakt, aldrig pseudolösningar som driftsätts.

**Steg F – QA/handoff:** Automatiska tester, mobil/desktopgranskning, SEO-audit, a11y, juridiskt relevanta blockers, driftanvisning och slutrapport.

---

## 10. Leveranskontrakt – vad AI:n ska lämna tillbaka

Efter varje slutförd kodomgång lämnas:

1. **Sammanfattning:** vad som faktiskt byggts och vad som medvetet inte byggts.
2. **Kravstatus:** tabell `KRAV-ID | DONE/IN_PROGRESS/BLOCKED/NOT_STARTED | bevis/länk`.
3. **Ändrade filer och designbeslut:** komponenter, sidor, config, eventuella dependencies.
4. **Testbevis:** kommandon som körts, resultat, skärmbilder, mobil/a11y och SEO-kontroller.
5. **Miljö och driftsättning:** byggkommando, nödvändiga miljövariabler **utan hemligheter**, konfigurering av bokningslänk, deployspecifika begränsningar.
6. **Beslutslogg:** öppna frågor från `data/open_decisions.json`, påverkan och en enda rekommenderad nästa åtgärd per blocker.

**Done-definition:** Korrekt fakta + fungerande länk/integration + tillgänglig upplevelse + test + dokumenterad status. En sida med tomma knappar, placeholderfoton eller felaktig bokningsprovider är **inte klar**.

---

## 11. Öppna beslut – AI:n får inte gissa

- **B01 – Startår & TrackMan-byte:** Du angav 2002 som startår och 2023/2024 för TrackMan. Bekräfta exakt startår och år för teknikbytet. **Konsekvens:** Blockerar historikavsnittet på Om oss.
- **B02 – Köplänkar och betalstatus:** Vilka URL:er är live för Sweetspot, spelpott och presentkort? När ska Stripe aktiveras? **Konsekvens:** Blockerar exakta köpknappar och kassinstruktioner.
- **B03 – Epassi 2500:** Bekräftat: betala 2 500 kr → få 2 200 kr. Dubbelkontrollera av affärsskäl innan publicering. **Konsekvens:** Måste visas exakt eller rättas uttryckligen.
- **B04 – Friskvårdsregler:** Bekräfta hantering av Epassi-bonus, personligt saldo, kvitto och giltighet mot gällande regler och Epassivillkor. **Konsekvens:** Blockerar exakta köpvillkor, inte grundinformationen.
- **B05 – Grupppaket:** Swing After Work omtyckt som koncept. Godkänn eller avvisa Challenge/Exclusive och ange vad som faktiskt ingår. **Konsekvens:** Blockerar paketmarknadsföring utöver offertförfrågan.
- **B06 – Catering & alkoholförsäljning:** Vilka cateringtjänster kan beställas idag? Serveringstillstånd planeras men är inte klart. **Konsekvens:** Får inte lova tillståndspliktig servering innan den finns.
- **B07 – Fotomaterial & grafisk profil:** De uppladdade fotona är referens. Vilka bilder får användas skarpt? Fastställ logotyp, färger och typografi. **Konsekvens:** Blockerar slutligt bildval och visuellt godkännande.
- **B08 – Kollektivtrafik / tillgänglighet:** Bekräfta bästa hållplats, gångavstånd och eventuella tillgänglighetsförhållanden i lokalen. **Konsekvens:** Får inte publicera osäkra påståenden om tillgänglighet.
- **B09 – Partners och namnformer:** Kontrollera officiellt namn på Nicklastorp GK och vilka samarbeten som får användas med namn/logotyp. **Konsekvens:** Påverkar Om oss och eventuella loggor.
- **B10 – TrackMan-funktioner:** Verifiera antal virtuella banor och exakt tillgängliga spellägen, träningsdata och appfunktioner. **Konsekvens:** Undvik absoluta påståenden om millimeterprecision eller 250+ banor tills kontrollerade.
- **B11 – Tävlingarnas innehåll:** Vem publicerar öppna och privata tävlingar? Vilken info måste finnas? Vilket anmälningsflöde gäller? **Konsekvens:** Blockerar dynamiska eventlistan, inte grundtext.
- **B12 – Presentkort och villkor:** Kontrollera eventuell överlåtelse, hantering av förlorad kod, outnyttjat saldo och köp-/återbetalningsvillkor. **Konsekvens:** Påverkar köpvillkor och FAQ.
- **B13 – Engelska:** Prototypen har EN-alternativ. Ska engelska ingå vid leverans eller senare? **Konsekvens:** Avgör navigation, översättning och hreflang.
- **B14 – Content ownership:** Vem godkänner texter, priser, kampanjer, eventinnehåll och bilder innan de publiceras? **Konsekvens:** Behövs för långsiktigt korrekt webbplats.


Detta innebär inte att hela webbplatsen måste vänta: bygg de delar som baseras på bekräftad fakta, använd säkra fallback-vägar och rapportera bara de beslut som faktiskt blockerar aktuell funktion.

---

## 12. Filer som ingår i detta paket

- `AI_BUILD_SPEC.md` – detta styrdokument för AI-kodagenten.
- `START_HERE_AI_PROMPT.md` – färdig prompt att klistra in i Codex, Claude Code eller annan kodagent.
- `data/requirements.json` – maskinläsbar kravmatris med ID och acceptanskriterier.
- `data/pages.json` – sidöversikt och planerade guider.
- `data/content_drafts.json` – publiceringsutkast, metadata och CTA för samtliga 16 sidor.
- `data/open_decisions.json` – olösta frågor, ingen ska gissas bort.
- `data/site-config.EXAMPLE.json` – datamodell och faktavärden för teknisk inspiration, inte en aktiv integrationsfil.
- `references/Swing_Golf_Lounge_Handoff_Kravspecifikation.html` – tidigare, visuellt interaktivt helhetsunderlag.
- `references/Regler & information - org.docx` – historiskt regelblad, gammal aG Locker-information ska bort.
- `references/images_REFERENCE_ONLY/*` – foton och ritningar som referens, inte godkända för publik publicering.

**Viktigt:** Detta paket ger krav och underlag, inte åtkomst till repo, driftsmiljö, Sweetspot-integrations-URL:er eller Stripe-hemligheter. AI:n ska hantera det som blockers, inte gissa eller påstå att integrationerna är klara.
