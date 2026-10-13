<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\Clock;
use Library\Application\Ports\Outbound\LoanRepository;

/** HU-09: operational listing — shows the member's name on purpose, unlike the report (§3.7). */
class OverdueService
{
    public function __construct(
        private LoanRepository $loans,
        private Clock $clock,
    ) {
    }

    public function handle(): array
    {
        return $this->loans->findOverdueItems($this->clock->now());
    }
}
