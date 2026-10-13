<?php

namespace Library\Adapters\Inbound\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Library\Application\Services\CreateMemberService;
use Library\Application\Services\GetMemberService;
use Library\Application\Services\SearchMembersService;
use Library\Domain\Member;

class MemberController
{
    public function __construct(
        private SearchMembersService $search,
        private GetMemberService $get,
        private CreateMemberService $create,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query('page', 1));
        $size = min(50, max(1, (int) $request->query('size', 20)));

        $result = $this->search->handle($request->query('q'), $page, $size);

        return new JsonResponse([
            'items' => array_map(fn (Member $m) => $this->toJson($m), $result['items']),
            'page' => $page,
            'size' => $size,
            'total' => $result['total'],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $result = $this->get->handle($id);

        return new JsonResponse($this->toJson($result['member']) + ['active_loans' => $result['active_loans']]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:200'],
            'document_number' => ['required', 'string', 'max:30'],
        ]);

        $member = $this->create->handle($data['full_name'], $data['document_number']);

        return new JsonResponse($this->toJson($member), 201);
    }

    private function toJson(Member $member): array
    {
        return [
            'id' => $member->id(),
            'full_name' => $member->fullName(),
            'document_number' => $member->documentNumber(),
        ];
    }
}
