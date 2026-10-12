<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Unit;

use Medas\EntityManager\EntityManager;
use Medas\StorageManager\StorageManager;
use Medas\StorageManagerTests\Fake\{FakeStorage, FakeStorageController, InMemoryDatabase};
use PHPUnit\Framework\TestCase;

abstract class BaseHappyPathTests extends TestCase
{
    /**
     * Override to add stores to $this->db before each test.
     * Example: $this->db->addStore('stored_entities');
     */
    abstract protected function setUpStores(): void;

    // Both are services, so every test class gets the same instances: the ones the
    // storage manager writes through.
    protected InMemoryDatabase $db;
    protected FakeStorageController $controller;
    protected EntityManager $entityManager;

    protected function setUp(): void
    {
        // The storage manager finds FakeStorageController by itself, as it would a
        // driver's controller. Adding the storage again is harmless and keeps it the default.
        service(StorageManager::class)->add(new FakeStorage('default'), isDefault: true);

        $this->db = service(InMemoryDatabase::class);
        $this->controller = service(FakeStorageController::class);

        $this->db->reset();

        $this->entityManager = service(EntityManager::class);

        $this->entityManager->autoPersistOnCreate(true, false);
        $this->entityManager->clear();
        $this->setUpStores();
    }
}
