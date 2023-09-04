<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Entities\BackedEnums;

enum StringBackedEnum: string
{
    case Value1 = 'one';
    case Value2 = 'two';
}
