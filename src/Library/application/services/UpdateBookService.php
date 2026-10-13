<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\CategoryRepository;
use Library\Domain\Book;
use Library\Domain\Exceptions\NotFoundException;
use Library\Domain\Exceptions\ValidationException;
use Library\Domain\Quantity;

class UpdateBookService
{
    public function __construct(
        private BookRepository $books,
        private CategoryRepository $categories,
    ) {
    }

    public function handle(string $id, string $title, string $author, string $categoryId, int $totalCopies): Book
    {
        $book = $this->books->findActiveById($id) ?? throw new NotFoundException('Book not found.');

        if ($this->categories->findById($categoryId) === null) {
            throw new ValidationException('Unknown category.', [['field' => 'category_id', 'issue' => 'does not exist']]);
        }

        $book->rename(trim($title), trim($author), $categoryId);
        $book->changeTotalCopies(new Quantity($totalCopies));

        $this->books->save($book);

        return $book;
    }
}
