// Kontroll av externa länkar (TECH-13). Kräver nätverk — körs bara med CHECK_EXTERNAL=1.
const { test, expect } = require('@playwright/test');

const PAGES = ['/', '/inomhusgolf/', '/priser/', '/boka/', '/trackman-io/', '/foretagsevent/', '/gruppbokning/', '/spelpotter/', '/friskvard/', '/presentkort/', '/tavlingar/', '/team-swing/', '/om-oss/', '/kontakt/', '/vanliga-fragor/', '/infor-besoket/', '/kopvillkor/', '/integritetspolicy/'];

test('alla externa länkar svarar', async ({ page, request, baseURL }) => {
  test.skip(!process.env.CHECK_EXTERNAL, 'sätt CHECK_EXTERNAL=1 för att kontrollera externa länkar');
  test.setTimeout(120_000);

  const own = new URL(baseURL).host;
  const links = new Set();
  for (const path of PAGES) {
    await page.goto(path);
    for (const href of await page.$$eval('a[href^="http"]', (as) => as.map((a) => a.href))) {
      if (new URL(href).host !== own) links.add(href);
    }
  }

  for (const url of links) {
    const res = await request.get(url, { maxRedirects: 5, failOnStatusCode: false, headers: { 'User-Agent': 'Mozilla/5.0 (Macintosh)' } });
    // 429 = tjänsten begränsar automatiska anrop; länken finns.
    expect([200, 429], `${url} svarade ${res.status()}`).toContain(res.status());
  }
});
