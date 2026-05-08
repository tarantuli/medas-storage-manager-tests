<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Builders;

use Medas\EntityManager\{MetaDataManager, Selector\Selector};
use Medas\StorageManager\Interfaces\Builders\SelectorActionBuilder;
use Medas\StorageManager\StorageManager;
use Medas\StorageManager\UnitOfWork\ActionSet;
use Medas\StorageManagerTests\Fake\Actions\FakeGetAction;

readonly class FakeSelectorActionBuilder implements SelectorActionBuilder
{
    public function __construct(
        private MetaDataManager $metaDataManager,
        private StorageManager  $storageManager,
    )
    {
    }

    public function build(Selector $selector, array $arguments): ActionSet
    {
        $metaData = $this->metaDataManager->get($selector->entity());
        $storage = $this->storageManager->byName($metaData->entity->storage);
        $conditions = [];

        foreach ($selector->definition()->conditions ?? [] as $condition) {
            if (isset($condition->property, $condition->parameterName)) {
                $conditions[$condition->property] = $arguments[$condition->parameterName] ?? null;
            }
        }

        return ActionSet::fromAction(new FakeGetAction(
            $storage,
            [$metaData->entity->store],
            $conditions,
        ));
    }
}
