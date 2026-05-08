<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests;

use Medas\StorageManager\{
    Interfaces\Storage,
    Interfaces\StorageController,
    Interfaces\Store,
    StorageManager
};
use PHPUnit\Framework\TestCase;

class IntegrationTestBase extends TestCase
{
    use Integration\AllTests;

    private Storage $storage;
    private StorageController $controller;

    protected function storage(): Storage
    {
        if (!isset($this->storage)) {
            $this->storage = service(StorageManager::class)->byName('default');
        }

        return $this->storage;
    }

    protected function store(string $name): Store
    {
        return $this->controller()->store($name);
    }

    protected function controller(): StorageController
    {
        if (!isset($this->controller)) {
            $this->controller = service(StorageManager::class)->controller($this->storage());
        }

        return $this->controller;
    }

    protected function checkBackedEnumMigration(string $migration): void
    {
        // Do nothing
    }

    protected function checkPropertyHandlerMigration(string $migration): void
    {
        // Do nothing
    }

    protected function preMigrationPreparations(): void
    {
        // Do nothing
    }

    protected function migrationAssertions(string $migration): void
    {
        // Do nothing
    }

    protected function postMigrationAssertions(): void
    {
        // Do nothing
    }
}
