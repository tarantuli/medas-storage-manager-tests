<?php

declare(strict_types=1);

namespace Medas\StorageManagerTests\Functional;

use Medas\StorageManagerTests\Entities\ManyToMany\{Book, Label, Labels};
use Medas\StorageManagerTests\TestStorage;

trait ManyToManyRelationTest
{
    use TestStorage;

    public function testCreateMtnMigration(): void
    {
        $this->controller()->deleteStore($this->store('books__labels'));
        $this->controller()->deleteStore($this->store('books'));
        $this->controller()->deleteStore($this->store('labels'));

        $migration = $this->createMigrationClassContent('ManyToMany');

        self::assertStringContainsString('class Migration', $migration);

        $this->executeMigration($migration);
    }

    /** @depends testCreateMtnMigration */
    public function testMtmStoring(): Book
    {
        $this->entityManager()->autoPersistOnCreate();

        $label1 = $this->entityManager()->create(Label::class, ['name' => 'label 1']);
        $label2 = $this->entityManager()->create(Label::class, ['name' => 'label 2']);

        $book = $this->entityManager()->create(
            Book::class,
            ['labels' => new Labels(fn() => [$label1, $label2])]
        );

        $label1Id = $label1->id();
        $bookId = $book->id();

        $this->entityManager()->clear();

        $book = $this->entityManager()->get(Book::class, $bookId);

        self::assertInstanceOf(Book::class, $book);
        self::assertCount(2, $book->labels);
        self::assertInstanceOf(Label::class, $book->labels[0]);
        self::assertEquals($label1Id, $book->labels[0]->id());

        return $book;
    }

    /** @depends testMtmStoring */
    public function testAdding(Book $book): Book
    {
        $this->entityManager()->autoPersistOnCreate();

        $label3 = $this->entityManager()->create(Label::class, ['name' => 'label 3']);
        $book->labels[] = $label3;

        $this->entityManager()->flush();
        $this->entityManager()->clear();

        $book = $this->entityManager()->get(Book::class, $book->id());

        self::assertEquals(3, $book->labels->count());

        return $book;
    }

    /** @depends testAdding */
    public function testDeleting(Book $book): Book
    {
        $this->entityManager()->autoPersistOnCreate();

        unset($book->labels[1]);

        $this->entityManager()->flush();
        $this->entityManager()->clear();

        $book = $this->entityManager()->get(Book::class, $book->id());

        self::assertEquals(2, $book->labels->count());

        return $book;
    }

    /** @depends testDeleting */
    public function testAddingAndDeleting(Book $book): Book
    {
        $this->entityManager()->autoPersistOnCreate();

        $label4 = $this->entityManager()->create(Label::class, ['name' => 'label 4']);

        unset($book->labels[0]);

        $book->labels[] = $label4;

        $this->entityManager()->clear();

        $book = $this->entityManager()->get(Book::class, $book->id());

        self::assertEquals(2, $book->labels->count());

        return $book;
    }
}
