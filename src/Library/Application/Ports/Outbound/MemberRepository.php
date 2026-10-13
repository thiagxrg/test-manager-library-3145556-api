<?php

namespace Library\Application\Ports\Outbound;

use DateTimeImmutable;
use Library\Domain\Member;

interface MemberRepository
{
    public function nextIdentity(): string;

    public function findById(string $id): ?Member;

    /** @return array{items: Member[], total: int} */
    public function search(?string $query, int $page, int $size): array;

    public function existsDocumentNumber(string $documentNumber): bool;

    public function save(Member $member): void;

    public function countUnreturnedForMember(string $memberId): int;

    public function hasOverdueForMember(string $memberId, DateTimeImmutable $now): bool;
}
