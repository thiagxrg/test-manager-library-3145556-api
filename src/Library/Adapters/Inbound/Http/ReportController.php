<?php

namespace Library\Adapters\Inbound\Http;

use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Library\Application\Services\MostBorrowedReportService;

class ReportController
{
    public function __construct(private MostBorrowedReportService $mostBorrowed)
    {
    }

    public function mostBorrowed(Request $request): JsonResponse
    {
        $data = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date'],
        ]);

        $from = new DateTimeImmutable($data['from']);
        $to = new DateTimeImmutable($data['to'].' 23:59:59');

        $rows = $this->mostBorrowed->handle($from, $to);

        return new JsonResponse([
            'from' => $data['from'],
            'to' => $data['to'],
            'rows' => $rows,
        ]);
    }
}
