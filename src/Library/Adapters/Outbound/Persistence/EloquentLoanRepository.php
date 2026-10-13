<?php

namespace Library\Adapters\Outbound\Persistence;

use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Library\Application\Ports\Outbound\LoanRepository;
use Library\Domain\Isbn;
use Library\Domain\Loan;
use Library\Domain\LoanItem;

class EloquentLoanRepository implements LoanRepository
{
    public function nextIdentity(): string
    {
        return (string) Str::uuid();
    }

    public function nextItemIdentity(): string
    {
        return (string) Str::uuid();
    }

    public function insert(Loan $loan): void
    {
        LoanModel::create([
            'id' => $loan->id(),
            'member_id' => $loan->memberId(),
            'loaned_by_user_id' => $loan->loanedByUserId(),
            'loaned_at' => $loan->loanedAt(),
        ]);

        foreach ($loan->items() as $item) {
            LoanItemModel::create([
                'id' => $item->id(),
                'loan_id' => $loan->id(),
                'book_id' => $item->bookId(),
                'book_title' => $item->bookTitle(),
                'isbn' => $item->isbn()->value(),
                'category_name' => $item->categoryName(),
                'due_at' => $item->dueAt(),
                'returned_at' => $item->returnedAt(),
            ]);
        }
    }

    public function findById(string $id): ?Loan
    {
        $model = LoanModel::with('items')->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findByIdForUpdate(string $id): ?Loan
    {
        $model = LoanModel::find($id);

        if (! $model) {
            return null;
        }

        $items = LoanItemModel::where('loan_id', $id)->lockForUpdate()->get();

        return $this->toDomain($model, $items);
    }

    public function updateReturnedAt(string $loanId, string $itemId, DateTimeImmutable $returnedAt): void
    {
        LoanItemModel::where('id', $itemId)->where('loan_id', $loanId)->update([
            'returned_at' => $returnedAt,
        ]);
    }

    public function search(array $filters, int $page, int $size): array
    {
        $builder = LoanModel::query();

        if (! empty($filters['from'])) {
            $builder->where('loaned_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $builder->where('loaned_at', '<=', $filters['to']);
        }

        if (! empty($filters['member_id'])) {
            $builder->where('member_id', $filters['member_id']);
        }

        if (! empty($filters['status'])) {
            $now = $filters['now'] ?? new DateTimeImmutable('now');

            match ($filters['status']) {
                'closed' => $builder->whereDoesntHave('items', fn ($q) => $q->whereNull('returned_at')),
                'overdue' => $builder->whereHas('items', fn ($q) => $q->whereNull('returned_at')->where('due_at', '<', $now)),
                'open' => $builder
                    ->whereHas('items', fn ($q) => $q->whereNull('returned_at'))
                    ->whereDoesntHave('items', fn ($q) => $q->whereNull('returned_at')->where('due_at', '<', $now)),
                default => null,
            };
        }

        $total = $builder->count();

        $items = $builder->with('items')
            ->orderByDesc('loaned_at')
            ->forPage($page, $size)
            ->get()
            ->map(fn (LoanModel $m) => $this->toDomain($m))
            ->all();

        return ['items' => $items, 'total' => $total];
    }

    public function findOverdueItems(DateTimeImmutable $now): array
    {
        $rows = LoanItemModel::whereNull('returned_at')
            ->where('due_at', '<', $now)
            ->with(['loan.member'])
            ->orderBy('due_at')
            ->get();

        return $rows->map(function (LoanItemModel $item) use ($now) {
            $dueAt = $item->due_at->toImmutable();

            return [
                'loan_id' => $item->loan_id,
                'item_id' => $item->id,
                'book_title' => $item->book_title,
                'member_name' => $item->loan->member->full_name,
                'due_at' => $dueAt,
                'days_late' => $dueAt->diff($now)->days,
            ];
        })->sortByDesc('days_late')->values()->all();
    }

    private function toDomain(LoanModel $model, ?\Illuminate\Support\Collection $items = null): Loan
    {
        $items ??= $model->items;

        $loanItems = $items->map(fn (LoanItemModel $i) => new LoanItem(
            $i->id,
            $i->loan_id,
            $i->book_id,
            $i->book_title,
            new Isbn($i->isbn),
            $i->category_name,
            $i->due_at->toImmutable(),
            $i->returned_at?->toImmutable(),
        ))->all();

        return new Loan(
            $model->id,
            $model->member_id,
            $model->loaned_by_user_id,
            $model->loaned_at->toImmutable(),
            $loanItems,
        );
    }
}
