<?php

declare(strict_types=1);

use Medas\StorageManagerTests\StorageManagerTestsPackage;
use Medas\ServiceManager\{ServiceConfig, ServiceManager};

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        StorageManagerTestsPackage::instance(),
    ]);

    return $config;
});
