<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Attributes;

use Medas\Core\Interfaces\HasId;
use Medas\EntityManager\{
    Attributes\Entity,
    Attributes\Id,
    Attributes\IsGeneratedValue,
    Traits\Timestamps
};

#[Entity(store: 'timestamped_posts')]
class TimestampedPost implements HasId
{
    use Timestamps;

    #[Id, IsGeneratedValue]
    private int $id;

    public int $counter = 0;

    public function id(): int
    {
        return $this->id;
    }
}
