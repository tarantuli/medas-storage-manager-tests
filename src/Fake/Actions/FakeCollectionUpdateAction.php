<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Actions;

use Medas\Core\{Interfaces\ManagedCollection, Types\Collection};
use Medas\StorageManager\{Interfaces\Storage, UnitOfWork\BaseAction, UnitOfWork\Priority};

class FakeCollectionUpdateAction extends BaseAction
{
    public function __construct(
        Storage                           $storage,
        public readonly string            $storeName,
        public readonly object            $entity,
        public readonly string            $propertyName,
        public readonly Collection        $collectionType,
        public readonly ManagedCollection $values,
        Priority                          $priority = Priority::UpdateCollection,
    )
    {
        parent::__construct($storage, $priority);
    }
}
