#!/usr/bin/env python3
"""One-time conversion to SIGRAK's own curriculum dataset (D-363, D-365, D-366).

Reads the legacy app curriculum files and the two SIGRAK religion drafts, and writes:

- docs/data/curriculum/sigrak-sd-2026-r1-umum.json   general SD subjects and PAI
- docs/data/curriculum/sigrak-sd-2026-r1-agama.json  the five other SD religions
- docs/data/curriculum/sigrak-mi-2026-r1.json        the five MI Kemenag subjects
- docs/data/curriculum/crosswalk-legacy-2026.json    old code -> new code (team only, not imported)

Run once from the repository root: python3 -I scripts/curriculum/convert_legacy_topics.py
The legacy input files were removed after the conversion (see git history of
docs/data/curriculum/ before 2026-11-10). The script is kept to show how R1 was made.
"""
import json
import re
import sys
from collections import Counter, OrderedDict

ROOT = 'docs/data/curriculum/'
SD = 'SIGRAK-SD-2026-R1'
MI = 'SIGRAK-MI-2026-R1'
TODAY = '2026-11-09'

LEGACY_TOPICS = ROOT + 'data_kurikulum_dengan_cp.json'
LEGACY_ALLOCATION = ROOT + 'struktur_alokasi_sd.json'
RELIGION_SD = ROOT + 'tp-atp-pendidikan-agama-sd-2026.json'
RELIGION_MI = ROOT + 'tp-atp-pai-bahasa-arab-mi-2026.json'

REFERENCE_KEYS = {
    'sumber_cp': 'cp_source', 'sumber_tp_atp': 'tp_atp_source', 'sumber_buku': 'book',
    'halaman_buku': 'book_pages', 'halaman_cp_buku': 'book_cp_pages', 'halaman_bab_buku': 'book_chapter_pages',
    'halaman_atp_buku': 'book_atp_pages', 'halaman_skema_buku': 'book_scheme_pages',
    'tanggal_penyesuaian': 'adjusted_on', 'status_verifikasi': 'verification',
    'sumber_struktur': 'structure_source', 'regulasi_cp': 'cp_regulation', 'tanggal_verifikasi': 'verified_on',
}
ATP_COLUMNS = {'kode_atp', 'urutan_semester', 'alokasi_jp', 'jp_per_minggu', 'minggu_mulai', 'minggu_selesai', 'distribusi_mingguan'}
ATP_EXTRA_KEYS = {
    'urutan_tahunan': 'order_in_year', 'durasi_minggu': 'duration_weeks', 'minggu_efektif_semester': 'semester_effective_weeks',
    'sumber_perhitungan': 'calculation_basis', 'total_jp_fokus': 'focus_jp_total', 'metode_alokasi_fokus': 'focus_allocation_method',
    'catatan': 'notes', 'alokasi_jp_buku': 'book_jp', 'status_penyesuaian_jp': 'jp_adjustment_status',
    'alokasi_jp_min': 'book_jp_min', 'alokasi_jp_max': 'book_jp_max', 'status_alokasi': 'allocation_status',
}
CURATION_KEYS = {'status': 'source_status', 'catatan': 'notes'}

# D-234, applied in the data instead of at import: Keterampilan Tata Boga grade 6
# semester 2 had 34 JP over weeks 1-17 but that semester has 14 effective weeks.
D234_TOPIC = 'KTB-K6-04'


def load(path):
    with open(path, encoding='utf-8') as handle:
        return json.load(handle)


def dump(path, data):
    with open(path, 'w', encoding='utf-8') as handle:
        json.dump(data, handle, ensure_ascii=False, indent=2)
        handle.write('\n')


def subject_index():
    catalogue = load(ROOT + 'subjects.json')['subjects']
    index = {}
    for subject in catalogue:
        for name in [subject['code'], subject['name'], *subject.get('aliases', [])]:
            index[name.casefold()] = subject
    return index


def semester_of(value):
    if isinstance(value, int):
        return value
    match = re.search(r'([12])\)?\s*$', str(value))
    if not match:
        sys.exit(f'bad semester {value!r}')
    return int(match.group(1))


def rename(mapping, values):
    out = OrderedDict()
    for key, value in values.items():
        out[mapping.get(key, key)] = value
    return out


# Wording cleanup for the general SD notes: they named the dropped allocation file
# and the legacy app's internals. The meaning stays; old IDs move to the crosswalk.
TEXT_FIXES = [
    (re.compile(r'\s*--\s*sebelumnya tidak ikut ter-update.*?\(sudah diperbaiki\)\.?'), '.'),
    (re.compile(r'struktur_alokasi_sd(\.json)?'), 'target alokasi semester SD'),
]
RETIRED_IDS = re.compile(r'identitas lamanya dicatat sebagai entri yang dipensiunkan: ([^.]+)\.')


