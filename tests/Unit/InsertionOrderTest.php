<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Unit;

use Medas\StorageManager\Exceptions\ReferenceCycleBetweenNewEntities;
use Medas\StorageManagerTests\Entities\Cycles\{Chicken, Egg};
use Medas\StorageManagerTests\Entities\Relations\{Group, Person};

// New entities are inserted after the new entities they refer to, whatever order they
// were persisted in, because the database checks a foreign key as the row goes in.
class InsertionOrderTest extends BaseHappyPathTests
{
    public function testAReferencedEntityPersistedLaterIsInsertedFirst(): void
    {
        $person = $this->entityManager->create(Person::class, ['name' => 'Ada']);
        $group = $this->entityManager->create(Group::class, ['name' => 'Engines']);

        // Set after both were persisted, so the person is queued first.
        new \ReflectionProperty(Person::class, 'group')->setValue($person, $group);

        $this->entityManager->flush();

        $record = $this->db->findOne('r_people', ['name' => 'Ada']);

        self::assertNotNull($record);
        self::assertSame($group->id(), $record['group']);
    }

    public function testACycleBetweenNewEntitiesIsRefused(): void
    {
        $chicken = new Chicken();
        $egg = new Egg();

        $chicken->hatchedFrom = $egg;
        $egg->laidBy = $chicken;

        $this->entityManager->persist($chicken, $egg);

        $this->expectException(ReferenceCycleBetweenNewEntities::class);

        $this->entityManager->flush();
    }

    protected function setUpStores(): void
    {
        $this->db->addStore('r_people');
        $this->db->addStore('r_groups');
        $this->db->addStore('r_groups__labels');
        $this->db->addStore('c_chickens');
        $this->db->addStore('c_eggs');
    }
}
