<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Builders;

use Medas\Core\{Interfaces\ManagedCollection, Types\Collection};
use Medas\StorageManager\Interfaces\{Builders\CollectionUpdateBuilder, Store};
use Medas\StorageManager\UnitOfWork\ActionSet;
use Medas\StorageManagerTests\Fake\Actions\FakeCollectionUpdateAction;

class FakeCollectionUpdateBuilder implements CollectionUpdateBuilder
{
    public function build(
        Store             $store,
        object            $entity,
        string            $name,
        Collection        $type,
        ManagedCollection $values,
    ): ActionSet
    {
        return ActionSet::fromAction(new FakeCollectionUpdateAction(
            $store->storage(),
            $store->name(),
            $entity,
            $name,
            $type,
            $values,
        ));
    }
}
