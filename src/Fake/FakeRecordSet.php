<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\{Entities\Record, Interfaces\RecordSet, Interfaces\RecordSetMetaData};

class FakeRecordSet implements RecordSet
{
    private int $index = 0;

    /** @param array<int, array<string, mixed>> $records */
    public function __construct(
        private readonly array $records,
    )
    {
    }

    public function fetchRecord(): Record|null
    {
        if (!array_key_exists($this->index, $this->records)) {
            return null;
        }

        return new Record($this->records[$this->index++]);
    }

    public function fetchRecords(): array
    {
        return array_map(fn(array $data) => new Record($data), $this->records);
    }

    public function hasRecords(): bool
    {
        return count($this->records) > 0;
    }

    public function fetchMetaData(): RecordSetMetaData
    {
        return new FakeRecordSetMetaData($this->records);
    }
}
