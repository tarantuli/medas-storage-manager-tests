<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\InsertionOrder;

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity(store: 'o_novels')]
class Novel implements HasId
{
    #[Id]
    public int $id;

    public string $title;
    public Writer $writer;

    public function id(): int
    {
        return $this->id;
    }
}
