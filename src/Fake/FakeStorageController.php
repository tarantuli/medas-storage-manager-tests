<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\Core\Interfaces\Serializer;
use Medas\StorageManager\{
    Interfaces\ActionBuilders,
    Interfaces\ActionExecutor,
    Interfaces\RecordFetchers,
    Interfaces\Storage,
    Interfaces\StorageController,
    Interfaces\Store,
    Interfaces\Transaction,
    Shared\ValueSerializer
};

readonly class FakeStorageController implements StorageController
{
    private FakeStorage $storage;
    private FakeTransaction $transaction;

    public function __construct(
        private InMemoryDatabase   $db,
        private FakeActionBuilders $actionBuilders,
        private FakeActionExecutor $actionExecutor,
        private FakeRecordFetchers $recordFetchers,
        private ValueSerializer    $valueSerializer,
        string                     $storageName = 'default',
    )
    {
        $this->storage = new FakeStorage($storageName);
        $this->transaction = new FakeTransaction();
    }

    public function storage(): FakeStorage
    {
        return $this->storage;
    }

    public function handles(Storage $storage): bool
    {
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        return $storage instanceof FakeStorage && $storage->name() === $this->storage->name();
    }

    public function serializer(Storage|null $storage = null): Serializer
    {
        return $this->valueSerializer;
    }

    public function transaction(Storage|null $storage = null): Transaction
    {
        return $this->transaction;
    }

    public function lastGeneratedValue(Storage|null $storage = null): int|null
    {
        return $this->db->lastInsertId;
    }

    public function store(string $name, Storage|null $storage = null): Store
    {
        return new FakeStore($name, $this->storage);
    }

    public function deleteStore(Store $store): void
    {
        $this->db->removeStore($store->name());
    }

    /** @return Store[] */
    public function getStores(Storage|null $storage = null, string|null $nameFilter = null): array
    {
        $names = $this->db->storeNames();

        if ($nameFilter !== null) {
            $names = array_filter($names, fn(string $name) => $name === $nameFilter);
        }

        return array_map(fn(string $name) => new FakeStore($name, $this->storage), $names);
    }

    public function hasStore(Store $store, Storage|null $storage = null): bool
    {
        return $this->db->hasStore($store->name());
    }

    public function actionBuilders(): ActionBuilders
    {
        return $this->actionBuilders;
    }

    public function actionExecutor(): ActionExecutor
    {
        return $this->actionExecutor;
    }

    public function recordFetchers(): RecordFetchers
    {
        return $this->recordFetchers;
    }
}
