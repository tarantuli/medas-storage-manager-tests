<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Builders;

use Medas\StorageManager\Interfaces\{Builders\InsertBuilder, Store};
use Medas\StorageManager\UnitOfWork\{ActionSet, Priority};
use Medas\StorageManagerTests\Fake\Actions\FakeInsertAction;

class FakeInsertBuilder implements InsertBuilder
{
    public function build(Store $store, array $values, Priority $priority = Priority::CreateRecord): ActionSet
    {
        return ActionSet::fromAction(new FakeInsertAction(
            $store->storage(),
            $store->name(),
            $values,
            $priority,
        ));
    }
}
