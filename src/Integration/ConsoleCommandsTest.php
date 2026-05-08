<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

use Medas\ConfigOptions\OptionController;
use Medas\Console\Commands\CommandInput;
use Medas\Core\Interfaces\ConfigManager;
use Medas\EntityManager\ConfigOptions\EntityDirectories;
use Medas\MigrationBuilder\ConsoleCommands\MakeMigrationCommand;
use Medas\StorageManager\{
    ConfigOptions\MigrationDirectory,
    ConsoleCommands\MigrateCommand,
    Migrations\MigrationManager
};
use Medas\StorageManagerTests\TestStorage;

trait ConsoleCommandsTest
{
    use TestStorage;

    private mixed $originalEntityDirectories;

    public function testMakeMigrationCommand(): void
    {
        $this->controller()->deleteStore($this->store('new_stored_entities'));

        $directory = $this->getDirectory();
        $initialCount = count(glob($directory . '/*'));

        $this->makeMigration();

        self::assertCount($initialCount + 1, glob($directory . '/*'));

        $this->cleanUp();
    }

    public function testMigrateCommand(): void
    {
        $aPrioriCount = count(service(MigrationManager::class)->processedMigrations());

        $this->makeMigration();

        service(MigrateCommand::class)->process(new CommandInput([], []));

        self::assertCount(
            $aPrioriCount + 1,
            service(MigrationManager::class)->processedMigrations()
        );

        $this->cleanUp();
    }

    private function makeMigration(): void
    {
        $this->setMigrationEntityDirectory();

        ob_start();

        service(MakeMigrationCommand::class)->process(new CommandInput([], []));

        ob_end_clean();
    }

    private function setMigrationEntityDirectory(): void
    {
        $configManager = service(ConfigManager::class);
        $optionController = service(OptionController::class);
        $path = $optionController->getPath(service(EntityDirectories::class));

        $this->originalEntityDirectories = $configManager->hasValue($path)
            ? $configManager->getValue($path)
            : null;

        $configManager->setValue($path, [realpath(__DIR__ . '/../Entities/Migrations')]);
    }

    private function cleanUp(): void
    {
        $path = service(OptionController::class)->getPath(service(EntityDirectories::class));

        if ($this->originalEntityDirectories) {
            service(ConfigManager::class)->setValue($path, $this->originalEntityDirectories);
        }

        foreach (glob($this->getDirectory() . '/*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function getDirectory(): string
    {
        return service(OptionController::class)->getValue(service(MigrationDirectory::class));
    }
}
