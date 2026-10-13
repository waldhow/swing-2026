// Kritiska flöden (QA-03, TECH-10, UX-01, UX-05). Skickar aldrig en riktig bokning.
const { test, expect } = require('@playwright/test');

const hideDebug = async (page) => page.addStyleTag({ content: '#debug-icon,#debug-bar,.kint-rich{display:none!important}' });

test.describe('startsidan', () => {
  test('H1, boka-CTA till Sweetspot och köp spelpott', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('main h1')).toHaveText('Golfen tar inte paus för vädret');
    const book = page.locator('.hero a.btn-primary');
    await expect(book).toHaveAttribute('href', /^https:\/\/book\.sweetspot\.io\/clubs\/swing-golf-lounge/);
    await expect(book).toHaveAttribute('target', '_blank');
    await expect(page.locator('#huvudmeny a.nav-cta')).toHaveAttribute('target', '_blank');
    await expect(page.locator('.hero a', { hasText: 'Köp spelpott' })).toHaveAttribute('href', '/spelpotter/');
  });

  test('skip-länken är första tabbstoppet och leder till innehållet', async ({ page }) => {
    await page.goto('/');
    await page.keyboard.press('Tab');
    const skip = page.locator('.skip-link');
    await expect(skip).toBeFocused();
    await page.keyboard.press('Enter');
    await expect(page).toHaveURL(/#innehall$/);
  });
});

test.describe('mobilmeny 375 px', () => {
  test.use({ viewport: { width: 375, height: 800 } });

  test('Boka tid syns utan att öppna menyn', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('.nav-cta-m')).toBeVisible();
    await expect(page.locator('#huvudmeny')).toBeHidden();
  });

  test('öppnas med Enter, går att tabba i och stängs med Escape', async ({ page }) => {
    await page.goto('/priser/');
    await hideDebug(page);
    const burger = page.locator('.burger');
    await burger.focus();
    await page.keyboard.press('Enter');
    await expect(burger).toHaveAttribute('aria-expanded', 'true');
    await expect(page.locator('#huvudmeny')).toBeVisible();
    await expect(page.locator('#huvudmeny a').first()).toBeFocused();
    await page.keyboard.press('Tab');
    await expect(page.locator('#huvudmeny .nav-toggle').first()).toBeFocused();
    await expect(page.locator('#huvudmeny a[aria-current="page"]')).toHaveText('Priser');
    await page.keyboard.press('Escape');
    await expect(burger).toHaveAttribute('aria-expanded', 'false');
    await expect(page.locator('#huvudmeny')).toBeHidden();
    await expect(burger).toBeFocused();
  });

  test('Presentkort finns i menyn och markeras på sin sida', async ({ page }) => {
    await page.goto('/presentkort/');
    await page.locator('.burger').click();
    await expect(page.locator('#huvudmeny a[aria-current="page"]')).toHaveText('Presentkort');
  });

  test('undermeny fälls ut i mobilmenyn', async ({ page }) => {
    await page.goto('/');
    await page.locator('.burger').click();
    const toggle = page.getByRole('button', { name: 'Fler sidor under Anläggningen' });
    await toggle.click();
    await expect(toggle).toHaveAttribute('aria-expanded', 'true');
    await page.locator('.nav-sub a', { hasText: 'TrackMan iO & simulatorer' }).click();
    await expect(page).toHaveURL(/\/trackman-io\/$/);
  });

  test('menylänk navigerar till sidan', async ({ page }) => {
    await page.goto('/');
    await page.locator('.burger').click();
    await page.locator('#huvudmeny .nav-split > a', { hasText: /^Kontakt$/ }).click();
    await expect(page).toHaveURL(/\/kontakt\/$/);
    await expect(page.locator('main h1')).toHaveText('Kontakta Swing Golf & Lounge');
  });
});

test.describe('desktop 1280 px', () => {
  test.use({ viewport: { width: 1280, height: 900 } });

  test('hela menyn och Boka tid syns, ingen hamburgare', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('.burger')).toBeHidden();
    for (const label of ['Anläggningen', 'Priser', 'Spelpotter', 'Presentkort', 'Företag & grupper', 'Tävlingar', 'Friskvård', 'Kontakt', 'Boka tid']) {
      await expect(page.locator('#huvudmeny a', { hasText: label }).first()).toBeVisible();
    }
    // Undermeny: pilen öppnar, Esc stänger och lämnar fokus på pilen.
    const toggle = page.getByRole('button', { name: 'Fler sidor under Kontakt' });
    await toggle.click();
    await expect(page.locator('.nav-sub a', { hasText: 'Inför ditt besök' })).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(toggle).toHaveAttribute('aria-expanded', 'false');
    await expect(toggle).toBeFocused();
    // Mörk text på mässingsknappen (inte menylänkarnas ljusa färg).
    await expect(page.locator('#huvudmeny a.nav-cta')).toHaveCSS('color', 'rgb(28, 17, 8)');
  });
});

