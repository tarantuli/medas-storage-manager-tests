<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\StorageManager\Interfaces\{Storage, Store};
use Medas\StorageManagerTests\TestStorage;

trait DatabaseManagerTest
{
    use TestStorage;

    public function testConnect(): void
    {
        self::assertInstanceOf(Storage::class, $this->storage());
    }

    public function testGetTable(): void
    {
        $table = $this->store('database_manager_test');
        self::assertInstanceOf(Store::class, $table);
    }
}
