<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\Clock;
use Library\Application\Ports\Outbound\LoanRepository;

class SearchLoansService
{
    public function __construct(
        private LoanRepository $loans,
        private Clock $clock,
    ) {
    }

    /**
     * @param array{from?: \DateTimeImmutable, to?: \DateTimeImmutable, status?: string, member_id?: string} $filters
     * @return array{items: array<int, array{loan: \Library\Domain\Loan, status: string}>, total: int}
     */
    public function handle(array $filters, int $page, int $size): array
    {
        $now = $this->clock->now();
        $filters['now'] = $now;

        $result = $this->loans->search($filters, $page, $size);

        $items = array_map(
            fn ($loan) => ['loan' => $loan, 'status' => $loan->status($now)],
            $result['items'],
        );

        return ['items' => $items, 'total' => $result['total']];
    }
}
