<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\{
    Entities\LastInsertIdPlaceholder,
    Interfaces\ActionExecutor,
    UnitOfWork\Action,
    UnitOfWork\ActionSet
};

readonly class FakeActionExecutor implements ActionExecutor
{
    public function __construct(
        private InMemoryDatabase $db,
    )
    {
    }

    public function execute(Action $action, ActionSet|null $actionSet = null): void
    {
        match (true) {
            $action instanceof Actions\FakeInsertAction => $this->handleInsert($action, $actionSet),
            $action instanceof Actions\FakeUpdateAction => $this->handleUpdate($action),
            $action instanceof Actions\FakeDeleteAction => $this->handleDelete($action),
            $action instanceof Actions\FakeGetAction => $this->handleGet($action),
            $action instanceof Actions\FakeCollectionUpdateAction => $this->handleCollectionUpdate($action),
            default => null,
        };

        if ($onComplete = $action->onComplete()) {
            $onComplete($action->storage(), $this->db->lastInsertId);
        }
    }

    private function handleInsert(Actions\FakeInsertAction $action, ActionSet|null $actionSet): void
    {
        $values = $this->resolvePlaceholders($action->values, $actionSet);
        $id = $this->db->insert($action->storeName, $values);

        if ($actionSet !== null) {
            $actionSet->lastInsertId = $id;
        }
    }

    private function resolvePlaceholders(array $values, ActionSet|null $actionSet): array
    {
        // LastInsertIdPlaceholder is used for dependent store records in multi-table
        // inheritance. Resolve it to the last generated id so the dependent record
        // carries the same id as the primary record it belongs to.
        foreach ($values as $key => $value) {
            if ($value instanceof LastInsertIdPlaceholder) {
                $values[$key] = $actionSet?->lastInsertId ?? $this->db->lastInsertId;
            }
        }

        return $values;
    }

    private function handleUpdate(Actions\FakeUpdateAction $action): void
    {
        $this->db->update($action->storeName, $action->values, $action->conditions);
    }

    private function handleDelete(Actions\FakeDeleteAction $action): void
    {
        $this->db->delete($action->storeName, $action->conditions);
    }

    private function handleGet(Actions\FakeGetAction $action): void
    {
        $records = $this->db->find($action->storeNames[0], $action->conditions);

        // Merge in data from additional stores for multi-table inheritance
        if (count($action->storeNames) > 1) {
            foreach ($records as $i => $record) {
                foreach (array_slice($action->storeNames, 1) as $joinedStore) {
                    $joined = $this->db->findOne($joinedStore, ['id' => $record['id']]);

                    if ($joined !== null) {
                        $records[$i] = array_merge($record, $joined);
                    }
                }
            }
        }

        $action->setResult(new FakeRecordSet($records));
    }

    private function handleCollectionUpdate(Actions\FakeCollectionUpdateAction $action): void
    {
        $entityId = method_exists($action->entity, 'id') ? $action->entity->id() : null;

        if ($entityId === null) {
            return;
        }

        // Replace existing collection entries for this entity
        $this->db->delete($action->storeName, ['id' => $entityId]);

        foreach ($action->values->__serialize() as $value) {
            $this->db->insert($action->storeName, ['id' => $entityId, 'value' => $value]);
        }
    }

    public function executeSet(ActionSet $actionSet): void
    {
        foreach ($actionSet as $action) {
            $this->execute($action, $actionSet);

            $actionSet->lastRecordSet = $action->recordSet();
        }
    }
}
