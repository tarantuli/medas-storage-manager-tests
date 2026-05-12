<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

class InMemoryDatabase
{
    /** @var array<string, array<int, array<string, mixed>>> */
    public array $stores = [];

    /** @var array<string, int> */
    private array $sequences = [];

    public int|null $lastInsertId = null;

    public function addStore(string $name): void
    {
        if (!array_key_exists($name, $this->stores)) {
            $this->stores[$name] = [];
            $this->sequences[$name] = 0;
        }
    }

    public function removeStore(string $name): void
    {
        unset($this->stores[$name], $this->sequences[$name]);
    }

    public function hasStore(string $name): bool
    {
        return array_key_exists($name, $this->stores);
    }

    /** @return string[] */
    public function storeNames(): array
    {
        return array_keys($this->stores);
    }

    public function insert(string $storeName, array $values): int
    {
        // If the values already contain an id (e.g. resolved from a
        // LastInsertIdPlaceholder for a dependent store), use that id instead
        // of auto-generating a new one.
        if (array_key_exists('id', $values) && $values['id'] !== null) {
            $id = (int) $values['id'];
        }
        else {
            $id = ++$this->sequences[$storeName];
        }

        $this->stores[$storeName][$id] = array_merge($values, ['id' => $id]);
        $this->lastInsertId = $id;

        return $id;
    }

    public function update(string $storeName, array $values, array $conditions): void
    {
        foreach ($this->stores[$storeName] as $id => $record) {
            if ($this->matches($record, $conditions)) {
                $this->stores[$storeName][$id] = array_merge($record, $values);
            }
        }
    }

    public function delete(string $storeName, array $conditions): void
    {
        foreach ($this->stores[$storeName] as $id => $record) {
            if ($this->matches($record, $conditions)) {
                unset($this->stores[$storeName][$id]);
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function find(string $storeName, array $conditions = []): array
    {
        if (!array_key_exists($storeName, $this->stores)) {
            return [];
        }

        if (!$conditions) {
            return array_values($this->stores[$storeName]);
        }

        return array_values(array_filter(
            $this->stores[$storeName],
            fn(array $record) => $this->matches($record, $conditions)
        ));
    }

    private function matches(array $record, array $conditions): bool
    {
        return array_all(
            $conditions,
            fn($value, $field) => array_key_exists($field, $record) && $record[$field] == $value
        );
    }

    public function findOne(string $storeName, array $conditions = []): array|null
    {
        $results = $this->find($storeName, $conditions);

        return $results[0] ?? null;
    }

    public function reset(): void
    {
        foreach ($this->stores as $name => $_) {
            $this->stores[$name] = [];
            $this->sequences[$name] = 0;
        }

        $this->lastInsertId = null;
    }
}
