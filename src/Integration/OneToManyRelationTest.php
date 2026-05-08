<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

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
        $this->entityManager()->autoPersistOnCreate();
        $this->rebuildTables();

        $group = $this->entityManager()->create(Group::class, ['name' => 'test group']);

        $person = $this->entityManager()->create(
            Person::class,
            ['name' => 'test person', 'group' => $group]
        );

        self::assertEquals($group, $this->entityManager()->get(Group::class, $group->id()));
        self::assertEquals($person, $this->entityManager()->get(Person::class, $person->id()));
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
