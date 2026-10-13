# Driftsättning på Loopia

Loopias webbhotell kör nginx framför Apache med PHP och MariaDB. Kontot har SSH (nyckelinloggning), rsync och Composer. Allt byggs lokalt (`composer build:loopia`) och driftsätts med `bin/deploy-loopia.sh` via SSH/rsync.

**Miljöer**

| Miljö | Adress | Mappar på servern | Env-fil lokalt |
|---|---|---|---|
| Test | https://beta.swinggolf.se | `~/beta.swinggolf.se/public_html`, `~/beta.swinggolf.se/swing-app` | `.env.production` (just nu riktad mot beta) |
| Live | https://swinggolf.se | `~/swinggolf.se/public_html`, `~/swinggolf.se/swing-app` | samma fil med live-adressen |

Env-filerna och `.loopia` ignoreras av git och följer aldrig med i bygget.

> Kontot hos Loopia har ett 20-tal andra domäner. Skriptet skriver bara i `~/beta.swinggolf.se` eller `~/swinggolf.se`, och kräver `CONFIRM_PROD=yes` för live-sajten.

**Driftsätt**

```bash
composer test && composer build:loopia
LOOPIA_SSH=<användare>@ssh.loopia.se bin/deploy-loopia.sh beta.swinggolf.se
# första gången, eller när env ändrats:
LOOPIA_SSH=<användare>@ssh.loopia.se UPLOAD_ENV=.env.production bin/deploy-loopia.sh beta.swinggolf.se
```

Skriptet laddar upp `public_html/` och `swing-app/` (kod, `vendor/`), skapar `writable/` utan att skriva över loggar eller cache, laddar upp env-filen som `swing-app/.env` med `chmod 600` när `UPLOAD_ENV` är satt, och tömmer sidcachen. Migreringar: `ssh … 'cd ~/<domän>/swing-app && php spark migrate --all'` eller `/admin/setup/?token=…`.

**Loopia-särskilt (verifierat på beta 2026-10-13)**
- `AddOutputFilterByType` i `.htaccess` ger 500 på alla sidor. Komprimering (brotli) sköts av nginx.
- Apache ser inte HTTPS själv (nginx avslutar TLS). Villkor på HTTPS i `.htaccess` måste även titta på `req('X-Forwarded-Proto')`.
- Webben kör PHP 8.3, SSH-kommandoraden PHP 8.5.

## Förutsättningar i Loopia Kundzon

1. **PHP-version:** 8.2 eller senare för domänen, med `intl`, `mbstring` och `mysqli`, som brukar vara aktiverade som standard.
2. **SSL:** aktivera Let's Encrypt för `swinggolf.se` och `www.swinggolf.se`. `.htaccess` skickar all trafik till `https://swinggolf.se`.
3. **Databas:** skapa en MariaDB-databas och en användare. Notera värdnamnet, till exempel `mysqlXXX.loopia.se`.
   - **Användarvärd:** *Unix/SSH hos Loopia*. Sajten körs på Loopias webbservrar och ansluter därifrån. Vill du själv använda phpMyAdmin, skapa en separat användare för det.
   - **Rättigheter:** *Läs*, *Skriv*, *Radera*, *Skapa*, *Ändra* och *Indexera*.
     - *Läs*, *Skriv* och *Radera* behövs för sajten och för gallringen av gamla förfrågningar och ordrar.
     - *Skapa*, *Ändra* och *Indexera* behövs för migreringarna som `/admin/setup/` kör, både vid första installationen och vid uppdateringar. Stripe-grenen lägger till kolumner och ett index.
     - Övriga rättigheter behövs inte. Det gäller *Ta bort*, *Referenser*, *Låsa tabell*, *Skapa temporär tabell*, vyer, lagrade procedurer och *Trigger*. Sessioner och cache ligger i `writable/`, inte i databasen.
     - Vill du hårdna: ta bort *Skapa*, *Ändra* och *Indexera* efter installationen, och lägg tillbaka dem tillfälligt när en uppdatering innehåller nya migreringar.
4. **E-post:** brevlådan `info@swinggolf.se`, som även används som SMTP-avsändare. Kontrollera vilken brevlåda swing-order synkar för ordrar (`NUXT_MAILBOX_USER` i swing-order). Den adressen anges som `orders.mailTo`.

## Bygg

```bash
composer test
composer build:loopia          # skapar dist/loopia/
```

Resultat:

