# medas-storage-manager-tests

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

This package provides a **reusable functional test suite** for storage driver implementations built on top of `morphp/medas-storage-manager`. Instead of writing integration tests from scratch for each driver, a driver package can require this package as a dev dependency and inherit the full suite by composing a few traits.

---

## What it tests

The suite covers all major storage-manager features through the following trait groups:

| Trait                    | What it covers                                                                                    |
|--------------------------|---------------------------------------------------------------------------------------------------|
| `DatabaseManagerTest`    | Basic connectivity and store retrieval                                                            |
| `EntityPersisterTest`    | Create, read, update, delete; ID assignment; default values; UUID auto-generation                 |
| `HydratorTest`           | Reconstructing entities from raw storage records                                                  |
| `UnsortedTest`           | Migration generation, execution, idempotency; low-level record insert/fetch; relation persistence |
| `ConsoleCommandsTest`    | `make:migration` and `migrate` console commands                                                   |
| `DefaultValuesTest`      | Nullable and non-nullable fields with and without defaults                                        |
| `UuidTest`               | UUID primary keys and UUID-typed properties                                                       |
| `TimestampsTest`         | Automatic `createdAt` / `modifiedAt` population and update behaviour                              |
| `EnumTest`               | Int- and string-backed enum columns; rejection of unbacked enums                                  |
| `InheritanceTest`        | Single-table and joined-table entity inheritance with `#[StoreOriginalEntityType]`                |
| `OneToManyRelationTest`  | Foreign key storage and lazy hydration                                                            |
| `ManyToManyRelationTest` | Pivot table creation, lazy collections, adding and removing related items                         |
| `PropertyHandlerTest`    | Migration generation for entities with custom `#[Handler]` properties                             |
| `HandledPropertyTest`    | Full persist-and-fetch cycle for custom-serialised properties                                     |

All traits are combined in `Functional\AllTests` for convenience.

---

## Installation

Add this package as a **dev dependency** in your storage driver package:

```bash
composer require --dev morphp/medas-storage-manager-tests
```

---

## Usage

### 1. Bootstrap the service container

Create a `phpunit.bootstrap.php` in your driver package that boots the Medas service container with your own package registered alongside `StorageManagerTestsPackage`:

```php
<?php

declare(strict_types=1);

use Medas\ServiceManager\{ServiceConfig, ServiceManager};
use Medas\StorageManagerTests\StorageManagerTestsPackage;
use YourVendor\YourDriver\YourDriverPackage;

chdir(__DIR__);

new ServiceManager(function (): ServiceConfig {
    $config = new ServiceConfig();

    $config->addPackages([
        YourDriverPackage::instance(),
        StorageManagerTestsPackage::instance(),
    ]);

    return $config;
});
```

### 2. Add a `phpunit.xml` at your project root

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

### 3. Configure your driver

Your `config.yaml` (or equivalent) must supply a storage block and a migration directory. See `src/example.yaml` for a reference:

```yaml
env: test

storage:
  pdo:
    dsn: $env(DB_PDO_DSN)
    username: $env(DB_PDO_USERNAME)
    password: $env(DB_PDO_PASSWORD)
  migration-directory: tests/Migrations
```

### 4. Create a test class

Extend `PHPUnit\Framework\TestCase`, use `Functional\AllTests`, and implement the three abstract methods from `TestStorage` plus the hook methods declared by individual traits:

```php
<?php

declare(strict_types=1);

namespace YourVendor\YourDriver\Tests;

use Medas\StorageManager\{
    Interfaces\Storage,
    Interfaces\StorageController,
    Interfaces\Store,
    StorageManager
};
use Medas\StorageManagerTests\Integration\AllTests;
use PHPUnit\Framework\TestCase;

class FunctionalTest extends TestCase
{
    use AllTests;

    private Storage $storage;
    private StorageController $controller;

    // --- Required: resolve the Storage for your driver ---

    protected function storage(): Storage
    {
        if (!isset($this->storage)) {
            $this->storage = service(StorageManager::class)->byName('default');
        }

        return $this->storage;
    }

    protected function store(string $name): Store
    {
        return $this->controller()->store($name);
    }

    protected function controller(): StorageController
    {
        if (!isset($this->controller)) {
            $this->controller = service(StorageManager::class)->controller($this->storage());
        }

        return $this->controller;
    }

    // --- Required hook: assert driver-specific content in backed enum migrations ---

    protected function checkBackedEnumMigration(string $migration): void
    {
        // Example: self::assertStringContainsString('ENUM', $migration);
    }

    // --- Required hook: assert driver-specific content in property handler migrations ---

    protected function checkPropertyHandlerMigration(string $migration): void
    {
        // Example: self::assertStringContainsString('TEXT', $migration);
    }

    // --- Required hooks for UnsortedTest ---

    protected function preMigrationPreparations(): void
    {
        // Runs before the migration; drop any additional driver-specific objects here
    }

    protected function migrationAssertions(string $migration): void
    {
        // Assert driver-specific content in the generated migration code
    }

    protected function postMigrationAssertions(): void
    {
        // Assert a driver-specific state after the migration has run
    }
}
```

### 5. Run the suite

```bash
./vendor/bin/phpunit
```

---

## Using individual trait groups

`Functional\AllTests` composes every trait. If your driver only supports a subset of features, pick only what you need:

```php
use Medas\StorageManagerTests\Integration\{
    DatabaseManagerTest,
    EntityPersisterTest,
    HydratorTest,
};
```

Note that each trait may declare its own `abstract protected` methods that your test class must implement. Check the trait source for what is required.

---

## Entity fixtures

Test entities live in `src/Entities/` and are grouped by feature:

| Directory           | Contents                                                                                                      |
|---------------------|---------------------------------------------------------------------------------------------------------------|
| `Attributes/`       | `TimestampedPost`, `UuidPost`, `UuidPropertyPost`                                                             |
| `BackedEnums/`      | `BackedEnumEntity`, `IntBackedEnum`, `StringBackedEnum`                                                       |
| `UnbackedEnums/`    | `UnbackedEnumEntity`, `UnbackedEnum` (asserts an exception is thrown)                                         |
| `Inheritance/`      | `Card → ItemCard → WeaponCard / ArmorCard` hierarchy with `#[StoreOriginalEntityType]`                        |
| `ManyToMany/`       | `Book` ↔ `Label` with a `books__labels` pivot table                                                           |
| `Relations/`        | `Group → Person` (one-to-many), `Label`, and `AnEntitySortingBeforeGroup` (tests FK-aware migration ordering) |
| `Migrations/`       | `StoredEntity` and `NewStoredEntity` for basic persistence and migration tests                                |
| `PropertyHandlers/` | `EntityWithHandler` and `PropertyClass` for custom serialisation via `#[Handler]`                             |
| `Selectors/`        | `StoredEntityWithId` and `StoredEntityWithName` — `Selector` implementations for `StoredEntity`               |
| `Structure/`        | `EntityWithDefaultValues` for nullable and default field introspection                                        |

---

## Package structure

```
src/
  StorageManagerTestsPackage.php   Medas package definition; registers all required dependencies
  TestStorage.php                  Shared trait: migration helpers and EntityManager accessor
  ExampleTest.php                  Reference implementation using a PDO 'default' storage
  example.yaml                     Reference config file
  Exceptions/                      Domain exceptions used by TestStorage helpers
  Entities/                        Test fixture entities (grouped by feature, see table above)
  Functional/                      One trait per feature area, all composed by AllTests
```