def clean_text(value, code, crosswalk):
    if isinstance(value, dict):
        return OrderedDict((k, clean_text(v, code, crosswalk)) for k, v in value.items())
    if isinstance(value, list):
        return [clean_text(v, code, crosswalk) for v in value]
    if not isinstance(value, str):
        return value
    for pattern, replacement in TEXT_FIXES:
        value = pattern.sub(replacement, value)
    value = value.replace('..', '.')
    match = RETIRED_IDS.search(value)
    if match:
        crosswalk['retired'].append({'merged_into': code, 'old': [i.strip() for i in match.group(1).split(',')]})
        value = RETIRED_IDS.sub('kode lamanya dicatat di crosswalk tim (D-365).', value)
    return value


def curation(status, note):
    return {'status': status, 'since': TODAY, 'reviewed_by': None, 'reviewed_on': None, 'note': note}


def convert_general(crosswalk):
    rows = load(LEGACY_TOPICS)
    allocation = load(LEGACY_ALLOCATION)
    subjects = subject_index()
    groups = OrderedDict()

    for row in rows:
        old = row['ID_Kurikulum']
        suffix = old.split('KM-SD-2026-R2-', 1)[1]
        code = f'{SD}-{suffix}'
        subject = subjects[row['Mata Pelajaran'].casefold()]
        atp = row['Data_ATP']
        goals = row['Data_Tujuan']
        texts, goal_codes = goals['tujuan_pembelajaran'], goals['kode_tp']
        assert len(texts) == len(goal_codes), old

        objectives = []
        tp_map = {}
        for index, (text, old_tp) in enumerate(zip(texts, goal_codes), start=1):
            new_tp = f'{code}-TP{index:02d}'
            tp_map[old_tp] = new_tp
            objectives.append({'code': new_tp, 'order': index, 'text': text})
            crosswalk['learning_objectives'].append({'old': old_tp, 'new': new_tp})

        focus_items = []
        for focus in sorted(row['Data_Fokus'], key=lambda item: item['urutan']):
            new_fm = f"{code}-FM{focus['urutan']:02d}"
            crosswalk['focus_items'].append({'old': focus['kode_fokus'], 'new': new_fm})
            item = {'code': new_fm, 'order': focus['urutan'], 'title': focus['fokus'],
                    'estimated_jp': focus['estimasi_jp'], 'allocation_status': focus.get('status_alokasi') or ''}
            if 'kode_tp' in focus:
                item['objective_codes'] = [tp_map[c] for c in focus['kode_tp']]
            focus_items.append(item)

        weekly = atp.get('distribusi_mingguan') or []
        if weekly and isinstance(weekly[0], dict):
            weekly = [week['jp'] for week in sorted(weekly, key=lambda week: week['minggu'])]

        extras = OrderedDict()
        for source, target in (('Cabang_Seni', 'art_branch'), ('Keterampilan_Proses', 'science_process_skills'),
                               ('Status_Mapel', 'subject_status'), ('Jenis_Pembelajaran', 'learning_type')):
            if row.get(source) not in (None, '', []):
                extras[target] = row[source]
        atp_extra = rename(ATP_EXTRA_KEYS, {k: v for k, v in atp.items() if k not in ATP_COLUMNS})
        if atp_extra:
            extras['atp'] = atp_extra

        topic = OrderedDict([
            ('code', code), ('grade', int(row['Kelas'])), ('phase', row['Fase']), ('semester', semester_of(row['Semester'])),
            ('order_in_semester', atp['urutan_semester']), ('element', row['Elemen']), ('title', row['Materi Pokok']),
            ('cp_text', row['Capaian Pembelajaran']), ('atp_code', atp['kode_atp']), ('jp_total', atp['alokasi_jp']),
            ('jp_per_week', atp['jp_per_minggu']), ('week_start', atp['minggu_mulai']), ('week_end', atp['minggu_selesai']),
            ('weekly_distribution', weekly), ('learning_objectives', objectives), ('focus_items', focus_items),
            ('references', rename(REFERENCE_KEYS, row.get('Referensi') or {})),
            ('curation_notes', rename(CURATION_KEYS, row.get('Audit_Data') or {})),
        ])
        if extras:
            topic['extras'] = extras

        if suffix == D234_TOPIC and topic['jp_total'] == 34 and topic['week_end'] == 17:
            topic['jp_total'], topic['week_end'] = 28, 14
            topic['weekly_distribution'] = topic['weekly_distribution'][:14 - topic['week_start'] + 1]
            for item in topic['focus_items']:
                item['estimated_jp'] = 14
            topic['curation_notes'].setdefault('notes', []).append(
                'D-234: trimmed from 34 JP over weeks 1-17 to 28 JP over weeks 1-14, the effective weeks of grade 6 semester 2.')

        legacy_index = topic['curation_notes'].pop('indeks_sumber_lama', None)
        topic['curation_notes'] = clean_text(topic['curation_notes'], code, crosswalk)
        if 'extras' in topic:
            topic['extras'] = clean_text(topic['extras'], code, crosswalk)
        entry = {'old': old, 'legacy_id': row['Legacy_ID'],
                 'legacy_aliases': [a for a in row['Legacy_ID_Aliases'] if a != old], 'new': code}
        if legacy_index not in (None, []):
            entry['legacy_index'] = legacy_index
        crosswalk['topics'].append(entry)
        groups.setdefault(subject['code'], {'subject': subject, 'topics': []})['topics'].append(topic)

    allocations = {}
    for row in allocation['alokasi']:
        if (row.get('jenis_pembelajaran') or 'Intrakurikuler').lower() != 'intrakurikuler':
            continue
        subject = subjects[row['mata_pelajaran'].casefold()]
        if subject['kind'] == 'cocurricular':
            continue
        allocations.setdefault(subject['code'], []).append(OrderedDict([
            ('grade', int(row['kelas'])), ('phase', row['fase']), ('jp_per_week', row['jp_per_minggu']),
            ('s1_jp', row['semester_1']['jp']), ('s1_effective_weeks', row['semester_1']['minggu_efektif']),
            ('s2_jp', row['semester_2']['jp']), ('s2_effective_weeks', row['semester_2']['minggu_efektif']),
        ]))

    out_subjects = []
    for code, group in groups.items():
        subject = group['subject']
        entry = OrderedDict([('code', code), ('name', subject['name'])])
        if subject.get('religion'):
            entry['religion'] = subject['religion']
        entry['curation'] = curation('converted', 'Converted from the legacy app data; not yet curated (D-364).')
        entry['allocations'] = sorted(allocations.get(code, []), key=lambda a: a['grade'])
        entry['topics'] = group['topics']
        out_subjects.append(entry)

    return OrderedDict([
        ('dataset', 'sigrak_curriculum_topics'),
        ('version_code', SD),
        ('status', 'draft'),
        ('scope', f'{SD}: SIGRAK curriculum for SD, topics (materi pokok/ATP), TP, fokus, and JP'),
        ('part', 'General SD subjects, Pendidikan Agama Islam, muatan lokal, and KKA (kokurikuler content, D-208)'),
        ('source', OrderedDict([
            ('cp', 'CP text per topic as recorded in references.cp_source (Panduan Guru books, guru.kemendikdasmen.go.id, Kepka BKPDM 020/2026 for PAI). To be re-checked against the official CP decrees during curation (D-364, P4-23).'),
            ('allocation', 'Semester JP targets per subject and grade follow the national structure (Permendikdasmen 13/2025): 36 weeks, 32 in grade 6. Weekly distribution is a default until the school calendar computes it (D-366).'),
            ('conversion', f'Converted on {TODAY} from the legacy app curriculum data into this shape (D-363). Old codes are only in crosswalk-legacy-2026.json (D-365).'),
        ])),
        ('authoring', OrderedDict([
            ('method', 'Topics, TP, ATP, fokus, and JP adjusted to the Buku Panduan Guru of each grade, as noted per topic in curation_notes.'),
            ('review', OrderedDict([('reviewer', 'Tim Validasi SIGRAK'), ('status', 'pending'), ('note', 'Each subject carries its own curation status; only curated subjects are active for teachers (D-364).')])),
        ])),
        ('conventions', [
            'Shape follows the data model tables topics, learning_objectives (TP), focus_items, and focus_item_objectives (docs/final/data-model.md#curriculum), D-140.',
            f'Codes: topic {SD}-<subject>-K<grade>-<nn>; TP <topic>-TP<mm>; fokus <topic>-FM<mm>; ATP ATP-<subject>-K<grade>-BAB-<nn>. Seni dan Budaya topics keep their branch code (SM, SR, MUS, SMU, SRP).',
            'curation.status per subject: converted (not yet reviewed), in_review, curated (D-364).',
        ]),
        ('subjects', out_subjects),
    ])


