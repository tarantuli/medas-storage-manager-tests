<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Inheritence;

use Medas\EntityManager\Attributes\Entity;

#[Entity('i_armor_cards')]
class ArmorCard extends ItemCard
{
    public string $armorType;

    public function __construct()
    {
        $this->itemType = 'armor';
    }
}
