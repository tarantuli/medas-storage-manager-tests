<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Selectors;

use Medas\Core\AsSingleton;
use Medas\EntityManager\Selector\{
    Conditions\WhereIs,
    Definition,
    Operants\Argument,
    Operants\Property,
    Parameter,
    Selector
};
use Medas\StorageManagerTests\Entities\Migrations\StoredEntity;

class StoredEntityWithId implements Selector
{
    use AsSingleton;

    public function entity(): string
    {
        return StoredEntity::class;
    }

    public function definition(): Definition
    {
        return Definition::create(StoredEntity::class)
            ->add(new Parameter('id'))
            ->add(new WhereIs(new Property('id'), new Argument('id')));
    }
}
