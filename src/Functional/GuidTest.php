<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\Core\Interfaces\Guid;
use Medas\StorageManager\Interfaces\Store;
use Medas\StorageManagerTests\Entities\Attributes\{GuidPost, GuidPropertyPost};
use Medas\StorageManagerTests\TestStorage;

trait GuidTest
{
    use TestStorage;

    private const TABLE_NAME = 'guid_posts';

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
    public function testCreateInstance(): GuidPost
    {
        $post = new GuidPost();
        $post2 = new GuidPost();

        em()->persist($post, $post2);
        em()->flush();

        self::assertTrue($this->isGuid($post->id()));
        self::assertNotEquals($post2->id(), $post->id());

        return $post;
    }

    /**
     * @depends testCreateTable
     */
    public function testGuidProperty(): void
    {
        $post = new GuidPropertyPost();

        em()->persist($post);
        em()->flush();

        self::assertTrue($post->id() > 0);
        self::assertInstanceOf(Guid::class, $post->guid());
        self::assertTrue($this->isGuid((string) $post->guid()));
    }

    private function isGuid(string $value): bool
    {
        return (bool) preg_match('/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)})$/i', $value);
    }
}
