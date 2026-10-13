<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\CategoryRepository;
use Library\Domain\Book;
use Library\Domain\Exceptions\IsbnTakenException;
use Library\Domain\Exceptions\ValidationException;
use Library\Domain\Isbn;
use Library\Domain\Quantity;

class CreateBookService
{
    public function __construct(
        private BookRepository $books,
        private CategoryRepository $categories,
    ) {
    }

    /** @return array{book: Book, category: \Library\Domain\Category} */
    public function handle(string $title, string $author, string $isbn, string $categoryId, int $totalCopies): array
    {
        $category = $this->categories->findById($categoryId);

        if ($category === null) {
            throw new ValidationException('Unknown category.', [['field' => 'category_id', 'issue' => 'does not exist']]);
        }

        $isbnVo = new Isbn($isbn);

        if ($this->books->existsActiveIsbn($isbnVo->value())) {
            throw new IsbnTakenException();
        }

        $quantity = new Quantity($totalCopies);

        $book = new Book(
            $this->books->nextIdentity(),
            trim($title),
            trim($author),
            $isbnVo,
            $categoryId,
            $quantity,
            $quantity->value(),
        );

        $this->books->save($book);

        return ['book' => $book, 'category' => $category];
    }
}
