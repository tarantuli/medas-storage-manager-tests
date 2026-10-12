<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Cycles;

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\Attributes\{Entity, Id, IsGeneratedValue};

#[Entity(store: 'c_eggs')]
class Egg implements HasId
{
    #[Id, IsGeneratedValue]
    private int $id;

    public Chicken $laidBy;

    public function id(): int
    {
        return $this->id;
    }
}
