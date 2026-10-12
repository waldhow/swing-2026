// Kör mot en lokal server: php spark serve --port 8120, eller BASE_URL=https://swinggolf.se för smoke test.
const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
  testDir: '.',
  timeout: 30_000,
  reporter: [['list']],
  use: {
    baseURL: process.env.BASE_URL || 'http://localhost:8120',
    // Använd installerad Chrome — ingen separat browsernedladdning behövs.
    channel: process.env.PW_CHANNEL || 'chrome',
    reducedMotion: 'reduce',
  },
});
