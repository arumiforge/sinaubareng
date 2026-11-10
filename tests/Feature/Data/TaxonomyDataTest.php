<?php

declare(strict_types=1);

// Structural checks for the P4-22 taxonomy seed and regulation registry (docs/data/taxonomy/, D-357, D-354).
// Content is reviewed by Tim Validasi (D-113); this test only keeps codes, links, and vocabularies consistent.

const TAXONOMY_DIR = 'docs/data/taxonomy';
const TAXONOMY_VERSION = 'TAX-2026-R1';
const TAXONOMY_CODE = '/^[A-Z0-9]+(?:[.-][A-Z0-9]+)*$/';

/**
 * @return array<string, mixed>
 */
function taxonomyJson(string $relative): array
{
    $path = base_path($relative);
    expect(is_file($path))->toBeTrue("Missing {$relative}");

    $data = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
    expect($data)->toBeArray();

    return $data;
}

/**
 * @return array<string, string> facet letter => file name
 */
function taxonomyFacetFiles(): array
{
    return [
        'A' => 'facet-a-policy.json',
        'B' => 'facet-b-curriculum-structure.json',
        'C' => 'facet-c-profile-character.json',
        'D' => 'facet-d-learning.json',
        'E' => 'facet-e-assessment.json',
        'F' => 'facet-f-content.json',
        'G' => 'facet-g-context.json',
        'H' => 'facet-h-administration.json',
    ];
}

/**
 * @return array<string, array<string, mixed>> code => node
 */
function taxonomyNodes(): array
{
    $nodes = [];

    foreach (taxonomyFacetFiles() as $letter => $file) {
        $data = taxonomyJson(TAXONOMY_DIR.'/'.$file);
        expect($data['meta']['facet'] ?? null)->toBe($letter, $file);

        foreach ($data['nodes'] as $node) {
            $node['_file'] = $file;
            $node['_file_facet'] = $letter;
            $nodes[$node['code']] = isset($nodes[$node['code']]) ? ['_duplicate' => true] + $node : $node;
        }
    }

    return $nodes;
}

/**
 * Resolve a small path language: "a[]" iterates a list, "a[key=value]" iterates a filtered list, "a" reads a key.
 *
 * @param  array<mixed>  $roots
 * @return array<mixed>
 */
function taxonomyResolve(array $roots, string $path): array
{
    $current = $roots;

    foreach (explode('.', $path) as $segment) {
        if (! preg_match('/^([a-z0-9_]+)(?:\[(?:([a-z0-9_]+)=([^\]]+))?\])?$/', $segment, $m)) {
            return [];
        }

        $iterate = str_contains($segment, '[');
        $next = [];

        foreach ($current as $item) {
            $value = is_array($item) ? ($item[$m[1]] ?? null) : null;

            if (! $iterate) {
                if ($value !== null) {
                    $next[] = $value;
                }

                continue;
            }

            foreach (is_array($value) ? $value : [] as $child) {
                if (isset($m[2]) && $m[2] !== '' && (! is_array($child) || ($child[$m[2]] ?? null) !== $m[3])) {
                    continue;
                }
                $next[] = $child;
            }
        }

        $current = $next;
    }

    return $current;
}

it('has the meta block on every taxonomy file', function (): void {
    foreach ([...array_values(taxonomyFacetFiles()), 'regulations.json'] as $file) {
        $meta = taxonomyJson(TAXONOMY_DIR.'/'.$file)['meta'];

        expect($meta['version'])->toBe(TAXONOMY_VERSION, $file)
            ->and($meta['status'])->toBe('draft', $file)
            ->and($meta['review']['reviewer'] ?? null)->toBe('Tim Validasi SIGRAK', $file);
    }
});