for (const width of [320, 375, 768, 1280]) {
  test(`ingen horisontell scroll vid ${width} px`, async ({ page }) => {
    await page.setViewportSize({ width, height: 900 });
    for (const path of ['/', '/priser/', '/spelpotter/', '/presentkort/', '/foretagsevent/', '/kontakt/']) {
      await page.goto(path);
      await hideDebug(page);
      const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
      expect(overflow, `${path} vid ${width} px`).toBeLessThanOrEqual(0);
    }
  });
}

test('knappar har aldrig understruken text', async ({ page }) => {
  for (const path of ['/', '/friskvard/', '/boka/', '/spelpotter/', '/foretagsevent/', '/kontakt/']) {
    await page.goto(path);
    const underlined = await page.$$eval('a.btn, a.nav-cta, a.card-link, .btn-row a', (els) =>
      els.filter((el) => getComputedStyle(el).textDecorationLine.includes('underline')).map((el) => el.textContent.trim()));
    expect(underlined, `${path}: understrukna knappar`).toEqual([]);
  }
});

// Vänta tills en (mjuk) skrollning har stannat i stället för en fast tid.
async function scrollSettled(page) {
  let last = -1;
  await expect.poll(async () => {
    const y = await page.evaluate(() => window.scrollY);
    const still = y === last;
    last = y;
    return still;
  }, { intervals: [100, 150, 200, 250], timeout: 5000 }).toBe(true);
}

// Ankarlänkar landar vid avsnittets rubrik och ingress — under den fasta menyn, inte bakom den.
const anchorCases = [
  { name: 'Köp spelpott på spelpotter', path: '/spelpotter/', click: '.phero a[href="#kop"]', target: '#kop' },
  { name: 'Köp presentkort', path: '/presentkort/', click: '.phero a[href="#kop"]', target: '#kop' },
  { name: 'Köp friskvårdspott', path: '/friskvard/', click: '.phero a[href="#kop"]', target: '#kop' },
  { name: 'Begär offert på företagsevent', path: '/foretagsevent/', click: '.phero a[href="#offert"]', target: '#offert' },
  { name: 'Fråga om gruppbokning', path: '/gruppbokning/', click: '.phero a[href="#fraga"]', target: '#fraga' },
  { name: 'Fråga om egen tävling', path: '/tavlingar/', click: 'a.btn[href="#egen"]', target: '#egen' },
  { name: 'Begär offert från startsidan', path: '/', click: 'a[href="/foretagsevent/#offert"]', target: '#offert' },
  { name: 'Ladda ner Sweetspot-appen på boka', path: '/boka/', click: '.phero a[href="#appar"]', target: '#appar' },
];

