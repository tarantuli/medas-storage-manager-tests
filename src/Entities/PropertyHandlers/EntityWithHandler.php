<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\PropertyHandlers;

use Medas\Core\{Attributes\Handler, Interfaces\Uuid};
use Medas\EntityManager\{Attributes\Entity, Attributes\Id, Properties\SerializingHandler};

#[Entity(store: 'entities_with_handler')]
class EntityWithHandler
{
    #[Id]
    public Uuid $uuid;

    #[Handler(SerializingHandler::class)]
    public PropertyClass $propertyClass;
}
