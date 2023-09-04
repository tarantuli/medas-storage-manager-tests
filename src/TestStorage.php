<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests;

use Medas\StorageManager\Interfaces\{Storage, StorageController, Store};
use Medas\StorageManager\Migrations\{MigrationBuildManager, MigrationManager};

trait TestStorage
{
    abstract protected function storage(): Storage;

    abstract protected function store(string $name): Store;

    abstract protected function controller(): StorageController;

    protected function createMigrationClassContent(string $directory): string|null
    {
        $buildManager = service(MigrationBuildManager::class);
        $realDirectory = realpath(__DIR__ . '/../MockUps/' . $directory);

        if ($realDirectory === false) {
            throw new \Exception('directory "' . __DIR__ . '/../MockUps/' . $directory . '" does not exist');
        }

        return $buildManager->createMigrationClass($realDirectory);
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
