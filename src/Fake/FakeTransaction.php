<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\Interfaces\Transaction;

class FakeTransaction implements Transaction
{
    public bool $inTransaction = false;

    public function begin(): void
    {
        $this->inTransaction = true;
    }

    public function rollback(): void
    {
        $this->inTransaction = false;
    }

    public function commit(): void
    {
        $this->inTransaction = false;
    }
}
