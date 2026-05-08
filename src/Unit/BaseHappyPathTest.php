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

abstract class BaseHappyPathTest extends TestCase
{
    /**
     * Override to add stores to $this->db before each test.
     * Example: $this->db->addStore('stored_entities');
     */
    abstract protected function setUpStores(): void;

    protected InMemoryDatabase $db;
    protected FakeStorageController $controller;
    protected EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->db = new InMemoryDatabase();
        $db = $this->db;
        $storageManager = service(StorageManager::class);
        $metaDataManager = service(MetaDataManager::class);
        $valueSerializer = service(ValueSerializer::class);
        $filteredFetcher = new FakeFilteredFetcher($db);
        $collectionFetcher = new FakeCollectionRecordFetcher($db);
        $recordFetchers = new FakeRecordFetchers($filteredFetcher, $collectionFetcher);

        $actionBuilders = new FakeActionBuilders(
            new FakeCollectionUpdateBuilder(),
            new FakeDeleteBuilder(),
            new FakeGetBuilder(),
            new FakeInsertBuilder(),
            new FakeSelectorActionBuilder($metaDataManager, $storageManager),
            new FakeUpdateBuilder(),
        );

        $executor = new FakeActionExecutor($db);

        $this->controller = new FakeStorageController(
            $db,
            $actionBuilders,
            $executor,
            $recordFetchers,
            $valueSerializer,
        );

        $storageManager->registerController($this->controller);
        $storageManager->add(new FakeStorage('default'));

        $this->entityManager = service(EntityManager::class);

        $this->setUpStores();
    }
}
