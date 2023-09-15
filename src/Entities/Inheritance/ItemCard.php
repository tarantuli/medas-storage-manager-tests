<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity]
class ItemCard extends Card
{
    public string $itemType;
}