for (const width of [375, 1280]) {
  test.describe(`ankarlänkar vid ${width} px`, () => {
    test.use({ viewport: { width, height: 800 } });

    for (const c of anchorCases) {
      test(c.name, async ({ page }) => {
        await page.goto(c.path);
        await page.locator(c.click).first().click();
        await expect(page).toHaveURL(new RegExp(c.target + '$'));
        await scrollSettled(page);
        const r = await page.evaluate((sel) => {
          const el = document.querySelector(sel);
          const heading = el.querySelector('h2');
          const intro = heading.nextElementSibling;
          return {
            navBottom: document.querySelector('.nav').getBoundingClientRect().bottom,
            top: el.getBoundingClientRect().top,
            headingTop: heading.getBoundingClientRect().top,
            introBottom: intro ? intro.getBoundingClientRect().bottom : heading.getBoundingClientRect().bottom,
            viewport: window.innerHeight,
          };
        }, c.target);
        expect(r.top, 'rubrikblocket ska börja under menyn').toBeGreaterThanOrEqual(r.navBottom + 8);
        expect(r.headingTop).toBeGreaterThan(r.navBottom);
        expect(r.introBottom, 'ingressen ska synas utan att scrolla').toBeLessThanOrEqual(r.viewport);
      });
    }

    test('första felaktiga fältet syns under menyn efter inskick', async ({ page }) => {
      await page.goto('/kontakt/');
      await page.locator('#formular button[type="submit"]').click();
      await expect(page.locator('#f-name')).toBeFocused();
      await scrollSettled(page);
      const { navBottom, top } = await page.evaluate(() => ({
        navBottom: document.querySelector('.nav').getBoundingClientRect().bottom,
        top: document.querySelector('label[for="f-name"]').getBoundingClientRect().top,
      }));
      expect(top).toBeGreaterThanOrEqual(navBottom);
    });

    test('pottfelet syns ovanför korten efter inskick', async ({ page }) => {
      await page.goto('/spelpotter/');
      await page.locator('#bestall button[type="submit"]').click();
      await expect(page.locator('.pot-radio').first()).toBeFocused();
      const err = page.locator('#f-produkt-err');
      await scrollSettled(page);
      await expect(err).toBeInViewport();
      const { navBottom, top } = await page.evaluate(() => ({
        navBottom: document.querySelector('.nav').getBoundingClientRect().bottom,
        top: document.querySelector('#f-produkt-err').getBoundingClientRect().top,
      }));
      expect(top).toBeGreaterThanOrEqual(navBottom);
    });
  });
}

