<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

use Medas\StorageManagerTests\Entities\PropertyHandlers\{EntityWithHandler, PropertyClass};
use Medas\StorageManagerTests\TestStorage;

trait HandledPropertyTest
{
    use TestStorage;

    public function testStoreHandledProperty(): void
    {
        $this->entityManager()->autoPersistOnCreate();
        $this->controller()->deleteStore($this->store('entities_with_handler'));

        // Ensure storage existence
        $migration = $this->createMigrationClassContent('PropertyHandlers');

        $this->executeMigration($migration);

        $entity = $this->entityManager()->create(
            EntityWithHandler::class,
            ['propertyClass' => new PropertyClass(1, 10)]
        );

        $this->entityManager()->clear();

        // Fetch it again
        $refetchedEntity = $this->entityManager()->get(EntityWithHandler::class, $entity->uuid);

        self::assertEquals(1, $refetchedEntity->propertyClass->min);
    }
}
