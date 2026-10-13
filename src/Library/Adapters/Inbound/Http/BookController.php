<?php

namespace Library\Adapters\Inbound\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Library\Application\Ports\Outbound\CoverStorage;
use Library\Application\Services\CreateBookService;
use Library\Application\Services\DeleteBookService;
use Library\Application\Services\DeleteCoverService;
use Library\Application\Services\GetBookService;
use Library\Application\Services\SearchBooksService;
use Library\Application\Services\UpdateBookService;
use Library\Application\Services\UploadCoverService;
use Library\Domain\Exceptions\NotFoundException;

class BookController
{
    public function __construct(
        private SearchBooksService $search,
        private GetBookService $get,
        private CreateBookService $create,
        private UpdateBookService $update,
        private DeleteBookService $delete,
        private UploadCoverService $uploadCover,
        private DeleteCoverService $deleteCover,
        private CoverStorage $covers,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query('page', 1));
        $size = min(50, max(1, (int) $request->query('size', 20)));

        $result = $this->search->handle(
            $request->query('q'),
            $request->query('category_id'),
            $page,
            $size,
        );

        return new JsonResponse([
            'items' => array_map(fn ($row) => $this->toJson($row['book'], $row['category']), $result['items']),
            'page' => $page,
            'size' => $size,
            'total' => $result['total'],
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $result = $this->get->handle($id);

        return new JsonResponse($this->toJson($result['book'], $result['category']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:250'],
            'author' => ['required', 'string', 'max:200'],
            'isbn' => ['required', 'string'],
            'category_id' => ['required', 'string'],
            'total_copies' => ['required', 'integer'],
        ]);

        $result = $this->create->handle(
            $data['title'],
            $data['author'],
            $data['isbn'],
            $data['category_id'],
            (int) $data['total_copies'],
        );

        return new JsonResponse($this->toJson($result['book'], $result['category']), 201);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:250'],
            'author' => ['required', 'string', 'max:200'],
            'category_id' => ['required', 'string'],
            'total_copies' => ['required', 'integer'],
        ]);

        $result = $this->update->handle(
            $id,
            $data['title'],
            $data['author'],
            $data['category_id'],
            (int) $data['total_copies'],
        );

        return new JsonResponse($this->toJson($result['book'], $result['category']));
    }

    public function destroy(string $id): Response
    {
        $this->delete->handle($id);

        return new Response(null, 204);
    }

    public function uploadCover(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension() ?: $file->extension();

        $key = $this->uploadCover->handle($id, $file->get(), $extension);

        return new JsonResponse(['cover_key' => $key]);
    }

    public function showCover(string $id): Response
    {
        $result = $this->get->handle($id);
        $key = $result['book']->coverKey();

        if ($key === null) {
            throw new NotFoundException('This book has no cover.');
        }

        $contents = $this->covers->read($key) ?? throw new NotFoundException('This book has no cover.');

        return new Response($contents, 200, ['Content-Type' => 'application/octet-stream']);
    }

    public function deleteCover(string $id): Response
    {
        $this->deleteCover->handle($id);

        return new Response(null, 204);
    }

    private function toJson($book, $category): array
    {
        return [
            'id' => $book->id(),
            'title' => $book->title(),
            'author' => $book->author(),
            'isbn' => $book->isbn()->value(),
            'category' => $category ? ['id' => $category->id(), 'name' => $category->name()] : null,
            'total_copies' => $book->totalCopies(),
            'available_copies' => $book->availableCopies(),
            'cover_key' => $book->coverKey(),
        ];
    }
}
