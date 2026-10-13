<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    use HasUuids;

    protected $table = 'category';

    public $timestamps = false;

    protected $fillable = ['name'];
}
