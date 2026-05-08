<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Inheritance;

use Medas\EntityManager\Attributes\Entity;

#[Entity('i_weapon_cards')]
class WeaponCard extends ItemCard
{
    public string $weaponType;

    public function __construct()
    {
        $this->itemType = 'weapon';
    }

    public function name(): string
    {
        return $this->name;
    }
}
