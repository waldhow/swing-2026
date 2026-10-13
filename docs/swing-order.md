# Integration med swing-order

Beställningar av spelpotter, presentkort och friskvårdspotter tas emot på webbplatsen och hanteras i **swing-order**, som reserverar Sweetspot-kod, verifierar Swish-betalningen och skickar bekräftelse med kvitto.

## Flöde

1. Kunden fyller i formuläret på `/spelpotter/`, `/presentkort/` eller `/friskvard/` (det sista bara när `orders.friskvardDirect = true`).
2. Webbplatsen validerar mot `Config\Swing` eller en aktiv kampanj. Belopp från formuläret används aldrig.
3. Ordern sparas i tabellen `orders` med en kort referens, `W1042`. Den bygger på radens id och återanvänds aldrig. W skiljer den från swing-orders egna `ORD-`nummer. Kunden skriver referensen i Swish-meddelandet. Äldre ordrar har formatet `W-ÅÅMMDD-XXXX`.
4. Ordern skickas till swing-order via vald kanal (`orders.channel`).
5. Kunden ser en tacksida med en förifylld Swish-betalning och får samma uppgifter via e-post. Nummer, belopp och ordernummer är redan ifyllda och låsta. I mobilen öppnar knappen *Betala med Swish* appen. Knappen är vit med Swish officiella ikon, enligt Swish riktlinjer för webbshoppar. På datorn visas en QR-kod. Båda går till samma länk, `https://app.swish.nu/1/p/sw/?sw=…&amt=…&msg=W1042`, enligt Swish utvecklarguide. Manuella uppgifter finns kvar som reserv.
6. swing-order levererar koden när betalningen är verifierad.

Misslyckas steg 4 sparas felet på ordern. Den syns i admin under **Beställningar** och kan skickas igen. Lokala kopior raderas efter `Orders::$retentionDays` (90) dagar, eftersom swing-order är huvudregistret.

## Kanal 1: mejl (`orders.channel = mail`, standard)

Kräver inga ändringar i swing-order. Webbplatsen skickar ett textmejl till `orders.mailTo`, alltså brevlådan som swing-order synkar via IMAP (`NUXT_MAILBOX_USER`, ämnesfilter `NUXT_MAILBOX_SUBJECT=Order`).

Formatet matchar `server/lib/mailIngestion.ts` i swing-order. Ämnet ska vara exakt `Order`, med en rad per fält:

```text
type:Spelpott            ← Spelpott | Presentkort | Friskvård
price:1000               ← betalt belopp i hela kronor (matchas mot kodlagrets belopp)
email:kund@exempel.se
name:Anna Andersson
personnummer:19900101-1234   ← endast Friskvård
name on giftcard:Pappa       ← endast Presentkort, om ifyllt
design:JUL                   ← endast Presentkort: STANDARD | MORSDAG | FARSDAG | JUL (utan val STANDARD)

referens:W1042           ← information till personalen, ignoreras av parsern
produkt:Swing 1000
spelvarde:1150
kalla:swinggolf.se
```

Reply-To sätts till kunden. Radbrytningar i kunddata tas bort så att inga extra fält kan injiceras (testas i `tests/unit/MailOrderGatewayTest.php`).

**Presentkortsdesign**

swing-order behöver en design för att kunna skapa presentkortet. Utan design svarar `giftcard.get.ts` med *"Presentkortsdesign saknas på ordern"*, och `send-confirmation` bifogar inget kort. Mejltolken i swing-order läser i dag ingen design.

- **Webben** skickar alltid raden `design:` för presentkort. Utan val i formuläret är värdet `STANDARD`.
- **I swing-order** behövs en liten ändring. Den finns som patch i `docs/patches/swing-order-giftcard-design.patch` och appliceras med `git apply` i swing-order. Med ändringen läser `mailIngestion.ts` raden `design:` (ogiltig eller saknad blir `STANDARD`), och `mailImportService.ts` sparar `giftCardDesign` på ordern.
- **När swing-order har ändringen:** sätt `orders.mailCarriesGiftCardDesign = true` i `.env`. Då visas designvalet i presentkortsformuläret på webben.
- **Namnet på kortet** skrivs bara ut på designen Standard (`server/lib/giftCard.ts`), och formuläret säger det.

**Begränsningar i mejlkanalen**
- `price` för kampanjpotter är kampanjens betalbelopp, till exempel 2500. Kodlagret i swing-order behöver koder med det beloppet.
- Idempotensen i swing-order bygger på IMAP-UID. Om ett mejl skickas om från admin skapas en ny order, så kontrollera i swing-order först.

## Kanal 2: API (`orders.channel = api`) – förberett, inte aktivt

Webbplatsen har en färdig klient (`app/Libraries/Orders/ApiOrderGateway.php`). Den används när swing-order har en motsvarande endpoint, och sedan räcker en ändring i `.env`. Föreslaget kontrakt:

```http
POST /api/integrations/orders
Authorization: Bearer <orders.apiToken>
Idempotency-Key: W1042
Content-Type: application/json

{
  "externalRef": "W1042",
  "source": "WEB",
  "type": "SPELPOTT",              // SPELPOTT | PRESENTKORT | FRISKVARD
  "amount": 1000,                  // betalt belopp
  "value": 1150,                   // spelvärde (information)
  "name": "Anna Andersson",
  "email": "anna@exempel.se",
  "personalNumber": "19900101-1234",  // endast FRISKVARD
  "giftCardName": "Pappa",            // valfri
  "giftCardDesign": "FARSDAG",        // STANDARD | MORSDAG | FARSDAG | JUL
  "productName": "Swing 1000"
}
```

Förväntat svar är `2xx` vid lyckad eller redan mottagen order, alltså samma `Idempotency-Key`. Allt annat räknas som fel och kan skickas igen från admin.

I swing-order behövs ungefär:
- en route som validerar bearer-token mot en hemlighet i miljön, till exempel `NUXT_WEB_ORDER_TOKEN`,
- återanvändning av samma logik som `mail/ingest`: reservera kod ur kodlagret, `source = WEB`, notiser via Slack, mejl och push,
- idempotens på `externalRef`.

Med API-kanalen kan presentkortsdesignen följa med, och dubbletter vid omsändning undviks.
