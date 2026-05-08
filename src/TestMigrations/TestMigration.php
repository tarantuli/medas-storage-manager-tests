<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\TestMigrations;

use Medas\StorageManager\{Migrations\Migration, UnitOfWork\UnitOfWork};

class TestMigration implements Migration
{
    public static bool $wasExecuted = false;

    public function migrate(UnitOfWork $unitOfWork): void
    {
        self::$wasExecuted = true;
    }

    public function undo(UnitOfWork $unitOfWork): void
    {
        self::$wasExecuted = false;
    }
}
