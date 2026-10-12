<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Unit;

use Medas\StorageManagerTests\Entities\Migrations\StoredEntity;

class EntityPersistenceTest extends BaseHappyPathTests
{
    public function testCreateStoresRecord(): void
    {
        $entity = new StoredEntity();

        $entity->name = 'hello';

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $record = $this->db->findOne('stored_entities', ['name' => 'hello']);

        self::assertNotNull($record);
        self::assertSame('hello', $record['name']);
    }

    public function testCreatePopulatesId(): void
    {
        $entity = new StoredEntity();

        $entity->name = 'hello';

        self::assertNull($entity->id());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        self::assertSame(1, $entity->id());
    }

    public function testSecondInsertAutoIncrementsId(): void
    {
        $a = new StoredEntity();

        $a->name = 'first';

        $this->entityManager->persist($a);

        $b = new StoredEntity();

        $b->name = 'second';

        $this->entityManager->persist($b);
        $this->entityManager->flush();

        self::assertSame(1, $a->id());
        self::assertSame(2, $b->id());
    }

    public function testUpdateChangesRecord(): void
    {
        $entity = new StoredEntity();

        $entity->name = 'original';

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $entity->name = 'updated';

        $this->entityManager->flush();

        $record = $this->db->findOne('stored_entities', ['id' => $entity->id()]);

        self::assertSame('updated', $record['name']);
    }

    public function testDeleteRemovesRecord(): void
    {
        $entity = new StoredEntity();

        $entity->name = 'to-delete';

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $id = $entity->id();

        $this->entityManager->delete($entity);
        $this->entityManager->flush();

        self::assertNull($this->db->findOne('stored_entities', ['id' => $id]));
    }

    public function testGetFetchesPersistedEntity(): void
    {
        $entity = new StoredEntity();

        $entity->name = 'fetchable';

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $id = $entity->id();

        $this->entityManager->clear();

        $fetched = $this->entityManager->get(StoredEntity::class, $id);

        self::assertInstanceOf(StoredEntity::class, $fetched);
        self::assertSame('fetchable', $fetched->name);
    }

    protected function setUpStores(): void
    {
        $this->db->addStore('stored_entities');
    }
}
