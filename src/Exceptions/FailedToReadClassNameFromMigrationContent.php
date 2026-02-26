<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Exceptions;

use Medas\Core\Exceptions\BaseException;

class FailedToReadClassNameFromMigrationContent extends BaseException
{
    public function __construct(string $migration)
    {
        parent::__construct(substr($migration, 0, 200));
    }

    public function pattern(): string
    {
        return 'Failed to read class name from migration content "%s"';
    }
}
