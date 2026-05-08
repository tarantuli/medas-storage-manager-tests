<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Actions;

use Medas\StorageManager\{Interfaces\Storage, UnitOfWork\BaseAction, UnitOfWork\Priority};

class FakeInsertAction extends BaseAction
{
    public function __construct(
        Storage                $storage,
        public readonly string $storeName,
        public readonly array  $values,
        Priority               $priority = Priority::CreateRecord,
    )
    {
        parent::__construct($storage, $priority);
    }
}
