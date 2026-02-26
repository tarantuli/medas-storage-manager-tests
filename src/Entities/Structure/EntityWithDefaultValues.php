<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Structure;

use Medas\Core\Types\Uuid;
use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity]
class EntityWithDefaultValues
{
    #[Id, Uuid]
    public string $id;

    private \DateTime $dateTimeNotNullNoDefault;
    private \DateTime|null $dateTimeNullNoDefault;
    private \DateTime|null $dateTimeNullDefaultNull = null;
}
