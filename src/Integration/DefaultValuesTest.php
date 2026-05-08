<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

use Medas\MigrationBuilder\Structure\{Blueprint, EntityBlueprintBuilder};
use Medas\StorageManagerTests\Entities\Structure\EntityWithDefaultValues;
use Medas\StorageManagerTests\TestStorage;

trait DefaultValuesTest
{
    use TestStorage;

    public function testStructureFinder(): Blueprint
    {
        $blueprint = service(EntityBlueprintBuilder::class)->find(EntityWithDefaultValues::class);

        self::assertInstanceOf(
            Blueprint\Field::class,
            $blueprint->fieldByName('dateTimeNotNullNoDefault')
        );

        return $blueprint;
    }

    /** @depends testStructureFinder */
    public function testDateTimeNotNullNoDefault(Blueprint $blueprint): void
    {
        $field = $blueprint->fieldByName('dateTimeNotNullNoDefault');

        self::assertFalse($field->isNullable);
        self::assertFalse($field->hasDefault);
    }

    /** @depends testStructureFinder */
    public function testDateTimeNullNoDefault(Blueprint $blueprint): void
    {
        $field = $blueprint->fieldByName('dateTimeNullNoDefault');

        self::assertTrue($field->isNullable);
        self::assertFalse($field->hasDefault);
    }

    /** @depends testStructureFinder */
    public function testDateTimeNullDefaultNull(Blueprint $blueprint): void
    {
        $field = $blueprint->fieldByName('dateTimeNullDefaultNull');

        self::assertTrue($field->isNullable);
        self::assertTrue($field->hasDefault);
        self::assertNull($field->default);
    }
}
