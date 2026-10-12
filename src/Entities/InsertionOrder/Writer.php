<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\InsertionOrder;

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\Attributes\{Entity, Id};

// The id is set by the application, as with a uuid: known before the flush.
#[Entity(store: 'o_writers')]
class Writer implements HasId
{
    #[Id]
    public int $id;

    public string $name;

    public function id(): int
    {
        return $this->id;
    }
}
