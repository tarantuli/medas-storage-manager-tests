<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

use Medas\EntityManager\EntityManager;
use Medas\StorageManagerTests\Entities\Migrations\StoredEntity;
use Medas\StorageManagerTests\TestStorage;

trait HydratorTest
{
    use TestStorage;

    /**
     * @depends testEpCreateAndFetch
     */
    public function testHydrateEntity(): void
    {
        $entityManager = service(EntityManager::class);
        $entity = $entityManager->get(StoredEntity::class, 1);

        self::assertInstanceOf(StoredEntity::class, $entity);
        self::assertEquals(1, $entity->id());
        self::assertNull($entity->createdAt);
    }
}