def recode_religion(path, version, crosswalk, part):
    data = load(path)
    old_version = data['version_code']
    for subject in data['subjects']:
        subject['curation'] = curation('in_review', 'SIGRAK draft from the verified CP, waiting for Tim Validasi review (D-113, D-364).')
        for topic in subject['topics']:
            old = topic['code']
            code = version + old[len(old_version):]
            topic['code'] = code
            crosswalk['topics'].append({'old': old, 'legacy_id': None, 'legacy_aliases': [], 'new': code})
            tp_map = {}
            for objective in topic['learning_objectives']:
                new_tp = f"{code}-TP{objective['order']:02d}"
                tp_map[objective['code']] = new_tp
                crosswalk['learning_objectives'].append({'old': objective['code'], 'new': new_tp})
                objective['code'] = new_tp
            for item in topic['focus_items']:
                new_fm = f"{code}-FM{item['order']:02d}"
                crosswalk['focus_items'].append({'old': item['code'], 'new': new_fm})
                item['code'] = new_fm
                if item.get('objective_codes'):
                    item['objective_codes'] = [tp_map[c] for c in item['objective_codes']]
        # keep key order: curation before allocations and topics
        ordered = OrderedDict((k, v) for k, v in subject.items() if k not in ('curation', 'allocations', 'topics'))
        ordered['curation'] = subject['curation']
        ordered['allocations'] = subject['allocations']
        ordered['topics'] = subject['topics']
        subject.clear()
        subject.update(ordered)

    text = json.dumps(data, ensure_ascii=False)
    data = json.loads(text.replace(old_version, version).replace('KM-SD-2026-R2', 'SIGRAK-SD-2026-R1'))
    data['dataset'] = 'sigrak_curriculum_topics'
    data['part'] = part
    data['scope'] = f"{version}: SIGRAK curriculum for {'SD' if version == SD else 'MI'}, topics (materi pokok/ATP), TP, fokus, and JP"
    data['conventions'] = [c for c in data['conventions'] if 'field names map' not in c and 'pattern' not in c and not c.startswith('Codes')]
    data['conventions'].insert(0, f'Codes: topic {version}-<subject>-K<grade>-<nn>; TP <topic>-TP<mm>; fokus <topic>-FM<mm>; ATP ATP-<subject>-K<grade>-BAB-<nn> (D-363).')
    return OrderedDict([(k, data[k]) for k in ('dataset', 'version_code', 'status', 'scope', 'part') if k in data] +
                       [(k, v) for k, v in data.items() if k not in ('dataset', 'version_code', 'status', 'scope', 'part', 'subjects')] +
                       [('subjects', data['subjects'])])


