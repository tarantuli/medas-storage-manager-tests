<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Integration;

use Medas\Core\Interfaces\Uuid;
use Medas\StorageManager\Interfaces\Store;
use Medas\StorageManagerTests\Entities\Attributes\{UuidPost, UuidPropertyPost};
use Medas\StorageManagerTests\TestStorage;

trait UuidTest
{
    use TestStorage;

    private const string TABLE_NAME = 'uuid_posts';

    public function testCreateTable(): void
    {
        $this->controller()->deleteStore($this->store(self::TABLE_NAME));

        $migration = $this->createMigrationClassContent('Attributes');

        $this->executeMigration($migration);

        self::assertInstanceOf(Store::class, $this->store(self::TABLE_NAME));
    }

    /**
     * @depends testCreateTable
     */
    public function testCreateInstance(): UuidPost
    {
        $post = new UuidPost();
        $post2 = new UuidPost();

        $this->entityManager()->persist($post, $post2);
        $this->entityManager()->flush();

        self::assertInstanceOf(Uuid::class, $post->id());
        self::assertNotEquals($post2->id(), $post->id());

        return $post;
    }

    /**
     * @depends testCreateTable
     */
    public function testUuidProperty(): void
    {
        $post = new UuidPropertyPost();

        $this->entityManager()->persist($post);
        $this->entityManager()->flush();

        self::assertTrue($post->id() > 0);
        self::assertInstanceOf(Uuid::class, $post->uuid());
    }
}
