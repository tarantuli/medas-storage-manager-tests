<?php

declare(strict_types=1);

use Medas\ServiceManager\{ServiceConfig, ServiceManager};
use Medas\StorageManagerTests\StorageManagerTestsPackage;

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        StorageManagerTestsPackage::instance(),
    ]);

    return $config;
});
