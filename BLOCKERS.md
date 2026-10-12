# Öppna beslut och blockers

Sajten publicerar bara information med status **BEKRÄFTAT**. Allt som väntar på beslut är avstängt, utelämnat eller går till en säker reservväg, det vill säga telefon och e-post (AGENT-03, AGENT-05, QA-05).

Status: `OPEN` = väntar på ägarens besked, `HANDLED` = sajten fungerar säkert utan beslutet men behöver ett besked för att bli komplett.

## Beslutsloggen (krav/ai/data/open_decisions.json)

| ID | Vad som saknas | Vad sajten gör nu | En rekommenderad nästa åtgärd |
|---|---|---|---|
| B01 | År för TrackMan-bytet | **Startår stängt 2026-10-11:** 2022 (ägarens besked, står även i logotypen). Visas på Om oss och som `foundingDate` i strukturerad data. Bytet från AboutGolf till TrackMan iO nämns fortfarande utan årtal. | Bekräfta året för TrackMan-bytet. |
| B02 | Livelänkar för köp och tidpunkt för Stripe | Bokning: verifierad Sweetspot-länk. Spelpott, presentkort och friskvård: beställning med Swish via swing-order (dagens liveflöde), men **bara om `orders.mailTo` är satt**, annars visas telefon och e-post. **Stripe Checkout är byggt men avstängt** (`payments.provider = swish`), se `docs/stripe.md`. | Ange `orders.mailTo`. För Stripe: testa i sandlåda, gå igenom checklistan i `docs/stripe.md` och bestäm hur swing-order ska hantera Stripe-betalda ordrar. |
| B03 | ~~Dubbelkontroll av Epassi 2 500~~ | **Stängt 2026-10-11:** ägaren rättade värdet till 2 500 → 2 700 kr. Visas så, och ett test larmar om beloppet ändras. | – |
| B04 | Friskvårdsregler (bonus, kvitto, personligt saldo) | **Delvis stängt 2026-10-11:** ägaren öppnade direktköp av friskvårdspott med personnummer, som ligger först på `/friskvard/`. Epassi-alternativen visas därefter. | Låt någon kontrollera att bonusvärdet på friskvårdspotter (t.ex. 1 000 → 1 150 kr) är förenligt med skattereglerna för friskvårdsbidrag. |
| B05 | Grupppaket | Swing After Work beskrivs som offertupplägg. Challenge och Exclusive nämns inte vid namn, bara som "tävling eller hela lokalen" på offert. | Godkänn eller avvisa Challenge/Exclusive och ange vad After Work innehåller. |
| B06 | Catering och alkohol | Inga löften om alkoholservering. Catering anges som "efter överenskommelse", med Nättraby Vägkrog. | Bekräfta vilka cateringalternativ som kan beställas i dag. |
| B07 | Bilder och grafisk profil | **HANDLED:** hero och galleri 01–05 godkända av ägaren 2026-10-11. Logotypen kommer från prototypen. | Bekräfta logotyp, färger och typografi som slutlig profil. |
| B08 | Kollektivtrafik och tillgänglighet | Ingen hållplats, parkering eller tillgänglighetsuppgift publiceras. Karta finns som extern länk. | Ange närmaste hållplats och lokalens tillgänglighet, så läggs det till på Kontakt och i FAQ. |
| B09 | Partner-namnformer | Nämner Trummenäs GK, Carlskrona GK, Aspero Gymnasium och Nättraby Vägkrog. **Nicklastorp GK utelämnad.** Inga logotyper. | Bekräfta officiellt namn för Nicklastorp GK och om logotyper får användas. |
| B10 | TrackMan-funktioner | Inga påståenden om antal banor eller precision. Prototypens "över 250 banor" och "millimetern" är borttagna, och ett test larmar om de kommer tillbaka. | Verifiera antal banor och spellägen i er installation. |
| B11 | Tävlingarnas innehåll och anmälningsflöde | Admin med publiceringsstatus finns. Sidan visar bara publicerade tävlingar och har ett ärligt tomt läge. Inga tävlingar är inlagda. | Bestäm vem som publicerar och lägg in aktuella tävlingar i admin. |
| B12 | Presentkortsvillkor | Nya köpvillkor: giltigt ett år, kan ges bort, 14 dagars ångerrätt för oanvänt värde. Inget om förlorad kod. | Låt villkoren granskas (se X02) och bestäm hantering av förlorad kod. |
| B13 | Engelska | Bara svenska (`lang="sv"`, ingen hreflang), enligt UX-06. | Besluta om och när engelska ska in. |
| B14 | Content ownership | Fakta i `app/Config/Swing.php`, tävlingar och kampanjer i admin. | Utse vem som godkänner texter, priser, kampanjer och bilder. |

