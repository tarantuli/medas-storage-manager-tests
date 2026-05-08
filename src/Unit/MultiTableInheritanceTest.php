<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Unit;

use Medas\StorageManagerTests\Entities\Inheritance\{ArmorCard, WeaponCard};

class MultiTableInheritanceTest extends BaseHappyPathTest
{
    public function testWeaponCardStoresBasePropertiesInParentStore(): void
    {
        $weapon = $this->entityManager->create(WeaponCard::class, [
            'name' => 'Iron blade',
            'weaponType' => 'blade',
        ]);

        $this->entityManager->flush();

        $record = $this->db->findOne('i_cards', ['id' => $weapon->id()]);

        self::assertNotNull($record);
        self::assertSame('Iron blade', $record['name']);
    }

    public function testWeaponCardStoresChildPropertiesInChildStore(): void
    {
        $weapon = $this->entityManager->create(WeaponCard::class, [
            'name' => 'Steel axe',
            'weaponType' => 'axe',
        ]);

        $this->entityManager->flush();

        $record = $this->db->findOne('i_weapon_cards', ['id' => $weapon->id()]);

        self::assertNotNull($record);
        self::assertSame('axe', $record['weaponType']);
    }

    public function testChildStoreRecordSharesIdWithParentStore(): void
    {
        $weapon = $this->entityManager->create(WeaponCard::class, [
            'name' => 'Dagger',
            'weaponType' => 'dagger',
        ]);

        $this->entityManager->flush();

        $parentRecord = $this->db->findOne('i_cards', ['id' => $weapon->id()]);
        $childRecord = $this->db->findOne('i_weapon_cards', ['id' => $weapon->id()]);

        self::assertNotNull($parentRecord);
        self::assertNotNull($childRecord);
        self::assertSame($parentRecord['id'], $childRecord['id']);
    }

    public function testArmorCardStoresInItsOwnChildStore(): void
    {
        $armor = $this->entityManager->create(ArmorCard::class, [
            'name' => 'Wooden shield',
            'armorType' => 'shield',
        ]);

        $this->entityManager->flush();

        $parentRecord = $this->db->findOne('i_cards', ['id' => $armor->id()]);
        $childRecord = $this->db->findOne('i_armor_cards', ['id' => $armor->id()]);

        self::assertNotNull($parentRecord);
        self::assertNotNull($childRecord);
        self::assertSame('Wooden shield', $parentRecord['name']);
        self::assertSame('shield', $childRecord['armorType']);
    }

    public function testTwoEntitiesGetDistinctIds(): void
    {
        $weapon = $this->entityManager->create(WeaponCard::class, [
            'name' => 'Sword',
            'weaponType' => 'sword',
        ]);

        $armor = $this->entityManager->create(ArmorCard::class, [
            'name' => 'Plate',
            'armorType' => 'full',
        ]);

        $this->entityManager->flush();

        self::assertNotSame($weapon->id(), $armor->id());
    }

    public function testOriginalClassIsRecordedInLinkingStore(): void
    {
        $weapon = $this->entityManager->create(WeaponCard::class, [
            'name' => 'Bow',
            'weaponType' => 'ranged',
        ]);

        $this->entityManager->flush();

        $record = $this->db->findOne('i_cards__original_class', ['id' => $weapon->id()]);

        self::assertNotNull($record);
        self::assertSame(WeaponCard::class, $record['entityClass']);
    }

    public function testFetchChildEntityById(): void
    {
        $weapon = $this->entityManager->create(WeaponCard::class, [
            'name' => 'Spear',
            'weaponType' => 'polearm',
        ]);

        $this->entityManager->flush();

        $id = $weapon->id();

        $this->entityManager->clear();

        $fetched = $this->entityManager->get(WeaponCard::class, $id);

        self::assertInstanceOf(WeaponCard::class, $fetched);
        self::assertSame('Spear', $fetched->name());
        self::assertSame('polearm', $fetched->weaponType);
    }

    protected function setUpStores(): void
    {
        $this->db->addStore('i_cards');
        $this->db->addStore('i_weapon_cards');
        $this->db->addStore('i_armor_cards');
        $this->db->addStore('i_cards__original_class');
    }
}
