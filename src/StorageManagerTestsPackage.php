<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests;

use Medas\ConfigOptions\ConfigOptionsPackage;
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
        return [
            ConfigOptionsPackage::instance(),
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