it('keeps taxonomy nodes unique, linked, and in the closed vocabularies', function (): void {
    $nodes = taxonomyNodes();
    $registry = taxonomyJson(TAXONOMY_DIR.'/regulations.json');
    $regulationCodes = array_column($registry['regulations'], 'code');
    $forces = [];

    foreach ($nodes as $node) {
        if (($node['parent'] ?? null) === 'FORCE') {
            $forces[] = $node['key'];
        }
    }

    $required = ['code', 'facet', 'label_id', 'parent', 'level_scope', 'force', 'source_ref', 'status', 'since', 'retired_in', 'crosswalk'];
    $errors = [];

    foreach ($nodes as $code => $node) {
        $where = "{$node['_file']} {$code}";

        if (isset($node['_duplicate'])) {
            $errors[] = "{$where}: duplicate code";
        }
        foreach ($required as $key) {
            if (! array_key_exists($key, $node)) {
                $errors[] = "{$where}: missing {$key}";
            }
        }
        if (! preg_match(TAXONOMY_CODE, (string) $code)) {
            $errors[] = "{$where}: bad code";
        }
        if ($node['facet'] !== $node['_file_facet']) {
            $errors[] = "{$where}: facet {$node['facet']} in the wrong file";
        }
        if (! is_string($node['label_id']) || trim($node['label_id']) === '') {
            $errors[] = "{$where}: empty label_id";
        }
        if (! in_array($node['level_scope'], ['sd', 'mi', 'both'], true)) {
            $errors[] = "{$where}: bad level_scope";
        }
        if (! in_array($node['force'], $forces, true)) {
            $errors[] = "{$where}: force {$node['force']} is not a FORCE key";
        }
        if ($node['status'] !== 'draft' || $node['since'] !== TAXONOMY_VERSION) {
            $errors[] = "{$where}: status must be draft and since ".TAXONOMY_VERSION;
        }
        if ($node['retired_in'] !== null && ! preg_match('/^TAX-\d{4}-R\d+$/', (string) $node['retired_in'])) {
            $errors[] = "{$where}: bad retired_in";
        }

        // Parent exists in the same facet, and the chain has no loop.
        $seen = [$code => true];
        $parent = $node['parent'];
        while ($parent !== null) {
            if (! isset($nodes[$parent])) {
                $errors[] = "{$where}: parent {$parent} not found";
                break;
            }
            if ($nodes[$parent]['facet'] !== $node['facet']) {
                $errors[] = "{$where}: parent {$parent} is in another facet";
            }
            if (isset($seen[$parent])) {
                $errors[] = "{$where}: parent loop";
                break;
            }
            $seen[$parent] = true;
            $parent = $nodes[$parent]['parent'];
        }

        if ($node['parent'] === null && $node['retired_in'] === null && ! in_array($node['vocabulary'] ?? null, ['closed', 'open'], true)) {
            $errors[] = "{$where}: top-level node needs vocabulary closed or open";
        }

        foreach ($node['crosswalk'] as $link) {
            $from = $nodes[$link['from'] ?? ''] ?? null;
            if ($from === null || $from['retired_in'] === null) {
                $errors[] = "{$where}: crosswalk from {$link['from']} must be a retired node";
            }
            if (! in_array($link['type'] ?? null, ['renamed', 'approximate', 'replaced', 'merged', 'split'], true)) {
                $errors[] = "{$where}: bad crosswalk type";
            }
        }

        $ref = $node['source_ref'];
        if (! is_array($ref)) {
            $errors[] = "{$where}: missing source_ref";
        } elseif (array_key_exists('regulation', $ref)) {
            foreach ([$ref['regulation'], ...array_column($ref['also'] ?? [], 'regulation')] as $regulation) {
                if (! in_array($regulation, $regulationCodes, true)) {
                    $errors[] = "{$where}: regulation {$regulation} not in regulations.json";
                }
            }
        } elseif (($ref['source'] ?? null) !== 'sigrak') {
            $errors[] = "{$where}: source_ref needs a regulation or source sigrak";
        }

        foreach (['maps_to', 'links_to'] as $key) {
            if (isset($node[$key]) && ! isset($nodes[$node[$key]])) {
                $errors[] = "{$where}: {$key} {$node[$key]} not found";
            }
        }

        foreach ($node['data_files'] ?? [] as $file) {
            if (! is_file(base_path($file))) {
                $errors[] = "{$where}: data file {$file} not found";
            }
        }
    }

    expect($errors)->toBe([]);
});