## Ytterligare blockers från implementationen

| ID | Vad som saknas | Påverkan | Nästa åtgärd |
|---|---|---|---|
| X01 | Brevlådan som swing-order synkar | Beställningsformulären döljs tills `orders.mailTo` är satt (fail closed). | Sätt `orders.mailTo` i produktions-`.env`. |
| X02 | Juridisk granskning av `/kopvillkor/` och `/integritetspolicy/` | Texterna är nya. De gamla PDF:erna från 2023 strider mot bekräftade fakta (24 månader, ingen ångerrätt). Integritetspolicyn nämner leverantörer generellt; var swing-order och dess databas lagrar data (Vercel/Supabase, ev. utanför EU) behöver anges. | Låt någon med juridisk kompetens granska båda sidorna före lansering. |
| X03 | Loopia-miljön | Okänt om FTP-kontot kan skapa mappar bredvid `public_html` och vilken PHP-version som är vald. | Kontrollera vid första uppladdningen, se `docs/drift-loopia.md`. |
| X04 | SMTP-uppgifter hos Loopia | Förfrågningar och ordrar sparas ändå, men mejlnotiser och kundkvitton kräver SMTP. | Lägg in SMTP-lösenord i `.env` och testa en förfrågan. |
| X05 | Google-företagsprofilen | SEO-03 kräver samma NAP och öppettider som i Google. | Jämför profilen med `app/Config/Swing.php`. |
| X06 | Exakt bokningsadress | Sajten använder `book.sweetspot.io/clubs/swing-golf-lounge`, som används på nuvarande sajt och svarar 200. Gamla `/boka` pekade på `…/1265/tee-sheet`. | Bekräfta vilken av dem kunderna ska landa på. |
| X07 | Tolkning av "Vagn- och bilförbud året runt" | Återgiven ordagrant från regelbladet på Inför besöket. | Förtydliga gärna vad regeln avser. |
| X08 | Kampanjer | Gamla sajtens "Oktober-special" (2 500 → 4 000 kr) är inte inlagd, eftersom aktiva kampanjer kräver slutdatum. | Lägg in kampanjen i admin med giltighet, om den gäller. Kodlagret i swing-order behöver koder för 2 500 kr. |
| X09 | Golfguiden (SEO-06, P1) | Inte byggd. Inga tomma mallsidor publiceras. | Skriv och sakgranska första artikeln, så byggs guidedelen. |
| X10 | Team Swing-profiler (CONTENT-08, P1) | Generell sida utan spelare. | Ta fram godkända profiler med samtycke. |
| X12 | TrackMans Google Play-länk | `dk.TrackMan.Range` (ägarens länk) ger 404 för utloggade besökare från Sverige i våra kontroller. Sökningen i Google Play visar även `dk.trackman.range.mobile` ("Trackman Range Mobile"). | Öppna länken i ett privat fönster. Ger den 404, ange rätt länk i `Config\Swing::$trackmanApp`. |
| X11 | Manuell skärmläsartest | Automatisk axe-kontroll har noll fel, tangentbordstester är automatiserade, men ingen test med VoiceOver/NVDA är gjord. | Kör en genomgång med VoiceOver på mobil och desktop. |
