<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Unit;

use Medas\StorageManager\Exceptions\ReferenceCycleBetweenNewEntities;
use Medas\StorageManagerTests\Entities\Cycles\{Chicken, Egg};
use Medas\StorageManagerTests\Entities\InsertionOrder\{Novel, Writer};

// New entities are inserted after the new entities they refer to, whatever order they
// were persisted in, because the database checks a foreign key as the row goes in.
class InsertionOrderTest extends BaseHappyPathTests
{
    public function testAReferencedEntityPersistedLaterIsInsertedFirst(): void
    {
        $novel = new Novel();
        $writer = new Writer();

        $novel->id = 1;
        $novel->title = 'The Analytical Engine';
        $writer->id = 7;
        $writer->name = 'Ada';
        $novel->writer = $writer;

        // The novel first: inserted in this order, its row would arrive before the
        // writer's row it refers to.
        $this->entityManager->persist($novel, $writer);
        $this->entityManager->flush();

        self::assertSame(['o_writers', 'o_novels'], $this->db->insertedStores);
        self::assertSame(7, $this->db->findOne('o_novels', ['id' => 1])['writer']);
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
        $this->db->addStore('o_writers');
        $this->db->addStore('o_novels');
        $this->db->addStore('c_chickens');
        $this->db->addStore('c_eggs');
    }
}