for (const width of [375, 1280]) {
  test(`pott från startsidan är förvald på spelpotter (${width} px)`, async ({ page }) => {
    await page.setViewportSize({ width, height: 800 });
    await page.goto('/');
    await page.locator('.pots a[href="/spelpotter/#pott-2000"]').click();
    await expect(page).toHaveURL(/\/spelpotter\/#pott-2000$/);
    await expect(page.locator('.pot-radio[value="pott-2000"]')).toBeChecked();
    await expect(page.locator('.pot-card', { hasText: 'Välj Swing 2000' }).locator('.pot-pick .on')).toBeVisible();
    await expect.poll(() => page.evaluate(() => {
      const nav = document.querySelector('.nav').getBoundingClientRect().bottom;
      const head = document.querySelector('#kop h2').getBoundingClientRect().top;
      return head > nav && head < window.innerHeight;
    })).toBe(true);
  });
}

test('prissidan visar pris per simulator och räkneexempel', async ({ page }) => {
  await page.goto('/priser/');
  const table = page.locator('table.dtable').first();
  await expect(table).toContainText('Måndag–fredag 15.00–00.00');
  await expect(table).toContainText('280 kr');
  await expect(page.locator('.examples')).toContainText('37,50 kr');
  await expect(page.locator('.examples')).toContainText('70 kr');
});

test('FAQ-svar öppnas med tangentbordet', async ({ page }) => {
  await page.goto('/vanliga-fragor/');
  const item = page.locator('details#dorrkod');
  await item.locator('summary').focus();
  await page.keyboard.press('Enter');
  await expect(item).toHaveAttribute('open', '');
  await expect(item.locator('.faq-a')).toContainText('avsluta med #');
});

test('formulärfel visas vid fälten och första fältet får fokus', async ({ page }) => {
  await page.goto('/kontakt/');
  await page.locator('#formular button[type="submit"]').click();
  await expect(page.locator('.form-alert')).toHaveCount(0);
  await expect(page.locator('#f-name')).toBeFocused();
  await expect(page.locator('#f-name-err')).toContainText('Fyll i namn.');
  await expect(page.locator('#f-email')).toHaveAttribute('aria-invalid', 'true');
  await expect(page.locator('#f-email')).toHaveAttribute('aria-describedby', /f-email-err/);
});

test('formulär valideras i webbläsaren utan omladdning', async ({ page }) => {
  await page.goto('/kontakt/');
  let posted = false;
  page.on('request', (r) => { if (r.method() === 'POST') posted = true; });
  await page.locator('#formular button[type="submit"]').click();
  await expect(page.locator('#f-name-err')).toContainText('Fyll i namn.');
  await expect(page.locator('#f-message-err')).toContainText('Fyll i meddelande.');
  await expect(page.locator('#f-name')).toBeFocused();
  expect(posted).toBe(false);

  // Felet försvinner när fältet är rättat.
  await page.fill('#f-name', 'Anna');
  await expect(page.locator('#f-name-err')).toHaveCount(0);
  await expect(page.locator('#f-name')).not.toHaveAttribute('aria-invalid', 'true');

  // Felet byter text medan man rättar: tomt → ogiltig adress → borta.
  await page.fill('#f-email', 'inte-en-adress');
  await expect(page.locator('#f-email-err')).toContainText('Ange en giltig e-postadress.');
  await expect(page.locator('#f-email')).toHaveAttribute('aria-describedby', /f-email-err/);
  await page.fill('#f-email', 'anna@exempel.se');
  await expect(page.locator('#f-email-err')).toHaveCount(0);
});

test('friskvård: pottval och personnummer kontrolleras i webbläsaren', async ({ page }) => {
  await page.goto('/friskvard/');
  await page.fill('#f-personnummer', '19811218-9875');
  await page.locator('#bestall button[type="submit"]').click();
  await expect(page.locator('#f-produkt-err')).toContainText('Välj vilken pott du vill beställa.');
  await expect(page.locator('#f-personnummer-err')).toContainText('Personnumret verkar inte stämma');
  await expect(page.locator('.pot-radio').first()).toBeFocused();

  await page.locator('label#pott-1000').click();
  await expect(page.locator('#f-produkt-err')).toHaveCount(0);
  await page.fill('#f-personnummer', '19811218-9876');
  await expect(page.locator('#f-personnummer-err')).toHaveCount(0);
});

test.describe('utan JavaScript', () => {
  test.use({ javaScriptEnabled: false });

  test('servern visar felen vid fälten', async ({ page }) => {
    await page.goto('/kontakt/');
    await page.locator('#formular button[type="submit"]').click();
    await expect(page).toHaveURL(/\/kontakt\/#formular$/);
    await expect(page.locator('#f-name-err')).toContainText('Fyll i namn.');
    await expect(page.locator('#f-name')).toHaveAttribute('aria-invalid', 'true');
  });
});

// Förifylld Swish: QR-kod på datorn, knapp till appen i mobilen – samma betalningslänk.
// Testerna lägger en riktig beställning, så de körs bara mot en lokal server.
async function placeSwishOrder(page, baseURL) {
  test.skip(!/^http:\/\/(localhost|127\.0\.0\.1)[:/]/.test(baseURL), 'lägger en riktig beställning – körs bara lokalt');
  await page.goto('/presentkort/');
  await page.selectOption('#f-belopp', '300');
  await page.fill('#f-name', 'E2E Test');
  await page.fill('#f-email', 'e2e@example.com');
  await page.check('#f-villkor');
  await page.locator('#bestall button[type="submit"]').click();
  await expect(page).toHaveURL(/\/tack\/bestallning\/[a-f0-9]{32}\/(#bestall)?$/);
}

test('tacksidan visar Swish-QR på datorn', async ({ page, baseURL }) => {
  await placeSwishOrder(page, baseURL);
  await expect(page.locator('.swish-qr svg')).toBeVisible();
  await expect(page.locator('.swish-app')).toBeHidden();
  const ref = (await page.locator('.swish .row').nth(2).locator('.v').innerText()).trim();
  expect(await page.locator('.swish-app a').getAttribute('href')).toBe(`https://app.swish.nu/1/p/sw/?sw=1232172559&amt=300.00&msg=${ref}`);
});

test.describe('i mobilen', () => {
  test.use({ viewport: { width: 390, height: 844 }, isMobile: true, hasTouch: true });

  test('tacksidan har knappen Betala med Swish i stället för QR', async ({ page, baseURL }) => {
    await placeSwishOrder(page, baseURL);
    await expect(page.locator('.swish-app a')).toBeVisible();
    await expect(page.locator('.swish-app a')).toHaveText('Betala med Swish');
    await expect(page.locator('.swish-app a img')).toHaveJSProperty('complete', true);
    expect(await page.locator('.swish-app a img').evaluate((img) => img.naturalWidth)).toBeGreaterThan(0);
    await expect(page.locator('.swish-qr')).toBeHidden();
  });
});

test('okänd sida ger 404 med vägar vidare', async ({ page }) => {
  const response = await page.goto('/finns-inte/');
  expect(response.status()).toBe(404);
  await expect(page.locator('main h1')).toHaveText('Sidan hittades inte');
  await expect(page.locator('.nf-links a')).toHaveCount(6);
});

test('gammal WordPress-adress omdirigeras', async ({ page }) => {
  await page.goto('/priser-medlemskap/');
  await expect(page).toHaveURL(/\/priser\/$/);
});
