<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Model;

class CategoryModel extends Model
{
    protected $table = 'category';

    public $timestamps = false;

    protected $fillable = ['name'];
}
