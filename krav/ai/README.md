# SWING GOLF & LOUNGE · AI-HANDOFF

Agenten får även automatiskt inledande anvisningar i `AGENTS.md` om den stöder sådana filer. Använd `IMPLEMENTATION_STATUS_TEMPLATE.md` för att granska leveransen.

Detta är ett *kravpaket för en AI-kodagent* som ska implementera den nya webbplatsen. Det är **inte källkoden** till webbplatsen.

## Användning

1. Packa upp zip-filen i AI-kodagentens projekt/arbetsyta, gärna som `docs/ai-handoff/` i det verkliga kod-repot.
2. Öppna `AI_BUILD_SPEC.html` om du vill granska kraven som ett läsbart dokument.
3. Ge agenten innehållet i `START_HERE_AI_PROMPT.md` och tillgång till hela paketet och det faktiska kod-repot.
4. Låt agenten **inventera befintlig kod först**. Be om en lista med blockers och planerade P0-ändringar innan större refaktorering.
5. Bedöm leveransen med ID:n i `data/requirements.json` och acceptanskriterierna i `AI_BUILD_SPEC.md`.

Det ursprungliga, visuella innehållsunderlaget finns under `references/`. Det tidigare dokumentet är underlag, men denna AI-spec har företräde vid motstridiga instruktioner om *hur implementationen ska ske*.

**Kända blockers:** Verifierad Sweetspot-länk, befintlig kod/ramverk, godkända produktionsbilder, livelänkar för presentkort/spelpotter, exakt hantering av Stripe/Epassi, godkännande av vissa paketerbjudanden och historikår. Agenten får inte hitta på svar.

**Inget leveransdatum eller tidplan ingår.**
