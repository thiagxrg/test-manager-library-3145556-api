<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Library\Application\Ports\Outbound\BookRepository;
use Library\Domain\Book;
use Library\Domain\Isbn;
use Library\Domain\Quantity;

class EloquentBookRepository implements BookRepository
{
    public function nextIdentity(): string
    {
        return (string) Str::uuid();
    }

    public function findActiveById(string $id): ?Book
    {
        $model = BookModel::whereNull('deleted_at')->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findById(string $id): ?Book
    {
        $model = BookModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function search(?string $query, ?string $categoryId, int $page, int $size): array
    {
        $builder = BookModel::query()->whereNull('deleted_at');

        if ($query !== null && $query !== '') {
            $builder->where(function ($q) use ($query) {
                $q->where('title', 'ILIKE', "%{$query}%")
                    ->orWhere('author', 'ILIKE', "%{$query}%");
            });
        }

        if ($categoryId !== null) {
            $builder->where('category_id', $categoryId);
        }

        $total = $builder->count();

        $items = $builder->orderBy('title')
            ->forPage($page, $size)
            ->get()
            ->map(fn (BookModel $m) => $this->toDomain($m))
            ->all();

        return ['items' => $items, 'total' => $total];
    }

    public function existsActiveIsbn(string $isbn, ?string $excludeBookId = null): bool
    {
        $builder = BookModel::whereNull('deleted_at')->where('isbn', $isbn);

        if ($excludeBookId !== null) {
            $builder->where('id', '!=', $excludeBookId);
        }

        return $builder->exists();
    }

    public function hasOpenLoans(string $bookId): bool
    {
        return LoanItemModel::where('book_id', $bookId)->whereNull('returned_at')->exists();
    }

    public function save(Book $book): void
    {
        BookModel::updateOrCreate(
            ['id' => $book->id()],
            [
                'title' => $book->title(),
                'author' => $book->author(),
                'isbn' => $book->isbn()->value(),
                'category_id' => $book->categoryId(),
                'total_copies' => $book->totalCopies(),
                'available_copies' => $book->availableCopies(),
                'cover_key' => $book->coverKey(),
                'deleted_at' => $book->deletedAt(),
            ],
        );
    }

    public function findByIdsForUpdate(array $ids): array
    {
        return BookModel::whereIn('id', $ids)
            ->lockForUpdate()
            ->get()
            ->map(fn (BookModel $m) => $this->toDomain($m))
            ->all();
    }

    private function toDomain(BookModel $m): Book
    {
        return new Book(
            $m->id,
            $m->title,
            $m->author,
            new Isbn($m->isbn),
            $m->category_id,
            new Quantity($m->total_copies),
            $m->available_copies,
            $m->cover_key,
            $m->deleted_at?->toImmutable(),
        );
    }
}
