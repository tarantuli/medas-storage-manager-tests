<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\Interfaces\{FieldMetaData, RecordSetMetaData};

class FakeRecordSetMetaData implements RecordSetMetaData
{
    private array $fieldNames;

    public function __construct(array $records)
    {
        $this->fieldNames = $records ? array_keys(reset($records)) : [];
    }

    /** @return FieldMetaData[] */
    public function fields(): array
    {
        return [];
    }

    /** @return string[] */
    public function fieldNames(): array
    {
        return $this->fieldNames;
    }

    /** @return string[] */
    public function primaryKeyFieldNames(): array
    {
        return ['id'];
    }

    public function rowCount(): int
    {
        return 0;
    }
}
