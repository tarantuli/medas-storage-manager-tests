<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\EntityManager\MetaData\Property;
use Medas\StorageManager\Interfaces\{Fetchers\CollectionRecordFetcher, Store};

readonly class FakeCollectionRecordFetcher implements CollectionRecordFetcher
{
    public function __construct(
        private InMemoryDatabase $db,
    )
    {
    }

    public function fetch(Store $store, object $entity, Property $property): iterable
    {
        $id = method_exists($entity, 'id') ? $entity->id() : null;

        if ($id === null) {
            return [];
        }

        return $this->db->find($store->name(), ['id' => $id]);
    }
}
