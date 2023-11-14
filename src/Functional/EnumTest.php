<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\StorageManager\{Exceptions\EnumIsNotBacked, Interfaces\Store};
use Medas\StorageManagerTests\TestStorage;

trait EnumTest
{
    use TestStorage;

    public function testUnbackedEnum(): void
    {
        self::expectException(EnumIsNotBacked::class);

        $this->createMigrationClassContent('UnbackedEnums');
    }

    public function testBackedEnum(): void
    {
        $this->controller()->deleteStore($this->store('backed_enum_entities'));

        $migration = $this->createMigrationClassContent('BackedEnums');

        self::assertStringContainsString('`enum` tinyint', $migration);
        self::assertStringContainsString('char(3)', $migration);

        $this->executeMigration($migration);

        self::assertInstanceOf(Store::class, $this->store('backed_enum_entities'));
    }
}
