<?php

namespace Library\Adapters\Outbound\Persistence;

use DateTimeImmutable;
use Illuminate\Support\Str;
use Library\Application\Ports\Outbound\MemberRepository;
use Library\Domain\Member;

class EloquentMemberRepository implements MemberRepository
{
    public function nextIdentity(): string
    {
        return (string) Str::uuid();
    }

    public function findById(string $id): ?Member
    {
        $model = MemberModel::find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function search(?string $query, int $page, int $size): array
    {
        $builder = MemberModel::query();

        if ($query !== null && $query !== '') {
            $builder->where(function ($q) use ($query) {
                $q->where('full_name', 'ILIKE', "%{$query}%")
                    ->orWhere('document_number', 'ILIKE', "%{$query}%");
            });
        }

        $total = $builder->count();

        $items = $builder->orderBy('full_name')
            ->forPage($page, $size)
            ->get()
            ->map(fn (MemberModel $m) => $this->toDomain($m))
            ->all();

        return ['items' => $items, 'total' => $total];
    }

    public function existsDocumentNumber(string $documentNumber): bool
    {
        return MemberModel::where('document_number', $documentNumber)->exists();
    }

    public function save(Member $member): void
    {
        MemberModel::updateOrCreate(
            ['id' => $member->id()],
            [
                'full_name' => $member->fullName(),
                'document_number' => $member->documentNumber(),
            ],
        );
    }

    public function countUnreturnedForMember(string $memberId): int
    {
        return LoanItemModel::whereNull('returned_at')
            ->whereIn('loan_id', LoanModel::where('member_id', $memberId)->pluck('id'))
            ->count();
    }

    public function hasOverdueForMember(string $memberId, DateTimeImmutable $now): bool
    {
        return LoanItemModel::whereNull('returned_at')
            ->where('due_at', '<', $now)
            ->whereIn('loan_id', LoanModel::where('member_id', $memberId)->pluck('id'))
            ->exists();
    }

    private function toDomain(MemberModel $m): Member
    {
        return new Member($m->id, $m->full_name, $m->document_number);
    }
}
