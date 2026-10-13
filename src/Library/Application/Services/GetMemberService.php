<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\MemberRepository;
use Library\Domain\Exceptions\NotFoundException;
use Library\Domain\Member;

class GetMemberService
{
    public function __construct(private MemberRepository $members)
    {
    }

    /** @return array{member: Member, active_loans: int} */
    public function handle(string $id): array
    {
        $member = $this->members->findById($id) ?? throw new NotFoundException('Member not found.');

        return [
            'member' => $member,
            'active_loans' => $this->members->countUnreturnedForMember($id),
        ];
    }
}
