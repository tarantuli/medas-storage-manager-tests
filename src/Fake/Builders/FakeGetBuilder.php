<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Builders;

use Medas\StorageManager\Interfaces\{Builders\GetBuilder, Store};
use Medas\StorageManager\UnitOfWork\ActionSet;
use Medas\StorageManagerTests\Fake\Actions\FakeGetAction;

class FakeGetBuilder implements GetBuilder
{
    /** @param Store[] $stores */
    public function build(array $stores, array $filters): ActionSet
    {
        $storage = $stores[0]->storage();
        $storeNames = array_map(fn(Store $store) => $store->name(), $stores);

        return ActionSet::fromAction(new FakeGetAction(
            $storage,
            $storeNames,
            $filters,
        ));
    }
}
