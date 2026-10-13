<?php

namespace Library\Adapters\Inbound\Http;

use Illuminate\Http\JsonResponse;
use Library\Application\Ports\Outbound\CategoryRepository;

class CategoryController
{
    public function __construct(private CategoryRepository $categories)
    {
    }

    public function index(): JsonResponse
    {
        $items = array_map(
            fn ($c) => ['id' => $c->id(), 'name' => $c->name()],
            $this->categories->all(),
        );

        return new JsonResponse($items);
    }
}
