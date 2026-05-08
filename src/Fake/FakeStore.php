<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\Interfaces\{Storage, Store};

readonly class FakeStore implements Store
{
    public function __construct(
        private string  $name,
        private Storage $storage,
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function storage(): Storage
    {
        return $this->storage;
    }
}
