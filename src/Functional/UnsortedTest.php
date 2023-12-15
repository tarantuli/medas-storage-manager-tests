<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\StorageManagerTests\Entities\Relations\{Group, Person};
use Medas\StorageManagerTests\TestStorage;

trait UnsortedTest
{
    use TestStorage;

    abstract protected function migrationAssertions(string $migration): void;

    public function testMigration(): void
    {
        // Delete all stores if they still exist
        $this->controller()->deleteStore($this->store('r_groups__labels'));
        $this->controller()->deleteStore($this->store('r_other_people'));
        $this->controller()->deleteStore($this->store('r_people'));
        $this->controller()->deleteStore($this->store('r_groups'));
        $this->controller()->deleteStore($this->store('r_labels'));

        // Create and execute a migration
        $migration = $this->createMigrationClassContent('Relations');

        self::assertStringContainsString('public function migrate(', $migration);

        $this->migrationAssertions($migration);
        $this->executeMigration($migration);

        self::assertTrue($this->controller()->hasStore($this->store('r_groups')));
        self::assertTrue($this->controller()->hasStore($this->store('r_people')));
        self::assertTrue($this->controller()->hasStore($this->store('r_labels')));
        self::assertTrue($this->controller()->hasStore($this->store('r_other_people')));
        self::assertTrue($this->controller()->hasStore($this->store('r_groups__labels')));

        // Another migration should be empty
        $migration = $this->createMigrationClassContent('Relations');

        self::assertNull($migration);
    }

    /**
     * @depends testMigration
     */
    public function testCreateRecords(): void
    {
        $action = $this->controller()->actionBuilders()->insert()->build(
            $this->store('r_groups'),
            ['id' => 1, 'name' => 'Test group']
        );

        $this->controller()->actionExecutor()->executeSet($action);

        self::assertFalse($action->lastRecordSet->hasRecords());

        $action = $this->controller()->actionBuilders()->insert()->build(
            $this->store('r_people'),
            ['id' => 1, 'name' => 'Test person', 'group' => 1]
        );

        $this->controller()->actionExecutor()->executeSet($action);

        self::assertFalse($action->lastRecordSet->hasRecords());
    }

    /**
     * @dpeends testCreateRecords
     */
    public function testFetchRecords(): void
    {
        $record = $this->controller()->recordFetchers()->filteredFetcher()->fetchOne(
            $this->store('r_groups'),
            ['id' => 1]
        );

        self::assertArrayHasKey('name', $record);
        self::assertEquals('Test group', $record['name']);
    }

    /**
     * @depends testMigration
     */
    public function testCreateGroup(): Group
    {
        $group = em()->create(Group::class, ['name' => 'related group']);

        em()->persist($group);
        em()->flush();

        self::assertIsNumeric($group->id());

        return $group;
    }

    /** @depends testCreateGroup */
    public function testCreatePerson(Group $group): Person
    {
        $person = em()->create(Person::class, ['name' => 'related person', 'group' => $group]);

        em()->persist($person);
        em()->flush();

        self::assertIsNumeric($person->id());

        return $person;
    }

    /**
     * @depends testCreatePerson
     */
    public function testFetchRelation(Person $person): void
    {
        self::assertInstanceOf(Group::class, em()->get(Person::class, $person->id())->group());
    }
}
