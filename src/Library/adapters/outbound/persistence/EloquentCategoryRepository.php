<?php

namespace Library\Adapters\Outbound\Persistence;

use Library\Application\Ports\Outbound\CategoryRepository;
use Library\Domain\Category;

class EloquentCategoryRepository implements CategoryRepository
{
    public function all(): array
    {
        return CategoryModel::orderBy('name')->get()
            ->map(fn (CategoryModel $m) => new Category($m->id, $m->name))
            ->all();
    }

    public function findById(string $id): ?Category
    {
        $model = CategoryModel::find($id);

        return $model ? new Category($model->id, $model->name) : null;
    }
}