```
dist/loopia/public_html/   → domänens webbrot, t.ex. /swinggolf.se/public_html/
dist/loopia/swing-app/     → bredvid webbroten, t.ex. /swinggolf.se/swing-app/
```

`public_html/index.php` pekar på `../swing-app/`. Kod, `.env`, loggar och cache ligger alltså utanför webbroten.

> **Kontrollera mappstrukturen första gången.** Det här förutsätter att FTP-kontot kan skapa mappar bredvid `public_html`. Går det inte kan `swing-app/` läggas inuti webbroten. Den innehåller en `.htaccess` som nekar all åtkomst, och appens egna mappar har likadana. Bygg då med `APPDIR=swing-app` och ändra raden `require FCPATH . '../swing-app/…'` i `index.php` till `__DIR__ . '/swing-app/…'`.

## Första uppladdningen

1. Ladda upp innehållet i `public_html/` till webbroten. Ta först bort WordPress-filerna eller flytta undan dem, och spara en säkerhetskopia.
2. Ladda upp `swing-app/` bredvid webbroten.
3. Skapa `swing-app/.env` utifrån `swing-app/.env.example`:
   - `CI_ENVIRONMENT = production`, `app.baseURL = 'https://swinggolf.se/'`
   - databas, `encryption.key` (`php spark key:generate --show` lokalt)
   - SMTP-uppgifter, `orders.mailTo`
   - `admin.setupToken` (minst 24 tecken)
   - `seo.indexable = false` tills allt är kontrollerat
4. Se till att `swing-app/writable/` och dess undermappar är skrivbara för webbservern (755 räcker oftast hos Loopia, annars 775).
5. Öppna `https://swinggolf.se/admin/setup/?token=<din token>`. Sidan kör databasmigrationerna och låter dig skapa den första admin-användaren. Sidan kan köras igen efter uppdateringar för att köra nya migrationer.
6. Logga in på `/admin/` och lägg in aktuella tävlingar och kampanjer.
7. Gå igenom checklistan nedan och sätt därefter `seo.indexable = true`.

## Uppdateringar

1. `composer test && composer build:loopia`
2. Ladda upp `public_html/` och `swing-app/app/` och `swing-app/vendor/` (skriv över). Rör inte `.env` eller `writable/`.
3. Kör `/admin/setup/?token=…` om det finns nya migrationer.
4. Töm sidcachen genom att radera innehållet i `swing-app/writable/cache/` utom `index.html`. Admin tömmer cachen automatiskt när tävlingar och kampanjer sparas.

## Lanseringschecklista

- [ ] `https://swinggolf.se/` laddar och `http://` samt `www.` omdirigeras till `https://swinggolf.se/`.
- [ ] Gamla URL:er ger 301 till rätt sida: `/priser-medlemskap/`, `/vara-simulatorer/`, `/veckogolf/`, `/juniortouren-2026/`, `/epassi-swing-golf-lounge/`, `/trackman-io-gjord-for-inomhusgolf/`, `/bekraftelse-kop/`, `/foretag`, köpvillkors- och integritets-PDF:erna.
- [ ] `/sitemap.xml` listar sidorna med `https://swinggolf.se/`.
- [ ] `/robots.txt` innehåller `Sitemap:` först när `seo.indexable = true`.
- [ ] Boka-knappen öppnar Sweetspot.
- [ ] Testa en riktig förfrågan via `/kontakt/`. Den ska komma fram till `inquiries.notifyTo` och synas i admin.
- [ ] Testa en riktig beställning via `/spelpotter/`. Ordern ska dyka upp i swing-order efter synk, och kunden ska få Swish-instruktioner via e-post.
- [ ] Skicka in sitemapen i Google Search Console och uppdatera Google-företagsprofilen om något ändrats.
- [ ] Kontrollera Core Web Vitals i PageSpeed Insights och Search Console när fältdata finns.

## Felsökning

- **Vit sida eller 500:** kontrollera `swing-app/writable/logs/`. Vanligast är fel databasuppgifter eller att `writable/` inte är skrivbar.
- **Mejl kommer inte fram:** SMTP-användaren måste vara hela e-postadressen, och `email.fromEmail` måste vara samma brevlåda. Förfrågningar och ordrar sparas ändå och syns i admin, och ordrar kan skickas igen därifrån.
- **Ändringar syns inte:** töm `writable/cache/`. CSS och JS har versionsparameter och byts direkt.
