<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

use Medas\EntityManager\Repository;
use Medas\StorageManagerTests\Entities\{
    Inheritance\ArmorCard,
    Inheritance\Card,
    Inheritance\WeaponCard,
    MockUpIds
};
use Medas\StorageManagerTests\TestStorage;

trait InheritanceTest
{
    use TestStorage;

    public function testInhCreateMigration(): void
    {
        $this->controller()->deleteStore($this->store('i_weapon_cards'));
        $this->controller()->deleteStore($this->store('i_armor_cards'));
        $this->controller()->deleteStore($this->store('i_cards__original_class'));
        $this->controller()->deleteStore($this->store('i_cards'));

        $migration = $this->createMigrationClassContent('Inheritance');

        self::assertStringContainsString('class Migration', $migration);

        $this->executeMigration($migration);
    }

    /** @depends testInhCreateMigration */
    public function testInhStoring(): MockUpIds
    {
        $this->entityManager()->autoPersistOnCreate();

        $weapon1 = $this->entityManager()->create(
            WeaponCard::class,
            ['name' => 'Iron blade', 'weaponType' => 'blade']
        );

        $armor1 = $this->entityManager()->create(
            ArmorCard::class,
            ['name' => 'Wooden shield', 'armorType' => 'shield']
        );

        $weaponId = $weapon1->id();
        $armorId = $armor1->id();

        self::assertNotEquals($armorId, $weaponId);

        $this->entityManager()->clear();

        $weapon2 = $this->entityManager()->get(WeaponCard::class, $weaponId);
        $armor2 = $this->entityManager()->get(ArmorCard::class, $armorId);

        self::assertInstanceOf(WeaponCard::class, $weapon2);
        self::assertInstanceOf(ArmorCard::class, $armor2);

        return new MockUpIds($weaponId, $armorId);
    }

    /** @depends testInhStoring */
    public function testInhRetrievingByChildClass(MockUpIds $ids): void
    {
        $armorCards = service(Repository::class)->fetchAll(ArmorCard::class);

        self::assertCount(1, $armorCards);
        self::assertEquals($ids->armorId, $armorCards[0]->id());
    }

    /** @depends testInhStoring */
    public function testInhRetrievingByParent(MockUpIds $ids): void
    {
        $weapon = $this->entityManager()->get(Card::class, $ids->weaponId);
        $armor = $this->entityManager()->get(Card::class, $ids->armorId);

        self::assertInstanceOf(WeaponCard::class, $weapon);
        self::assertInstanceOf(ArmorCard::class, $armor);
    }
}
