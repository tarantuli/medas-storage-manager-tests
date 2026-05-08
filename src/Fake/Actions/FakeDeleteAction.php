<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Actions;

use Medas\StorageManager\{Interfaces\Storage, UnitOfWork\BaseAction, UnitOfWork\Priority};

class FakeDeleteAction extends BaseAction
{
    public function __construct(
        Storage                $storage,
        public readonly string $storeName,
        public readonly array  $conditions,
        Priority               $priority = Priority::DeleteRecord,
    )
    {
        parent::__construct($storage, $priority);
    }
}
