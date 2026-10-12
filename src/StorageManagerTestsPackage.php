<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests;

use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\Core\{AsSingleton, BasePackage};
use Medas\EntityManager\EntityManagerPackage;
use Medas\Events\EventsPackage;
use Medas\RamseyUuidBridge\RamseyUuidBridgePackage;
use Medas\StorageManager\StorageManagerPackage;

class StorageManagerTestsPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        // The suite dispatches events and runs migrations, which print what they execute.
        // An application normally picks these implementations; for the suite, these do.
        return [
            ConfigOptionsPackage::instance(),
            ConsolePrinterPackage::instance(),
            EntityManagerPackage::instance(),
            EventsPackage::instance(),
            RamseyUuidBridgePackage::instance(),
            StorageManagerPackage::instance(),
        ];
    }

    public function sourceDirectory(): string
    {
        return __DIR__;
    }
}
