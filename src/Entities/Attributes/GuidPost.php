<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Attributes;

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\Attributes\{Entity, Id};
use Medas\EntityManager\Types\Guid;

#[Entity(store: 'guid_posts')]
class GuidPost implements HasId
{
    #[Id, Guid]
    private string $id;

    public function id(): string
    {
        return $this->id;
    }
}
