<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LoanItemModel extends Model
{
    use HasUuids;

    protected $table = 'loan_item';

    public $timestamps = false;

    protected $fillable = [
        'loan_id',
        'book_id',
        'book_title',
        'isbn',
        'category_name',
        'due_at',
        'returned_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'returned_at' => 'datetime',
    ];
}
