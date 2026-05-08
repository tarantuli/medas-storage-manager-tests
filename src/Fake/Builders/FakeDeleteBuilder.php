<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Builders;

use Medas\StorageManager\Interfaces\{Builders\DeleteBuilder, Store};
use Medas\StorageManager\UnitOfWork\{ActionSet, Priority};
use Medas\StorageManagerTests\Fake\Actions\FakeDeleteAction;

class FakeDeleteBuilder implements DeleteBuilder
{
    public function build(Store $store, array $conditions, Priority $priority = Priority::DeleteRecord): ActionSet
    {
        return ActionSet::fromAction(new FakeDeleteAction(
            $store->storage(),
            $store->name(),
            $conditions,
            $priority,
        ));
    }
}
