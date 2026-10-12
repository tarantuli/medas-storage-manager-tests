<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\Core\{Attributes\Service, Interfaces\Serializer};
use Medas\EntityManager\MetaDataManager;
use Medas\StorageManager\{
    Interfaces\ActionBuilders,
    Interfaces\ActionExecutor,
    Interfaces\RecordFetchers,
    Interfaces\Storage,
    Interfaces\StorageController,
    Interfaces\Store,
    Interfaces\Transaction,
    Shared\ValueSerializer,
    StorageManager
};

// A service, so the storage manager finds it the way it finds a real driver's controller.
// It handles the FakeStorage named "default" and keeps its rows in the shared InMemoryDatabase.
#[Service]
readonly class FakeStorageController implements StorageController
{
    private FakeStorage $storage;
    private FakeTransaction $transaction;
    private FakeActionBuilders $actionBuilders;
    private FakeActionExecutor $actionExecutor;
    private FakeRecordFetchers $recordFetchers;

    public function __construct(
        private InMemoryDatabase $db,
        private ValueSerializer  $valueSerializer,
        MetaDataManager          $metaDataManager,
        StorageManager           $storageManager,
    )
    {
        $this->storage = new FakeStorage('default');
        $this->transaction = new FakeTransaction();

        // The other fakes are built here rather than made services, so a driver's test
        // suite that registers this package does not find a second implementation of
        // the storage interfaces.
        $this->actionBuilders = new FakeActionBuilders(
            new Builders\FakeCollectionUpdateBuilder(),
            new Builders\FakeDeleteBuilder(),
            new Builders\FakeGetBuilder(),
            new Builders\FakeInsertBuilder(),
            new Builders\FakeSelectorActionBuilder($metaDataManager, $storageManager),
            new Builders\FakeUpdateBuilder(),
        );

        $this->actionExecutor = new FakeActionExecutor($db);

        $this->recordFetchers = new FakeRecordFetchers(
            new FakeFilteredFetcher($db),
            new FakeCollectionRecordFetcher($db),
        );
    }

    public function storage(): FakeStorage
    {
        return $this->storage;
    }

    public function initialize(Storage $storage): void
    {
        // Do nothing
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
