# Backlog

Uppgifter som är beslutade men inte påbörjade.

## Användare i admin

**Sparad:** 2026-10-13. **Läge:** inte påbörjad.

I dag skapas bara den första admin-användaren, på `/admin/setup/?token=…`. Varje användare kan sedan ändra sitt eget namn, sin e-post och sitt lösenord på Konto-sidan. Det går inte att lägga till eller ta bort andra användare.

Förslag på en sida "Användare" i admin:

- **Lista:** namn, e-post och senaste inloggning.
- **Bjud in** med namn och e-post. Personen får ett mejl med en engångslänk som gäller i 48 timmar och väljer själv sitt lösenord, minst `Admin::$minPasswordLength` tecken. Inga lösenord skickas mellan personer.
  - Enklare variant: ett tillfälligt lösenord som personen byter på Konto-sidan.
- **Ta bort** användare. Det ska inte gå att ta bort sig själv eller den sista användaren.
- **Behörighet:** samma för alla. Roller kan läggas till senare vid behov.
- **Säkerhet** som resten av admin: filtret `adminauth`, CSRF-skydd och tester i `tests/feature/AdminTest.php`.

Kvar att bestämma: inbjudningslänk eller tillfälligt lösenord.
