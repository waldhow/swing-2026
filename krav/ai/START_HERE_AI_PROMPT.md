# KOPIERA DENNA PROMPT TILL AI-KODAGENTEN

Jag vill att du bygger vidare på vår publika webbplats för Swing Golf & Lounge. Läs **hela** den bifogade `AI_BUILD_SPEC.md` samt `data/requirements.json`, `data/pages.json`, `data/content_drafts.json`, `data/open_decisions.json` och `references/Swing_Golf_Lounge_Handoff_Kravspecifikation.html` innan du börjar.

VIKTIGT: Befintlig prototyp finns på https://swing-public-staging.up.railway.app/. Den anger den visuella riktningen, men är inte källa för affärsfakta. Återanvänd befintligt repo, komponenter och design i största möjliga mån. Byt inte ramverk eller bygg om från grunden utan godkännande.

**Börja med att:**
1. Inventera befintligt repo, arkitektur, routning, komponenter, befintliga integrationer och kommandon.
2. Sammanfatta nuläget och vilka blockerande uppgifter som saknas (exakt Sweetspot-länk, betal-/voucherintegration osv.).
3. Föreslå en kort, prioriterad, tekniskt konkret implementationsordning *utan tidsplan*.
4. Implementera P0-kraven iterativt, med fungerande sidor, bra mobil-UX, SSR/prerender, teknisk SEO och tillgänglighet.
5. Använd 2026 års Sweetspot-lösning som **enda aktiva bokningssystem**. Designa för ett framtida providerskifte men bygg inte påhittat internt bokningssystem, falsk Stripe-betalning eller låtsasbokning.
6. Testa det du bygger och lämna en kravstatusrapport med ID, filer, tester och blockerare.

**Hårda regler:** Ingen påhittad fakta, ingen fejkcheckout, inga otestade köpknappar, inga aktiva tävlingar med uppdiktade datum, ingen alkoholservering som befintlig tjänst, inga referensbilder i produktion utan godkännande. Alla priser ska stämma med specifikationen och Epassi-listan får inte "rättas" på eget initiativ.

Fråga bara efter information som verkligen blockerar ett aktuellt utvecklingssteg. Fortsätt annars med säkra delar som kan testas nu. Rapportera skillnaden mellan klart och planerat. Ingen tidsplan behövs.
