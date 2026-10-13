<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\Clock;
use Library\Application\Ports\Outbound\LoanRepository;
use Library\Domain\Exceptions\NotFoundException;
use Library\Domain\Loan;

class GetLoanService
{
    public function __construct(
        private LoanRepository $loans,
        private Clock $clock,
    ) {
    }

    /** @return array{loan: Loan, status: string} */
    public function handle(string $id): array
    {
        $loan = $this->loans->findById($id) ?? throw new NotFoundException('Loan not found.');

        return ['loan' => $loan, 'status' => $loan->status($this->clock->now())];
    }
}
