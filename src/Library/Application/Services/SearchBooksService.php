<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\CategoryRepository;

class SearchBooksService
{
    public function __construct(
        private BookRepository $books,
        private CategoryRepository $categories,
    ) {
    }

    /** @return array{items: array<int, array{book: \Library\Domain\Book, category: ?\Library\Domain\Category}>, total: int} */
    public function handle(?string $query, ?string $categoryId, int $page, int $size): array
    {
        $result = $this->books->search($query, $categoryId, $page, $size);

        $categoryCache = [];
        $items = array_map(function ($book) use (&$categoryCache) {
            $categoryCache[$book->categoryId()] ??= $this->categories->findById($book->categoryId());

            return ['book' => $book, 'category' => $categoryCache[$book->categoryId()]];
        }, $result['items']);

        return ['items' => $items, 'total' => $result['total']];
    }
}
