<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Unit;

use Medas\StorageManager\Migrations\{MigrationManager, MigrationStoreManager};
use Medas\StorageManagerTests\TestMigrations\TestMigration;

class MigrationManagerTest extends BaseHappyPathTests
{
    public function testMigrationIsExecuted(): void
    {
        $this->migrate();

        self::assertTrue(TestMigration::$wasExecuted);
    }

    public function testExecutedMigrationIsRecorded(): void
    {
        $this->migrate();

        $record = $this->db->findOne($this->migrationsStore(), [
            'migration' => TestMigration::class,
        ]);

        self::assertNotNull($record);
        self::assertSame(TestMigration::class, $record['migration']);
    }

    public function testMigrationIsNotRunTwice(): void
    {
        $this->migrate();
        $this->migrate();

        $records = $this->db->find($this->migrationsStore(), [
            'migration' => TestMigration::class,
        ]);

        self::assertCount(1, $records, 'Migration should only be recorded once');
    }

    public function testAlreadyExecutedMigrationIsSkipped(): void
    {
        $this->migrate();

        // Reset the flag — if migrate() runs again, it would set it to true again
        TestMigration::$wasExecuted = false;

        $this->migrate();

        self::assertFalse(
            TestMigration::$wasExecuted,
            'Already-executed migration should not run again'
        );
    }

    private function migrate(): void
    {
        $directory = realpath(__DIR__ . '/../../src/TestMigrations');

        service(MigrationManager::class)->migrate($directory);
    }

    // Whatever the migrations store is configured as: the default is not this test's to know.
    private function migrationsStore(): string
    {
        return service(MigrationStoreManager::class)->store()->name();
    }

    protected function setUpStores(): void
    {
        $this->db->addStore($this->migrationsStore());
    }

    protected function setUp(): void
    {
        TestMigration::$wasExecuted = false;

        parent::setUp();
    }
}
