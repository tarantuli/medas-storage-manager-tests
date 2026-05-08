<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\Interfaces\{Fetchers, RecordFetchers};

readonly class FakeRecordFetchers implements RecordFetchers
{
    public function __construct(
        private FakeFilteredFetcher         $filteredFetcher,
        private FakeCollectionRecordFetcher $collectionRecordFetcher,
    )
    {
    }

    public function filteredFetcher(): Fetchers\FilteredFetcher
    {
        return $this->filteredFetcher;
    }

    public function collectionRecordFetcher(): Fetchers\CollectionRecordFetcher
    {
        return $this->collectionRecordFetcher;
    }
}
