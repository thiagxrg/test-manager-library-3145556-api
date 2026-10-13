<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\CoverStorage;
use Library\Domain\Exceptions\NotFoundException;

class DeleteCoverService
{
    public function __construct(
        private BookRepository $books,
        private CoverStorage $storage,
    ) {
    }

    public function handle(string $bookId): void
    {
        $book = $this->books->findActiveById($bookId) ?? throw new NotFoundException('Book not found.');

        if ($book->coverKey() !== null) {
            // Order matters (data-model.md §7): null the key and persist first, delete the binary after.
            $key = $book->coverKey();
            $book->setCoverKey(null);
            $this->books->save($book);
            $this->storage->delete($key);
        }
    }
}
