<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

use Medas\StorageManager\{Exceptions\EnumIsNotBacked, Interfaces\Store};
use Medas\StorageManagerTests\TestStorage;

trait EnumTest
{
    use TestStorage;

    abstract protected function checkBackedEnumMigration(string $migration): void;

    public function testUnbackedEnum(): void
    {
        self::expectException(EnumIsNotBacked::class);

        $this->createMigrationClassContent('UnbackedEnums');
    }

    public function testBackedEnum(): void
    {
        $this->controller()->deleteStore($this->store('backed_enum_entities'));

        $migration = $this->createMigrationClassContent('BackedEnums');

        $this->executeMigration($migration);
        $this->checkBackedEnumMigration($migration);

        self::assertInstanceOf(Store::class, $this->store('backed_enum_entities'));
    }
}
