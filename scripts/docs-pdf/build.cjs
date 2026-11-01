// Builds docs/pdf/<name>.pdf from the Indonesian translation docs/.build/<name>.md.
// Usage: node scripts/docs-pdf/build.cjs [name ...]   (no names = every doc in docs/final/)
const { execFileSync } = require('node:child_process');
const fs = require('node:fs');
const path = require('node:path');
const { chromium } = require('playwright');
const { checkHtml } = require('./check.cjs');

const root = path.resolve(__dirname, '../..');
const buildDir = path.join(root, 'docs/.build');
const pdfDir = path.join(root, 'docs/pdf');
const css = path.join(__dirname, 'print.css');

const footer = '<div style="width:100%;font-size:8px;color:#5B6660;text-align:center;font-family:Liberation Sans,Arial">'
  + 'Halaman <span class="pageNumber"></span> dari <span class="totalPages"></span></div>';

function names() {
  const args = process.argv.slice(2);
  if (args.length) return args;
  return fs.readdirSync(path.join(root, 'docs/final')).filter((f) => f.endsWith('.md')).map((f) => f.slice(0, -3));
}

(async () => {
  const browser = await chromium.launch();
  let failed = false;
  try {
    for (const name of names()) {
      const md = path.join(buildDir, `${name}.md`);
      if (!fs.existsSync(md)) {
        console.error(`missing translation: docs/.build/${name}.md`);
        failed = true;
        continue;
      }
      const title = fs.readFileSync(md, 'utf8').match(/^# (.+)$/m)[1];
      const html = path.join(buildDir, `${name}.html`);
      execFileSync('pandoc', [md, '-f', 'gfm', '-t', 'html5', '--standalone', '--embed-resources',
        '--no-highlight', '--metadata', 'lang=id', '--metadata', `pagetitle=${title}`,
        '--css', css, '-o', html], { stdio: ['ignore', 'ignore', 'inherit'] });

      const page = await browser.newPage();
      await page.goto(`file://${html}`, { waitUntil: 'networkidle' });
      await page.evaluate(() => document.fonts.ready);
      const problems = await checkHtml(page);
      if (problems.length) {
        console.error(`${name}: content wider than the page:\n  ${problems.join('\n  ')}`);
        failed = true;
      }
      await page.pdf({
        path: path.join(pdfDir, `${name}.pdf`), format: 'A4', printBackground: true, preferCSSPageSize: true,
        displayHeaderFooter: true, headerTemplate: '<span></span>', footerTemplate: footer,
      });
      await page.close();
      console.log(`built docs/pdf/${name}.pdf`);
    }
  } finally {
    await browser.close();
  }
  process.exit(failed ? 1 : 0);
})();
