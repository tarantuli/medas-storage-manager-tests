<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Fake;

use Medas\StorageManager\Interfaces\{ActionBuilders, Builders as BuildersInterface};

readonly class FakeActionBuilders implements ActionBuilders
{
    public function __construct(
        private Builders\FakeCollectionUpdateBuilder $collectionUpdateBuilder,
        private Builders\FakeDeleteBuilder           $deleteBuilder,
        private Builders\FakeGetBuilder              $getBuilder,
        private Builders\FakeInsertBuilder           $insertBuilder,
        private Builders\FakeSelectorActionBuilder   $selectorActionBuilder,
        private Builders\FakeUpdateBuilder           $updateBuilder,
    )
    {
    }

    public function selectorAction(): BuildersInterface\SelectorActionBuilder
    {
        return $this->selectorActionBuilder;
    }

    public function insert(): BuildersInterface\InsertBuilder
    {
        return $this->insertBuilder;
    }

    public function get(): BuildersInterface\GetBuilder
    {
        return $this->getBuilder;
    }

    public function update(): BuildersInterface\UpdateBuilder
    {
        return $this->updateBuilder;
    }

    public function delete(): BuildersInterface\DeleteBuilder
    {
        return $this->deleteBuilder;
    }

    public function collectionUpdate(): BuildersInterface\CollectionUpdateBuilder
    {
        return $this->collectionUpdateBuilder;
    }
}
