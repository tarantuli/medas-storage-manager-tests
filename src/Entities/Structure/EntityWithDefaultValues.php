<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Structure;

use Medas\EntityManager\{Attributes\Entity, Attributes\Id, Types\Uuid};

#[Entity]
class EntityWithDefaultValues
{
    #[Id, Uuid]
    public string $id;

    private \DateTime $dateTimeNotNullNoDefault;
    private \DateTime|null $dateTimeNullNoDefault;
    private \DateTime|null $dateTimeNullDefaultNull = null;
}
