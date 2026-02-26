<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Attributes;

use Medas\Core\Interfaces\{HasId, Uuid};
use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity(store: 'uuid_posts')]
class UuidPost implements HasId
{
    #[Id]
    private Uuid $id;

    public function id(): Uuid
    {
        return $this->id;
    }
}
