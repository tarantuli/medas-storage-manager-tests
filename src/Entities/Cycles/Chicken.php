<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Cycles;

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\Attributes\{Entity, Id, IsGeneratedValue};

// Refers to an Egg that refers back to it: two new ones can't be inserted in either order.
#[Entity(store: 'c_chickens')]
class Chicken implements HasId
{
    #[Id, IsGeneratedValue]
    private int $id;

    public Egg $hatchedFrom;

    public function id(): int
    {
        return $this->id;
    }
}
