# AI Agent Instructions — Swing Golf & Lounge

This folder contains the binding implementation handoff for an AI coding agent. Read the entire `AI_BUILD_SPEC.md` first, then `data/requirements.json`, `data/pages.json`, `data/content_drafts.json`, `data/open_decisions.json`, and the visual content handoff in `references/`.

## Priorities

- Inspect the existing repository and prototype implementation before changing code. Reuse existing layout and design patterns.
- Implement requirements by ID, P0 before P1, with tests and visible proof.
- Use Sweetspot as the **only currently active** booking provider. The proprietary booking product is *planned*, not live.
- Content drafts are not automatically approved. Verified owner facts supersede older copy and mockups.
- Do not invent third-party APIs, payment integrations, login flows, offers, events, partners, images, or legal permissions.
- Do not publish reference imagery to production without specific approval. Never add private secrets to git or client bundles.
- Never deploy to production, start live charging, or migrate user data without explicit authorization.
- Missing live URLs/integrations must disable the affected public action and be reported as `BLOCKED`.
- Do not create a schedule unless the owner asks. Work in small, verifiable increments.

## Done means

Requirements met + functioning interaction + factual review + passing tests + report. Follow the output contract in section 10 of `AI_BUILD_SPEC.md`. The suggested report format is in `IMPLEMENTATION_STATUS_TEMPLATE.md`.
