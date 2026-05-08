<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\Entities\Record;
use Medas\StorageManager\Interfaces\{
    Fetchers\FilteredFetcher,
    Record as RecordInterface,
    RecordSet,
    Store
};

readonly class FakeFilteredFetcher implements FilteredFetcher
{
    public function __construct(
        private InMemoryDatabase $db,
    )
    {
    }

    public function fetch(Store $store, array $filters = []): RecordSet
    {
        return new FakeRecordSet($this->db->find($store->name(), $filters));
    }

    public function fetchOne(Store $store, array $filters = []): RecordInterface|null
    {
        $record = $this->db->findOne($store->name(), $filters);

        return $record !== null ? new Record($record) : null;
    }
}
