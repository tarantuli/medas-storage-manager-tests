<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\StorageManagerTests\Entities\Relations\{Group, Person};
use Medas\StorageManagerTests\TestStorage;

trait OneToManyRelationTest
{
    use TestStorage;

    public function testCreateOtmMigration(): void
    {
        $this->controller()->deleteStore($this->store('r_other_people'));
        $this->controller()->deleteStore($this->store('r_groups__labels'));
        $this->controller()->deleteStore($this->store('r_people'));
        $this->controller()->deleteStore($this->store('r_groups'));

        $migration = $this->createMigrationClassContent('Relations');

        self::assertStringContainsString('class Migration', $migration);
    }

    public function testExecuteMigration(): void
    {
        $this->rebuildTables();

        // Check that both tables exist and are empty
        self::assertNull($this->controller()->recordFetchers()->filteredFetcher()->fetchOne($this->store('r_people')));
        self::assertNull($this->controller()->recordFetchers()->filteredFetcher()->fetchOne($this->store('r_groups')));
    }

    public function testStoreRelation(): void
    {
        em()->autoPersistOnCreate();

        $this->rebuildTables();

        $group = em()->create(Group::class, ['name' => 'test group']);
        $person = em()->create(Person::class, ['name' => 'test person', 'group' => $group]);

        self::assertEquals($group, em()->get(Group::class, 1));
        self::assertEquals($person, em()->get(Person::class, 1));
    }

    private function rebuildTables(): void
    {
        // Delete both stores if they still exist
        $this->controller()->deleteStore($this->store('r_other_people'));
        $this->controller()->deleteStore($this->store('r_people'));
        $this->controller()->deleteStore($this->store('r_groups__labels'));
        $this->controller()->deleteStore($this->store('r_groups'));

        // Execute the migration
        $migration = $this->createMigrationClassContent('Relations');

        $this->executeMigration($migration);
    }
}
