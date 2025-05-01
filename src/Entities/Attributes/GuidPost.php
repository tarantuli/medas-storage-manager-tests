<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Attributes;

use Medas\Core\Interfaces\{Guid, HasId};
use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity(store: 'guid_posts')]
class GuidPost implements HasId
{
    #[Id]
    private Guid $id;

    public function id(): Guid
    {
        return $this->id;
    }
}
