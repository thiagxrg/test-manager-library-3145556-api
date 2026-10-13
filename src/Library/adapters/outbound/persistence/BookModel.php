<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BookModel extends Model
{
    use HasUuids;

    protected $table = 'book';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'category_id',
        'total_copies',
        'available_copies',
        'cover_key',
        'deleted_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id');
    }
}
