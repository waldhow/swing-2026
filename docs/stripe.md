# Betalning med Stripe

Spelpotter, presentkort och friskvårdspotter kan betalas direkt i Stripe Checkout (Stripes hostade kassa) i stället för manuell Swish. Funktionen är **avstängd som standard** (`payments.provider = swish`) och slås på i `.env` när den är testad (TECH-06).

## Flöde

1. Kunden väljer produkt och skickar formuläret. Ordern valideras och sparas som vanligt, med `payment_provider = stripe` och `payment_status = pending`. Den lämnas **inte** över till swing-order ännu.
2. Webbplatsen skapar en Checkout-session på servern (belopp från `Config\Swing` eller kampanjen, aldrig från formuläret) och skickar kunden till Stripe med `303`.
3. Kunden betalar med de betalsätt som är påslagna i Stripe Dashboard (kort, Swish, Apple Pay, Google Pay …). Koden skickar inga `payment_method_types`.
4. Stripe skickar en webhook till `/stripe/webhook/`. Signaturen kontrolleras, sessionen hämtas från Stripe och belopp och valuta jämförs med ordern. Om `payment_status` är `paid` markeras ordern som betald (atomärt) och lämnas över till swing-order. Kunden får också en orderbekräftelse.
5. Kunden kommer tillbaka till `/tack/bestallning/{token}/?session_id=…`. Tacksidan gör samma kontroll som webhooken, så kunden ser "Betalningen är klar" även om webhooken är försenad. Det blir aldrig dubbel leverans, eftersom bara ett anrop kan vinna `OrderModel::claimPaid()`.

| Händelse | Vad som händer |
| --- | --- |
| `checkout.session.completed` | Levereras om `payment_status` är `paid`, annars väntar ordern. |
| `checkout.session.async_payment_succeeded` | Levereras (fördröjda betalsätt). |
| `checkout.session.async_payment_failed` | Ordern får status `failed`. |
| `checkout.session.expired` | Ordern får status `expired` (sessionen gäller 60 min). |

**Avbrutet köp:** kunden kommer till tacksidan med `?avbruten=1` och kan försöka igen (POST `/tack/bestallning/{token}/betala/`) inom ett dygn. Så länge den förra sessionen är öppen återanvänds den, så kunden kan aldrig betala två kassor för samma order.

**Säkerhet**
- Nycklar finns bara i `.env` och aldrig i koden eller git.
- Stripe slås bara på om nyckeln börjar med `rk_`/`sk_` och webhookhemligheten med `whsec_` (fail closed).
- Webhooken svarar `400` på fel signatur och `503` om hemligheten saknas.
- En order som inte är betald kan inte lämnas över, varken automatiskt eller från admin.
- `stripe-php` med fast API-version, idempotensnyckel per kassaförsök och `integration_identifier` för uppföljning i Dashboard.

## Inställningar (`.env`)

```ini
payments.provider = stripe
payments.stripeSecretKey = rk_test_…       # begränsad nyckel
payments.stripeWebhookSecret = whsec_…
payments.stripeMethodsText = 'kort eller Swish'
```

`stripeMethodsText` styr texterna på sajten ("Betala med kort eller Swish"). Håll den i synk med betalsätten i Dashboard.

**Begränsad nyckel (rekommenderas framför `sk_`):** skapa den under *Utvecklare → API-nycklar → Skapa begränsad nyckel* med rättigheten **Checkout Sessions: Write**. Inga andra rättigheter behövs. Om något saknas syns det i `writable/logs` som ett Stripe-fel när kassan ska skapas.

## Testa lokalt

1. Skapa en egen **sandlåda** i Stripe Dashboard (en isolerad testmiljö), till exempel "Swing webb – utveckling". Ta en `rk_test_`-nyckel därifrån.
2. Starta sajten: `php spark serve --port 8120`.
3. Vidarebefordra webhooks med Stripe CLI:
   ```bash
   stripe listen --forward-to localhost:8120/stripe/webhook/ \
     --events checkout.session.completed,checkout.session.async_payment_succeeded,checkout.session.async_payment_failed,checkout.session.expired
   ```
   Kommandot skriver ut en `whsec_…`. Lägg den i `payments.stripeWebhookSecret`.
4. Köp en spelpott på `/spelpotter/` och betala med testkortet `4242 4242 4242 4242` (valfritt framtida datum och CVC).
5. Kontrollera:
   - Tacksidan visar "Betalningen är klar".
   - Admin → Beställningar visar *Betald* och *Överlämnad*.
   - Ordermejlet till swing-order har raderna `betalning:` och `stripe:`.
6. Testa också:
   - Avbryt i kassan → "Inga pengar har dragits" och knappen *Försök betala igen*.
   - Kortet `4000 0000 0000 0002` (nekas).
   - `stripe trigger checkout.session.expired`.

Automatiska tester utan nätverk finns i `tests/feature/StripeCheckoutTest.php` (fejkad gateway och signerade webhooks).

## Inför lansering

- [ ] Stripe-kontot är aktiverat för Pomada AB (org.nr 559369-9019, se `Config\Swing`) med bankkonto och kontaktuppgifter. Det publika namnet på kortutdraget är *Swing Golf & Lounge*.
- [ ] Swish är aktiverat under *Inställningar → Betalmetoder*, och `stripeMethodsText` stämmer.
- [ ] *Inställningar → Kundmejl*: kvitton vid lyckade betalningar är på, så kunden får ett kvitto med moms från Stripe.
- [ ] Webhook-endpoint `https://swinggolf.se/stripe/webhook/` är skapad i live-läge med de fyra händelserna ovan. Dess `whsec_` ligger i produktionens `.env`.
- [ ] En begränsad live-nyckel (`rk_live_…`) med *Checkout Sessions: Write* ligger i produktionens `.env`.
- [ ] Migreringen `AddPaymentToOrders` är körd i produktion via `/admin/setup/?token=…` (se `docs/drift-loopia.md`).
- [ ] **swing-order** hanterar Stripe-betalda ordrar: personalen ska inte vänta på Swish för ordrar med raden `betalning:Stripe`. Med API-kanalen skickas `paymentVerified: true`. Se `docs/swing-order.md`.
- [ ] Ett riktigt köp med litet belopp i live-läge är genomfört och återbetalat (*Betalningar → Återbetala*).
- [ ] Köpvillkor och integritetspolicy har granskats. De visar Stripe-texterna automatiskt när Stripe är på.

## Återbetalning och ångerrätt

Återbetalningar görs i Stripe Dashboard under *Betalningar*. Admin-sidan för ordern länkar direkt till betalningen. Koden i Sweetspot spärras i swing-order som tidigare.
