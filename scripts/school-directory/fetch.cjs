// Fetches SD and MI of one regency from the Kemendikdasmen reference site into a JSON file
// for `php artisan school-directory:import`. Run rarely and by hand; it waits between requests.
//
//   node scripts/school-directory/fetch.cjs <regency-code> <output.json>
//   node scripts/school-directory/fetch.cjs 031900 docs/data/schools/kudus.json
'use strict';

const fs = require('node:fs');

const BASE = 'https://referensi.data.kemendikdasmen.go.id/pendidikan/dikdas';
const LEVELS = { 5: 'sd', 9: 'mi' }; // the site's "bentuk pendidikan" codes
const DELAY_MS = 1500;

const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, ms));

const decode = (text) => text
    .replace(/&amp;/g, '&').replace(/&quot;/g, '"').replace(/&#0?39;/g, "'")
    .replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&nbsp;/g, ' ')
    .replace(/\s+/g, ' ').trim();

async function get(url) {
    for (let attempt = 1; attempt <= 4; attempt++) {
        try {
            const response = await fetch(url, { headers: { 'User-Agent': 'SIGRAK school directory import' } });
            if (response.ok) {
                return await response.text();
            }
            throw new Error(`HTTP ${response.status}`);
        } catch (error) {
            if (attempt === 4) {
                throw new Error(`${url}: ${error.message}`);
            }
            await sleep(DELAY_MS * 2 ** attempt);
        }
    }
}

function parseDistricts(html, regencyCode) {
    const prefix = regencyCode.slice(0, 4);
    const districts = [...html.matchAll(/href="[^"]*\/dikdas\/(\d{6})\/3"[^>]*>([^<]+)</g)]
        .filter(([, code]) => code.startsWith(prefix) && code !== regencyCode)
        .map(([, code, name]) => ({ code, name: decode(name) }));

    return [...new Map(districts.map((d) => [d.code, d])).values()];
}

function parseSchools(html) {
    const body = html.slice(html.indexOf('<tbody'), html.indexOf('</tbody>'));

    return [...body.matchAll(/<tr>([\s\S]*?)<\/tr>/g)].map(([, row]) => {
        const cells = [...row.matchAll(/<td[^>]*>([\s\S]*?)<\/td>/g)].map(([, cell]) => decode(cell.replace(/<[^>]+>/g, '')));
        // Columns: No, NPSN, Nama, Alamat, Kelurahan, Status.
        return { npsn: cells[1], name: cells[2], address: cells[3], village: cells[4] };
    });
}

async function main() {
    const [regencyCode, output] = process.argv.slice(2);
    if (!/^\d{6}$/.test(regencyCode ?? '') || !output) {
        console.error('Usage: node scripts/school-directory/fetch.cjs <regency-code> <output.json>');
        process.exit(1);
    }

    const regencyUrl = `${BASE}/${regencyCode}/2`;
    const regencyHtml = await get(regencyUrl);
    const province = decode(regencyHtml.match(/\/dikdas\/\d{6}\/1\/all\/all\/all">([^<]+)/)?.[1] ?? '').replace(/^Prov\.\s*/, '');
    // The breadcrumb reads "Prov. Jawa Tengah</a> >> Kab. Kudus".
    const regency = decode(regencyHtml.match(/\/1\/all\/all\/all">[^<]+<\/a>\s*(?:>>|&gt;&gt;)\s*([^<]+)</)?.[1] ?? '')
        .replace(/^Kab\.\s*/, 'Kabupaten ');
    const districts = parseDistricts(regencyHtml, regencyCode);
    if (!province || !regency || districts.length === 0) {
        throw new Error(`Could not read the region names or districts from ${regencyUrl}`);
    }

    const schools = [];
    for (const district of districts) {
        for (const [code, level] of Object.entries(LEVELS)) {
            await sleep(DELAY_MS);
            const rows = parseSchools(await get(`${BASE}/${district.code}/3/all/${code}/all`));
            for (const row of rows) {
                if (!/^\d{8}$/.test(row.npsn) || !row.name) {
                    throw new Error(`Unexpected row in ${district.name}: ${JSON.stringify(row)}`);
                }
                schools.push({ ...row, level, district: district.name });
            }
            console.error(`${district.name} ${level.toUpperCase()}: ${rows.length}`);
        }
    }

    schools.sort((a, b) => a.npsn.localeCompare(b.npsn));
    const data = { source: regencyUrl, fetched_on: new Date().toISOString().slice(0, 10), province, regency, schools };
    fs.writeFileSync(output, `${JSON.stringify(data, null, 2)}\n`);
    console.error(`${schools.length} schools written to ${output}`);
}

main().catch((error) => {
    console.error(error.message);
    process.exit(1);
});
