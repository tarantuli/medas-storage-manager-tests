<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests;

use Medas\ConfigManager\ConfigManagerPackage;
use Medas\ConfigOptions\ConfigOptionsPackage;
use Medas\ConsolePrinter\ConsolePrinterPackage;
use Medas\Core\AsSingleton;
use Medas\EntityManager\EntityManagerPackage;
use Medas\Events\EventsPackage;
use Medas\RamseyUuidBridge\RamseyUuidBridgePackage;
use Medas\ServiceManager\BasePackage;
use Medas\StorageManager\StorageManagerPackage;

class StorageManagerTestsPackage extends BasePackage
{
    use AsSingleton;

    public function dependencies(): array
    {
        return [

            ConfigManagerPackage::instance(),
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
