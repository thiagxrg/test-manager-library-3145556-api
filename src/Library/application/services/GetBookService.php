<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\CategoryRepository;
use Library\Domain\Exceptions\NotFoundException;

class GetBookService
{
    public function __construct(
        private BookRepository $books,
        private CategoryRepository $categories,
    ) {
    }

    /** @return array{book: \Library\Domain\Book, category: ?\Library\Domain\Category} */
    public function handle(string $id): array
    {
        $book = $this->books->findActiveById($id) ?? throw new NotFoundException('Book not found.');

        return ['book' => $book, 'category' => $this->categories->findById($book->categoryId())];
    }
}
