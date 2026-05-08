<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Builders;

use Medas\StorageManager\Interfaces\{Builders\UpdateBuilder, Store};
use Medas\StorageManager\UnitOfWork\ActionSet;
use Medas\StorageManagerTests\Fake\Actions\FakeUpdateAction;

class FakeUpdateBuilder implements UpdateBuilder
{
    public function build(Store $store, array $updates, array $conditions): ActionSet
    {
        return ActionSet::fromAction(new FakeUpdateAction(
            $store->storage(),
            $store->name(),
            $updates,
            $conditions,
        ));
    }
}
