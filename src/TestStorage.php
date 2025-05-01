<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests;

use Medas\StorageManager\{
    Interfaces\Storage,
    Interfaces\StorageController,
    Interfaces\Store,
    Migrations\MigrationBuildManager,
    Migrations\MigrationManager,
    Migrations\Settings
};

trait TestStorage
{
    abstract protected function storage(): Storage;

    abstract protected function store(string $name): Store;

    abstract protected function controller(): StorageController;

    protected function createMigrationClassContent(string $directory): string|null
    {
        $buildManager = service(MigrationBuildManager::class);
        $path = __DIR__ . '/Entities/' . $directory;
        $realDirectory = realpath($path);

        if ($realDirectory === false) {
            throw new \Exception('directory "' . $path . '" does not exist');
        }

        $settings = new Settings([$realDirectory], $path);

        return $buildManager->createMigrationClassCode($settings)->classCode;
    }

    protected function executeMigration(string $migration): void
    {
        preg_match('/class (Migration\d+)/', $migration, $match);

        $directory = __DIR__ . DIRECTORY_SEPARATOR . 'migrations';
        $fileName = $directory . DIRECTORY_SEPARATOR . $match[1] . '.php';

        // Prepare the migration test directory
        if (!file_exists($directory)) {
            mkdir($directory);
        }
        else {
            foreach (glob($directory . DIRECTORY_SEPARATOR . '*') as $existingFile) {
                unlink($existingFile);
            }
        }

        // Execute the migration
        file_put_contents($fileName, $migration);

        $manager = service(MigrationManager::class);

        $manager->migrate($directory);

        // Remove the test directory
        unlink($fileName);

        rmdir($directory);
    }
}
