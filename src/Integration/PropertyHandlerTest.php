<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

use Medas\StorageManager\Interfaces\Store;
use Medas\StorageManagerTests\TestStorage;

trait PropertyHandlerTest
{
    use TestStorage;

    abstract protected function checkPropertyHandlerMigration(string $migration): void;

    public function testCreateStorage(): void
    {
        $this->controller()->deleteStore($this->store('entities_with_handler'));

        $migration = $this->createMigrationClassContent('PropertyHandlers');

        $this->executeMigration($migration);
        $this->checkPropertyHandlerMigration($migration);

        self::assertInstanceOf(Store::class, $this->store('entities_with_handler'));
    }
}
