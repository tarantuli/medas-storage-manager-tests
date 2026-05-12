<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Unit;

use Medas\EntityManager\{EntityManager, MetaDataManager};
use Medas\StorageManager\{Shared\ValueSerializer, StorageManager};
use Medas\StorageManagerTests\Fake\{
    Builders\FakeCollectionUpdateBuilder,
    Builders\FakeDeleteBuilder,
    Builders\FakeGetBuilder,
    Builders\FakeInsertBuilder,
    Builders\FakeSelectorActionBuilder,
    Builders\FakeUpdateBuilder,
    FakeActionBuilders,
    FakeActionExecutor,
    FakeCollectionRecordFetcher,
    FakeFilteredFetcher,
    FakeRecordFetchers,
    FakeStorage,
    FakeStorageController,
    InMemoryDatabase
};
use PHPUnit\Framework\TestCase;

abstract class BaseHappyPathTests extends TestCase
{
    /**
     * Override to add stores to $this->db before each test.
     * Example: $this->db->addStore('stored_entities');
     */
    abstract protected function setUpStores(): void;

    // Shared across all subclasses — declared on the base class and accessed via
    // BaseHappyPathTests:: to avoid PHP's per-subclass static scoping, which would
    // cause each test class to initialise its own copy and break the StorageManager
    // controller cache (keyed by storage name, never invalidated).
    protected static InMemoryDatabase|null $sharedDb = null;
    protected static FakeStorageController|null $sharedController = null;
    protected InMemoryDatabase $db;
    protected FakeStorageController $controller;
    protected EntityManager $entityManager;

    protected function setUp(): void
    {
        if (BaseHappyPathTests::$sharedDb === null) {
            $this->initializeSharedInfrastructure();
        }

        $this->db = BaseHappyPathTests::$sharedDb;
        $this->controller = BaseHappyPathTests::$sharedController;

        $this->db->reset();

        $this->entityManager = service(EntityManager::class);

        $this->entityManager->autoPersistOnCreate(true, false);
        $this->entityManager->clear();
        $this->setUpStores();
    }

    private function initializeSharedInfrastructure(): void
    {
        BaseHappyPathTests::$sharedDb = new InMemoryDatabase();
        $storageManager = service(StorageManager::class);
        $metaDataManager = service(MetaDataManager::class);
        $valueSerializer = service(ValueSerializer::class);
        $filteredFetcher = new FakeFilteredFetcher(BaseHappyPathTests::$sharedDb);
        $collectionFetcher = new FakeCollectionRecordFetcher(BaseHappyPathTests::$sharedDb);
        $recordFetchers = new FakeRecordFetchers($filteredFetcher, $collectionFetcher);

        $actionBuilders = new FakeActionBuilders(
            new FakeCollectionUpdateBuilder(),
            new FakeDeleteBuilder(),
            new FakeGetBuilder(),
            new FakeInsertBuilder(),
            new FakeSelectorActionBuilder($metaDataManager, $storageManager),
            new FakeUpdateBuilder(),
        );

        $executor = new FakeActionExecutor(BaseHappyPathTests::$sharedDb);

        BaseHappyPathTests::$sharedController = new FakeStorageController(
            BaseHappyPathTests::$sharedDb,
            $actionBuilders,
            $executor,
            $recordFetchers,
            $valueSerializer,
        );

        $storageManager->registerController(BaseHappyPathTests::$sharedController);
        $storageManager->add(new FakeStorage('default'));
    }
}
