<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\Migrations;

use Medas\Core\{Interfaces\Uuid, Types\DateTime};
use Medas\EntityManager\Attributes\{Entity, Id, IsGeneratedValue, IsUnique};

#[Entity(store: 'stored_entities')]
class StoredEntity
{
    #[Id, IsGeneratedValue]
    private int $id;

    #[IsUnique]
    public string $name;

    #[DateTime]
    public \DateTime|null $createdAt = null;

    public string $defaultString = 'default string';
    public int $defaultInteger = 10;
    public Uuid $uuid;

    public function id(): int|null
    {
        return $this->id ?? null;
    }
}
