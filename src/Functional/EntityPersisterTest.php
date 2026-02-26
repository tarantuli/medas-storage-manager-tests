<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\EntityManager\Repository;
use Medas\StorageManagerTests\Entities\{
    Migrations\StoredEntity,
    Selectors\StoredEntityWithId,
    Selectors\StoredEntityWithName
};
use Medas\StorageManagerTests\TestStorage;

trait EntityPersisterTest
{
    use TestStorage;

    public function testEpCreateMigration(): void
    {
        $this->controller()->deleteStore($this->store('stored_entities'));

        $migration = $this->createMigrationClassContent('Migrations');

        self::assertStringContainsString('class Migration', $migration);

        $this->executeMigration($migration);
    }

    public function testEpCreateAndFetch(): void
    {
        $entity = new StoredEntity();

        $entity->name = $newName = (string) mt_rand();

        self::assertNull($entity->id());

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        // Clear the cache, fetch the entity again
        $this->entityManager()->clear();

        $entity = service(Repository::class)->fetchOne(
            StoredEntityWithName::instance(),
            ['name' => $newName]
        );

        self::assertIsInt($entity->id());
        self::assertEquals($newName, $entity->name);
    }

    public function testEpCreateIsIdFilled(): void
    {
        $entity = new StoredEntity();

        $entity->name = $newName = (string) mt_rand();

        self::assertNull($entity->id());

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        // No clearing, no re-fetching
        self::assertIsInt($entity->id());
        self::assertEquals($newName, $entity->name);
    }

    public function testEpUpdate(): void
    {
        $entity = $this->entityManager()->get(StoredEntity::class, 1);

        $entity->name = $newName = (string) mt_rand();

        $this->entityManager()->flush();
        $this->entityManager()->clear();

        $entity = $this->entityManager()->get(StoredEntity::class, 1);

        self::assertEquals($newName, $entity->name);
    }

    public function testEpDelete(): void
    {
        // Create and persist a new entity
        $storedEntity = $this->entityManager()->create(
            StoredEntity::class,
            ['name' => (string) mt_rand()]
        );

        $this->entityManager()->persist($storedEntity);
        $this->entityManager()->flush();

        $id = $storedEntity->id();

        // Clear the cache and fetch it from storage to ensure it was stored
        $this->entityManager()->clear();

        $fetchedEntity = $this->entityManager()->get(StoredEntity::class, $id);

        self::assertInstanceOf(StoredEntity::class, $fetchedEntity);

        // Delete the entity and flush
        $this->entityManager()->delete($fetchedEntity);
        $this->entityManager()->flush();

        // Ensure it does not exist in the cache anymore
        $fetchedEntity = $this->entityManager()->get(StoredEntity::class, $id);

        self::assertFalse(isset($fetchedEntity->name));

        // Clear the cache and fetch it again to ensure it does not exist in storage anymore
        $this->entityManager()->clear();

        $fetchedEntity = service(Repository::class)->fetchOne(
            StoredEntityWithId::instance(),
            ['id' => $id]
        );

        self::assertNull($fetchedEntity);
    }

    public function testEpCreateAndFetchWithValues(): void
    {
        $entity = new StoredEntity();

        $entity->name = $newName = (string) mt_rand();

        self::assertNull($entity->id());

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        // Clear the cache, fetch the entity again
        $this->entityManager()->clear();

        $fetchedEntity = service(Repository::class)->getOrCreate(
            StoredEntity::class,
            ['name' => $newName]
        );

        self::assertInstanceOf(StoredEntity::class, $fetchedEntity);
    }
}
