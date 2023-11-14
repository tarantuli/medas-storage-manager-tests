<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\StorageManagerTests\TestStorage;
use Medas\StorageManager\Interfaces\Store;

trait PropertyHandlerTest
{
    use TestStorage;

    public function testCreateStorage(): void
    {
        $this->controller()->deleteStore($this->store('entities_with_handler'));

        $migration = $this->createMigrationClassContent('PropertyHandlers');

        self::assertStringContainsString('`propertyClass` text not null', $migration);

        $this->executeMigration($migration);

        self::assertInstanceOf(Store::class, $this->store('entities_with_handler'));
    }
}
