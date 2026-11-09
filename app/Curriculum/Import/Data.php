<?php

declare(strict_types=1);

namespace App\Curriculum\Import;

/**
 * Typed reads from a decoded JSON object. A missing or wrongly typed field makes the
 * whole file invalid, with the field's path in the message.
 */
final readonly class Data
{
    /**
     * @param  array<array-key, mixed>  $values
     */
    public function __construct(private array $values, public string $path = '') {}

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values) && $this->values[$key] !== null;
    }

    public function raw(string $key): mixed
    {
        return $this->values[$key] ?? null;
    }

    public function string(string $key): string
    {
        $value = $this->raw($key);

        if (! is_string($value) || trim($value) === '') {
            throw $this->invalid($key, 'a non-empty string');
        }

        return trim($value);
    }

    public function nullableString(string $key): ?string
    {
        $value = $this->raw($key);

        if ($value === null || (is_string($value) && trim($value) === '')) {
            return null;
        }

        return $this->string($key);
    }

    public function int(string $key): int
    {
        $value = $this->raw($key);

        if (is_float($value) && floor($value) === $value) {
            $value = (int) $value;
        }

        if (is_string($value) && preg_match('/^\d+$/', $value) === 1) {
            $value = (int) $value;
        }

        if (! is_int($value) || $value < 0) {
            throw $this->invalid($key, 'a whole number of 0 or more');
        }

        return $value;
    }

    public function number(string $key): float
    {
        $value = $this->raw($key);

        if (! is_int($value) && ! is_float($value)) {
            throw $this->invalid($key, 'a number');
        }

        return (float) $value;
    }

    /**
     * An object, or a JSON string holding one (the legacy app stores some objects as strings).
     */
    public function object(string $key): self
    {
        $value = $this->decoded($key);

        if (! is_array($value) || ($value !== [] && array_is_list($value))) {
            throw $this->invalid($key, 'an object');
        }

        return new self($value, $this->pathTo($key));
    }

    public function nullableObject(string $key): ?self
    {
        return $this->has($key) ? $this->object($key) : null;
    }

    /**
     * A list of objects, or a JSON string holding one.
     *
     * @return list<self>
     */
    public function objects(string $key): array
    {
        $items = [];

        foreach ($this->list($key) as $index => $item) {
            if (! is_array($item) || ($item !== [] && array_is_list($item))) {
                throw new InvalidCurriculumFile("{$this->pathTo($key)}[{$index}] must be an object.");
            }

            $items[] = new self($item, "{$this->pathTo($key)}[{$index}]");
        }

        return $items;
    }

    /**
     * @return list<mixed>
     */
    public function list(string $key): array
    {
        $value = $this->decoded($key);

        if (! is_array($value) || ! array_is_list($value)) {
            throw $this->invalid($key, 'a list');
        }

        return $value;
    }

    /**
     * @return list<string>
     */
    public function strings(string $key): array
    {
        return array_map(function (mixed $item) use ($key): string {
            if (! is_string($item) && ! is_int($item)) {
                throw $this->invalid($key, 'a list of strings');
            }

            return trim((string) $item);
        }, $this->list($key));
    }

    /**
     * @return list<int>
     */
    public function ints(string $key): array
    {
        return array_map(function (mixed $item) use ($key): int {
            if (is_float($item) && floor($item) === $item) {
                $item = (int) $item;
            }

            if (! is_int($item) || $item < 0) {
                throw $this->invalid($key, 'a list of whole numbers');
            }

            return $item;
        }, $this->list($key));
    }

    /**
     * @param  list<string>  $keys
     * @return array<string, mixed>
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->values, array_flip($keys));
    }

    /**
     * @return array<array-key, mixed>
     */
    public function toArray(): array
    {
        return $this->values;
    }

    public function pathTo(string $key): string
    {
        return $this->path === '' ? $key : "{$this->path}.{$key}";
    }

    private function decoded(string $key): mixed
    {
        $value = $this->raw($key);

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw $this->invalid($key, 'valid JSON');
            }

            return $decoded;
        }

        return $value;
    }

    private function invalid(string $key, string $expected): InvalidCurriculumFile
    {
        return new InvalidCurriculumFile("{$this->pathTo($key)} must be {$expected}.");
    }
}
