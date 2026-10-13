<?php

namespace Library\Adapters\Outbound\Persistence;

use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Library\Application\Ports\Outbound\ReportReader;

/** Query builder, not Eloquent, on purpose (ADR-004): a report row is not a model. */
class EloquentReportReader implements ReportReader
{
    public function mostBorrowed(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $rows = DB::table('loan_item as li')
            ->join('loan as l', 'l.id', '=', 'li.loan_id')
            ->whereBetween('l.loaned_at', [$from, $to])
            ->selectRaw('li.book_id, li.book_title, li.category_name, count(*) as times_borrowed')
            ->groupBy('li.book_id', 'li.book_title', 'li.category_name')
            ->orderByDesc('times_borrowed')
            ->orderBy('li.book_title')
            ->get();

        return $rows->map(fn ($row) => [
            'book_id' => $row->book_id,
            'book_title' => $row->book_title,
            'category_name' => $row->category_name,
            'times_borrowed' => (int) $row->times_borrowed,
        ])->all();
    }
}