def counts(files):
    c = Counter()
    for data in files:
        for subject in data['subjects']:
            for topic in subject['topics']:
                c['topics'] += 1
                c['tp'] += len(topic['learning_objectives'])
                c['focus'] += len(topic['focus_items'])
                c['jp'] += topic['jp_total']
    return c


def main():
    legacy = load(LEGACY_TOPICS)
    before = Counter(topics=len(legacy), tp=sum(len(r['Data_Tujuan']['kode_tp']) for r in legacy),
                     focus=sum(len(r['Data_Fokus']) for r in legacy), jp=sum(r['Data_ATP']['alokasi_jp'] for r in legacy))
    religion_before = counts([load(RELIGION_SD), load(RELIGION_MI)])

    crosswalk = OrderedDict([
        ('dataset', 'curriculum_crosswalk'),
        ('purpose', 'Team-only map from legacy and earlier draft codes to the SIGRAK R1 codes (D-365). Never imported.'),
        ('created_on', TODAY),
        ('topics', []), ('learning_objectives', []), ('focus_items', []), ('retired', []),
    ])
    general = convert_general(crosswalk)
    religion_sd = recode_religion(RELIGION_SD, SD, crosswalk, 'Pendidikan Agama Kristen, Katolik, Hindu, Buddha, and Khonghucu dan Budi Pekerti')
    religion_mi = recode_religion(RELIGION_MI, MI, crosswalk, "Al-Qur'an Hadis, Akidah Akhlak, Fikih, SKI, and Bahasa Arab")

    after_general = counts([general])
    after_religion = counts([religion_sd, religion_mi])
    print('general  before', dict(before), 'after', dict(after_general), '(jp differs by 6 for D-234)')
    print('religion before', dict(religion_before), 'after', dict(after_religion))
    assert after_general['topics'] == before['topics'] and after_general['tp'] == before['tp'] and after_general['focus'] == before['focus']
    assert after_general['jp'] == before['jp'] - 6
    assert after_religion == religion_before

    codes = [t['new'] for t in crosswalk['topics']] + [t['new'] for t in crosswalk['learning_objectives']] + [t['new'] for t in crosswalk['focus_items']]
    assert len(codes) == len(set(codes)), 'duplicate new codes'

    dump(ROOT + 'sigrak-sd-2026-r1-umum.json', general)
    dump(ROOT + 'sigrak-sd-2026-r1-agama.json', religion_sd)
    dump(ROOT + 'sigrak-mi-2026-r1.json', religion_mi)
    dump(ROOT + 'crosswalk-legacy-2026.json', crosswalk)


if __name__ == '__main__':
    main()
