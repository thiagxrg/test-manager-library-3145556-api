<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\MemberRepository;

class SearchMembersService
{
    public function __construct(private MemberRepository $members)
    {
    }

    /** @return array{items: \Library\Domain\Member[], total: int} */
    public function handle(?string $query, int $page, int $size): array
    {
        return $this->members->search($query, $page, $size);
    }
}
