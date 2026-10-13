<?php

namespace Library\Adapters\Inbound\Http;

use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Library\Application\Services\GetLoanService;
use Library\Application\Services\LendBooksService;
use Library\Application\Services\OverdueService;
use Library\Application\Services\ReturnLoanItemService;
use Library\Application\Services\SearchLoansService;
use Library\Domain\Loan;
use Library\Domain\LoanItem;

class LoanController
{
    public function __construct(
        private LendBooksService $lend,
        private ReturnLoanItemService $returnItem,
        private GetLoanService $get,
        private SearchLoansService $search,
        private OverdueService $overdue,
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'member_id' => ['required', 'string'],
            'items' => ['required', 'array', 'min:1', 'max:3'],
            'items.*.book_id' => ['required', 'string'],
        ]);

        $bookIds = array_map(fn ($item) => $item['book_id'], $data['items']);

        $loan = $this->lend->handle($data['member_id'], $bookIds, $request->user()->id);

        return new JsonResponse($this->toJson($loan, 'open'), 201);
    }

    public function show(string $id): JsonResponse
    {
        $result = $this->get->handle($id);

        return new JsonResponse($this->toJson($result['loan'], $result['status']));
    }

    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query('page', 1));
        $size = min(50, max(1, (int) $request->query('size', 20)));

        $filters = [];
        if ($request->query('from')) {
            $filters['from'] = new DateTimeImmutable($request->query('from'));
        }
        if ($request->query('to')) {
            $filters['to'] = new DateTimeImmutable($request->query('to'));
        }
        if ($request->query('status')) {
            $filters['status'] = $request->query('status');
        }
        if ($request->query('member_id')) {
            $filters['member_id'] = $request->query('member_id');
        }

        $result = $this->search->handle($filters, $page, $size);

        return new JsonResponse([
            'items' => array_map(fn ($row) => $this->toJson($row['loan'], $row['status']), $result['items']),
            'page' => $page,
            'size' => $size,
            'total' => $result['total'],
        ]);
    }

    public function returnItem(string $loanId, string $itemId): JsonResponse
    {
        $item = $this->returnItem->handle($loanId, $itemId);

        return new JsonResponse($this->itemJson($item));
    }

    public function overdueList(): JsonResponse
    {
        $items = array_map(fn ($row) => [
            'loan_id' => $row['loan_id'],
            'item_id' => $row['item_id'],
            'book_title' => $row['book_title'],
            'member_name' => $row['member_name'],
            'due_at' => $row['due_at']->format(DateTimeImmutable::ATOM),
            'days_late' => $row['days_late'],
        ], $this->overdue->handle());

        return new JsonResponse(['items' => $items]);
    }

    private function toJson(Loan $loan, string $status): array
    {
        return [
            'id' => $loan->id(),
            'member_id' => $loan->memberId(),
            'loaned_at' => $loan->loanedAt()->format(DateTimeImmutable::ATOM),
            'status' => $status,
            'items' => array_map(fn (LoanItem $i) => $this->itemJson($i), $loan->items()),
        ];
    }

    private function itemJson(LoanItem $item): array
    {
        return [
            'id' => $item->id(),
            'book_id' => $item->bookId(),
            'book_title' => $item->bookTitle(),
            'isbn' => $item->isbn()->value(),
            'category_name' => $item->categoryName(),
            'due_at' => $item->dueAt()->format(DateTimeImmutable::ATOM),
            'returned_at' => $item->returnedAt()?->format(DateTimeImmutable::ATOM),
        ];
    }
}
