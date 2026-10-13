<?php

namespace Library\Application\Services;

use Library\Application\Ports\Outbound\MemberRepository;
use Library\Domain\Exceptions\DocumentTakenException;
use Library\Domain\Member;

class CreateMemberService
{
    public function __construct(private MemberRepository $members)
    {
    }

    public function handle(string $fullName, string $documentNumber): Member
    {
        if ($this->members->existsDocumentNumber($documentNumber)) {
            throw new DocumentTakenException();
        }

        $member = new Member($this->members->nextIdentity(), trim($fullName), trim($documentNumber));

        $this->members->save($member);

        return $member;
    }
}
