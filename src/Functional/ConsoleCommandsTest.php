<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\ConfigOptions\OptionController;
use Medas\Core\Interfaces\ConfigManager;
use Medas\StorageManager\ConfigOptions\{EntityDirectory, MigrationDirectory};
use Medas\StorageManager\ConsoleCommands\{MakeMigrationCommand, MigrateCommand};
use Medas\StorageManager\Migrations\MigrationManager;
use Medas\StorageManagerTests\TestStorage;

trait ConsoleCommandsTest
{
    use TestStorage;

    public function testMakeMigrationCommand(): void
    {
        $this->controller()->deleteStore($this->store('new_stored_entities'));
        $directory = $this->getDirectory();
        $initialCount = count(glob($directory . '/*'));

        $this->makeMigration();

        self::assertCount($initialCount + 1, glob($directory . '/*'));

        $this->cleanUp();
    }

    private function getDirectory(): string
    {
        return service(OptionController::class)->getValue(service(MigrationDirectory::class));
    }

    private function makeMigration(): void
    {
        $this->setMigrationEntityDirectory();

        ob_start();
        service(MakeMigrationCommand::class)->process([]);
        ob_end_clean();
    }

    private function setMigrationEntityDirectory(): void
    {
        service(ConfigManager::class)->setValue(
            service(OptionController::class)->getPath(service(EntityDirectory::class)),
            __DIR__ . '/../Entities/Migrations'
        );
    }

    private function cleanUp(): void
    {
        foreach (glob($this->getDirectory() . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function testMigrateCommand(): void
    {
        $this->makeMigration();

        service(MigrateCommand::class)->process([]);

        self::assertCount(1, service(MigrationManager::class)->processedMigrations());
        $this->cleanUp();
    }
}
