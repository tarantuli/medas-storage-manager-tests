<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Attributes;

use Medas\Core\Interfaces\{HasId, Uuid};
use Medas\EntityManager\Attributes\{Entity, Id, IsGeneratedValue};

#[Entity(store: 'Uuid_property_posts')]
class UuidPropertyPost implements HasId
{
    #[Id, IsGeneratedValue]
    private int $id;

    private Uuid $uuid;

    public function id(): int
    {
        return $this->id;
    }

    public function Uuid(): Uuid
    {
        return $this->uuid;
    }
}
