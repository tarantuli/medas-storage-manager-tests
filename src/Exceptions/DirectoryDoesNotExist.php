<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Exceptions;

use Medas\Core\Exceptions\BaseException;

class DirectoryDoesNotExist extends BaseException
{
    public function __construct(string $path)
    {
        parent::__construct($path);
    }

    public function pattern(): string
    {
        return 'Directory "%s" does not exist';
    }
}