it('resolves every catalog and curriculum link without copying its items', function (): void {
    $errors = [];

    foreach (taxonomyNodes() as $code => $node) {
        if (! isset($node['members_from'])) {
            continue;
        }

        $link = $node['members_from'];
        $roots = [];
        foreach ($link['files'] as $file) {
            if (! is_file(base_path($file))) {
                $errors[] = "{$code}: {$file} not found";

                continue;
            }
            $roots[] = json_decode((string) file_get_contents(base_path($file)), true, flags: JSON_THROW_ON_ERROR);
        }

        $members = taxonomyResolve($roots, $link['path']);
        $codes = array_filter(array_map(fn ($m) => is_array($m) ? ($m[$link['code_field']] ?? null) : null, $members));

        if ($members === [] || count($codes) !== count($members)) {
            $errors[] = "{$code}: {$link['path']} gives no members or members without {$link['code_field']}";
        }
        if (count($codes) !== count(array_unique($codes))) {
            $errors[] = "{$code}: duplicate member codes";
        }
    }

    expect($errors)->toBe([]);
});

it('keeps the regulation registry consistent', function (): void {
    $registry = taxonomyJson(TAXONOMY_DIR.'/regulations.json');
    $kinds = [];
    foreach (taxonomyNodes() as $node) {
        if (($node['parent'] ?? null) === 'REGKIND') {
            $kinds[] = $node['key'];
        }
    }

    $errors = [];
    $codes = [];

    foreach ($registry['regulations'] as $regulation) {
        $code = $regulation['code'];
        $codes[] = $code;

        if (! preg_match(TAXONOMY_CODE, $code)) {
            $errors[] = "{$code}: bad code";
        }
        if (! in_array($regulation['kind'], $kinds, true)) {
            $errors[] = "{$code}: kind {$regulation['kind']} is not a REGKIND key";
        }
        if (! in_array($regulation['ministry'], ['kemendikdasmen', 'kemenag', 'other'], true)) {
            $errors[] = "{$code}: bad ministry";
        }
        if (! in_array($regulation['status'], ['draft', 'in_force', 'amended', 'revoked'], true)) {
            $errors[] = "{$code}: bad status";
        }
        if (! in_array($regulation['verification'], ['official_copy', 'mirror', 'secondary', 'unverified'], true)) {
            $errors[] = "{$code}: bad verification";
        }
        if (! in_array($regulation['layer'], ['L0', 'L1', 'L2', 'L3'], true)) {
            $errors[] = "{$code}: bad layer";
        }
        foreach (['issued_on', 'promulgated_on', 'effective_on'] as $date) {
            if ($regulation[$date] !== null && ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $regulation[$date])) {
                $errors[] = "{$code}: bad {$date}";
            }
        }

        if ($regulation['source_file'] !== null) {
            $path = base_path($regulation['source_file']);
            if (! is_file($path)) {
                $errors[] = "{$code}: {$regulation['source_file']} not found";
            } elseif (hash_file('sha256', $path) !== $regulation['sha256']) {
                $errors[] = "{$code}: sha256 does not match {$regulation['source_file']}";
            }
        } elseif ($regulation['sha256'] !== null) {
            $errors[] = "{$code}: sha256 without source_file";
        }

        foreach ($regulation['data_files'] as $file) {
            if (! is_file(base_path($file))) {
                $errors[] = "{$code}: data file {$file} not found";
            }
        }
    }

    if (count($codes) !== count(array_unique($codes))) {
        $errors[] = 'duplicate regulation codes';
    }

    foreach ($registry['regulation_relations'] as $relation) {
        $label = "{$relation['from']} {$relation['type']} {$relation['to']}";
        if (! in_array($relation['from'], $codes, true) || ! in_array($relation['to'], $codes, true) || $relation['from'] === $relation['to']) {
            $errors[] = "{$label}: unknown or self relation";
        }
        if (! in_array($relation['type'], ['amends', 'revokes', 'implements', 'references'], true)) {
            $errors[] = "{$label}: bad type";
        }
    }

    expect($errors)->toBe([]);
});
