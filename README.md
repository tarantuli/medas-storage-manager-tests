# medas-storage-manager-tests

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

A reusable functional test suite for `medas-storage-manager` driver implementations. Instead of writing integration tests from scratch, a driver package requires this as a dev dependency and inherits the full suite by composing PHPUnit traits.

**Test coverage by trait:**

| Trait | What it covers |
|---|---|
| `DatabaseManagerTest` | Basic connectivity and store retrieval |
| `EntityPersisterTest` | Create, read, update, delete; ID assignment; defaults; UUID auto-generation |
| `HydratorTest` | Reconstructing entities from raw storage records |
| `UnsortedTest` | Migration generation, execution, idempotency; low-level record insert/fetch; relation persistence |
| `ConsoleCommandsTest` | `make:migration` and `migrate` console commands |
| `DefaultValuesTest` | Nullable and non-nullable fields with and without defaults |
| `UuidTest` | UUID primary keys and UUID-typed properties |
| `TimestampsTest` | Automatic `createdAt` / `modifiedAt` population and update behaviour |
| `EnumTest` | Int- and string-backed enum columns; rejection of unbacked enums |
| `InheritanceTest` | Single-table and joined-table inheritance with `#[StoreOriginalEntityType]` |
| `OneToManyRelationTest` | Foreign key storage and lazy hydration |
| `ManyToManyRelationTest` | Pivot table creation, lazy collections, adding and removing related items |
| `PropertyHandlerTest` | Migration generation for entities with custom `#[Handler]` properties |
| `HandledPropertyTest` | Full persist-and-fetch cycle for custom-serialised properties |

`Functional\AllTests` composes every trait for convenience.

**Test fixture entities** live in `src/Entities/` grouped by feature area: timestamps, UUIDs, backed and unbacked enums, inheritance hierarchies, one-to-many and many-to-many relations, custom property handlers, and selector tests.

## Usage

### Package developer context

This package is a **dev dependency** for driver packages, not for application code. Add it to your driver:

```bash
composer require --dev morphp/medas-storage-manager-tests
```

**Step 1 — Bootstrap the service container** (`phpunit.bootstrap.php`):

```php
<?php

declare(strict_types=1);

use Medas\ServiceManager\{ServiceConfig, ServiceManager};
use Medas\ObjectInstantiator\ObjectInstantiator;
use Medas\StorageManagerTests\StorageManagerTestsPackage;
use YourVendor\YourDriver\YourDriverPackage;

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig(
        objectInstantiatorClass: ObjectInstantiator::class,
    );

    $config->addPackages([
        YourDriverPackage::instance(),
        StorageManagerTestsPackage::instance(),
    ]);

    return $config;
});
```

**Step 2 — `phpunit.xml`:**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="phpunit.bootstrap.php" colors="true">
    <testsuites>
        <testsuite name="Functional">
            <directory>tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

**Step 3 — Driver config** (`config.yaml`):

```yaml
pdo:
  dsn: $env(DB_PDO_DSN)
  username: $env(DB_PDO_USERNAME)
  password: $env(DB_PDO_PASSWORD)
  name: default
  persistent-connection: false

storage-manager:
  migration-directory: tests/Migrations
```

**Step 4 — Create a test class:**

```php
<?php

declare(strict_types=1);

namespace YourVendor\YourDriver\Tests;

use Medas\StorageManager\{Interfaces\Storage, Interfaces\StorageController, Interfaces\Store, StorageManager};
use Medas\StorageManagerTests\Functional\AllTests;
use PHPUnit\Framework\TestCase;

class FunctionalTest extends TestCase
{
    use AllTests;

    private Storage $storage;
    private StorageController $controller;

    protected function storage(): Storage
    {
        return $this->storage ??= service(StorageManager::class)->byName('default');
    }

    protected function store(string $name): Store
    {
        return $this->controller()->store($name);
    }

    protected function controller(): StorageController
    {
        return $this->controller ??= service(StorageManager::class)->controller($this->storage());
    }

    // Driver-specific assertion hooks (may be left empty for drivers without dialect-specific SQL)

    protected function checkBackedEnumMigration(string $migration): void
    {
        // e.g. self::assertStringContainsString('ENUM', $migration);
    }

    protected function checkPropertyHandlerMigration(string $migration): void
    {
        // e.g. self::assertStringContainsString('TEXT', $migration);
    }

    protected function preMigrationPreparations(): void {}

    protected function migrationAssertions(string $migration): void {}

    protected function postMigrationAssertions(): void {}
}
```

**Step 5 — Run:**

```bash
./vendor/bin/phpunit
```

**Using individual trait groups** — if your driver only supports a subset of features, pick only the relevant traits:

```php
use Medas\StorageManagerTests\Functional\{
    DatabaseManagerTest,
    EntityPersisterTest,
    UuidTest,
    TimestampsTest,
};
```

Each trait may declare its own `abstract protected` methods. Check the trait source for what your test class must implement.

### Backend user context

This package is for driver developers, not application developers. Application code never depends on it directly — it is always in `require-dev`.

The `medas-storage-manager` package itself is in `require-dev` of this package (not `require`) to avoid a circular dependency when `storage-manager` installs `storage-manager-tests` via a path repository during local development.
