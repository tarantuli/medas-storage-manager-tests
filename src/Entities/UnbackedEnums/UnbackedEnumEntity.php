<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\UnbackedEnums;

use Medas\EntityManager\Attributes\{Entity, Id};

#[Entity(store: 'unbacked_enum_entities')]
class UnbackedEnumEntity
{
    #[Id]
    private UnbackedEnum $enum;
}
