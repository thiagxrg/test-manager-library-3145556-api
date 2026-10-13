<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\BookRepository;
use Library\Application\Ports\Outbound\CoverStorage;
use Library\Domain\Exceptions\NotFoundException;

/** CA-03.4: JPG/PNG, max 2 MB, validated upstream by content (controller/request); stores an opaque key. */
class UploadCoverService
{
    public function __construct(
        private BookRepository $books,
        private CoverStorage $storage,
    ) {
    }

    public function handle(string $bookId, string $contents, string $extension): string
    {
        $book = $this->books->findActiveById($bookId) ?? throw new NotFoundException('Book not found.');

        if ($book->coverKey() !== null) {
            $this->storage->delete($book->coverKey());
        }

        $key = $this->storage->store($contents, $extension);
        $book->setCoverKey($key);
        $this->books->save($book);

        return $key;
    }
}
