<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Unit;

use Medas\StorageManager\{
    Interfaces\RecordSet,
    UnitOfWork\Action,
    UnitOfWork\BaseAction,
    UnitOfWork\Priority,
    UnitOfWork\UnitOfWork
};
use Medas\StorageManagerTests\Entities\Inheritance\WeaponCard;
use Medas\StorageManagerTests\Fake\FakeStorage;

class UnitOfWorkOrderingTest extends BaseHappyPathTests
{
    public function testUnitOfWorkSortsByPriority(): void
    {
        $storage = new FakeStorage('default');
        $unitOfWork = new UnitOfWork();
        $low = $this->stubAction($storage, Priority::DeleteRecord);
        $high = $this->stubAction($storage, Priority::CreateRecord);
        $mid = $this->stubAction($storage, Priority::UpdateRecord);

        // Add in reverse priority order
        $unitOfWork->addAction($low);
        $unitOfWork->addAction($mid);
        $unitOfWork->addAction($high);

        $actions = $unitOfWork->actions();

        self::assertSame(Priority::CreateRecord, $actions[0]->priority());
        self::assertSame(Priority::UpdateRecord, $actions[1]->priority());
        self::assertSame(Priority::DeleteRecord, $actions[2]->priority());
    }

    public function testDependentRecordRunsAfterPrimaryRecord(): void
    {
        $storage = new FakeStorage('default');
        $unitOfWork = new UnitOfWork();
        $dependent = $this->stubAction($storage, Priority::CreateDependentRecord);
        $primary = $this->stubAction($storage, Priority::CreateRecord);

        // Add dependent first — the wrong order
        $unitOfWork->addAction($dependent);
        $unitOfWork->addAction($primary);

        $sorted = $unitOfWork->actions();

        self::assertSame(Priority::CreateRecord, $sorted[0]->priority());
        self::assertSame(Priority::CreateDependentRecord, $sorted[1]->priority());
    }

    private function stubAction(FakeStorage $storage, Priority $priority): Action
    {
        return new class($storage, $priority)extends BaseAction
        {
            public function __construct(FakeStorage $storage, Priority $priority)
            {
                parent::__construct($storage, $priority);
            }

            public function recordSet(): RecordSet|null
            {
                return null;
            }
        };
    }

    public function testMultiTableInheritancePrimaryStoreInsertedFirst(): void
    {
        // When EntityPersister queues creates for a multi-table entity, the
        // primary store action (CreateRecord) must execute before the dependent
        // store actions (CreateDependentRecord) so that LastInsertIdPlaceholder
        // can be resolved correctly.
        //
        // We verify this indirectly: if the ordering were wrong, the dependent
        // insert would carry id=null and InMemoryDatabase would auto-generate a
        // mismatched id instead of linking to the primary record.
        $weapon = $this->entityManager->create(
            WeaponCard::class,
            ['name' => 'Longsword', 'weaponType' => 'sword']
        );

        $this->entityManager->flush();

        $weaponId = $weapon->id();
        $primaryRecord = $this->db->findOne('i_cards', ['id' => $weaponId]);
        $dependentRecord = $this->db->findOne('i_weapon_cards', ['id' => $weaponId]);

        self::assertNotNull($primaryRecord, 'Primary store record should exist');
        self::assertNotNull($dependentRecord, 'Dependent store record should exist');

        self::assertSame(
            $primaryRecord['id'],
            $dependentRecord['id'],
            'Dependent record id should match primary record id'
        );
    }

    protected function setUpStores(): void
    {
        $this->db->addStore('stored_entities');
        $this->db->addStore('i_cards');
        $this->db->addStore('i_weapon_cards');
        $this->db->addStore('i_armor_cards');
        $this->db->addStore('i_cards__original_class');
    }
}
