<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests;

use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\Core\AsSingleton;
use Medas\EntityManager\EntityManagerPackage;
use Medas\ServiceManager\BasePackage;
use Medas\StorageManager\StorageManagerPackage;

class StorageManagerTestsPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [
            ConfigOptionsPackage::instance(),
            ConsolePrinterPackage::instance(),
            EntityManagerPackage::instance(),
            StorageManagerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
