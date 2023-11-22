<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\StorageManager\Interfaces\Store;
use Medas\StorageManagerTests\Entities\Attributes\TimestampedPost;
use Medas\StorageManagerTests\TestStorage;

trait TimestampsTest
{
    use TestStorage;

    private const TS_TABLE_NAME = 'timestamped_posts';

    public function testCreateTsTable(): void
    {
        $this->controller()->deleteStore($this->store(self::TS_TABLE_NAME));

        $migration = $this->createMigrationClassContent('Attributes');

        $this->executeMigration($migration);

        self::assertInstanceOf(Store::class, $this->store(self::TS_TABLE_NAME));
    }

    /**
     * @depends testCreateTsTable
     */
    public function testCreateTsInstance(): TimestampedPost
    {
        $post = new TimestampedPost();

        em()->persist($post);
        em()->flush();

        self::assertInstanceOf(\DateTime::class, $post->createdAt());

        return $post;
    }

    /**
     * @depends testCreateTsInstance
     */
    public function testUpdateInstance(TimestampedPost $post): void
    {
        $post->counter++;

        em()->flush();

        self::assertNotEquals(
            $post->createdAt()->format(\DateTimeInterface::RFC3339_EXTENDED),
            $post->modifiedAt()->format(\DateTimeInterface::RFC3339_EXTENDED)
        );
    }
}
