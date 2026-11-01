// Checks for the Indonesian PDFs. Used by build.cjs; can also run alone:
// node scripts/docs-pdf/check.cjs [name ...]   reports leftover English prose in docs/.build/<name>.md
const fs = require('node:fs');
const path = require('node:path');

// Width of the A4 content area (210 mm - 2 × 16 mm margins) in CSS pixels.
const CONTENT_WIDTH_PX = Math.floor((178 / 25.4) * 96);

// Returns the tables and code blocks that are wider than the printed page.
async function checkHtml(page) {
  await page.setViewportSize({ width: CONTENT_WIDTH_PX, height: 1000 });
  await page.emulateMedia({ media: 'print' });
  return page.evaluate(() => {
    const width = document.documentElement.clientWidth;
    return [...document.querySelectorAll('table, pre')]
      .filter((el) => el.scrollWidth > width + 2 || el.getBoundingClientRect().right > width + 2)
      .map((el) => `${el.tagName.toLowerCase()}: ${el.textContent.trim().slice(0, 60)}`);
  });
}

// Returns prose lines (outside code) that still read like English.
function englishLines(markdown) {
  const prose = markdown.replace(/^```[\s\S]*?^```/gm, '').replace(/`[^`]*`/g, '');
  const words = /\b(the|and|is|are|with|from|which|this|that|only|never)\b/g;
  return prose.split('\n').filter((line) => (line.match(words) || []).length >= 3);
}

module.exports = { checkHtml, englishLines };

if (require.main === module) {
  const root = path.resolve(__dirname, '../..');
  const args = process.argv.slice(2);
  const list = args.length ? args
    : fs.readdirSync(path.join(root, 'docs/final')).filter((f) => f.endsWith('.md')).map((f) => f.slice(0, -3));
  let found = 0;
  for (const name of list) {
    const file = path.join(root, 'docs/.build', `${name}.md`);
    if (!fs.existsSync(file)) { console.error(`missing translation: docs/.build/${name}.md`); found++; continue; }
    const lines = englishLines(fs.readFileSync(file, 'utf8'));
    found += lines.length;
    for (const line of lines) console.log(`${name}: ${line.slice(0, 120)}`);
  }
  console.log(found ? `${found} line(s) to review` : 'no English prose found');
  process.exit(found ? 1 : 0);
}
