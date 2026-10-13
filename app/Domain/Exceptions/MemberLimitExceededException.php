<?php

namespace App\Domain\Exceptions;

class MemberLimitExceededException extends DomainException
{
    private int $currentCount;

    public function __construct(int $currentCount)
    {
        $this->currentCount = $currentCount;
        parent::__construct('The member already has the maximum of 3 unreturned books.');
    }

    public function code(): string
    {
        return 'MEMBER_LIMIT_EXCEEDED';
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function details(): array
    {
        return [['field' => 'member_id', 'issue' => "current count: {$this->currentCount}"]];
    }
}
