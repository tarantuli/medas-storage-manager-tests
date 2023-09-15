<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Inheritance;

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\Attributes\{Entity, Id, IsGeneratedValue, IsUnique, StoreOriginalEntityType};

#[Entity('i_cards'), StoreOriginalEntityType]
class Card implements HasId
{
    #[Id, IsGeneratedValue]
    protected int $id;

    #[IsUnique]
    protected string $name;

    public function id(): int
    {
        return $this->id;
    }
}
