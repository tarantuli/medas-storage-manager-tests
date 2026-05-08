<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\Interfaces\Storage;

readonly class FakeStorage implements Storage
{
    public function __construct(
        private string $name,
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
