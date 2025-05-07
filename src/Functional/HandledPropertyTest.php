<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\StorageManagerTests\Entities\PropertyHandlers\{EntityWithHandler, PropertyClass};
use Medas\StorageManagerTests\TestStorage;

trait HandledPropertyTest
{
    use TestStorage;

    public function testStoreHandledProperty(): void
    {
        em()->autoPersistOnCreate();

        $this->controller()->deleteStore($this->store('entities_with_handler'));

        // Ensure storage existence
        $migration = $this->createMigrationClassContent('PropertyHandlers');

        $this->executeMigration($migration);

        $entity = em()->create(
            EntityWithHandler::class,
            ['propertyClass' => new PropertyClass(1, 10)]
        );

        em()->clear();

        // Fetch it again
        $refetchedEntity = em()->get(EntityWithHandler::class, $entity->uuid);

        self::assertEquals(1, $refetchedEntity->propertyClass->min);
    }
}
