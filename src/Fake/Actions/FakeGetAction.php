<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake\Actions;

use Medas\StorageManager\{
    Interfaces\RecordSet,
    Interfaces\Storage,
    UnitOfWork\BaseAction,
    UnitOfWork\Priority
};
use Medas\StorageManagerTests\Fake\FakeRecordSet;

class FakeGetAction extends BaseAction
{
    private FakeRecordSet|null $result = null;

    /** @param string[] $storeNames */
    public function __construct(
        Storage               $storage,
        public readonly array $storeNames,
        public readonly array $conditions,
        Priority              $priority = Priority::Default,
    )
    {
        parent::__construct($storage, $priority);
    }

    public function setResult(FakeRecordSet $result): void
    {
        $this->result = $result;
    }

    public function recordSet(): RecordSet|null
    {
        return $this->result;
    }
}
