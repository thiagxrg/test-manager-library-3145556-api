<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MemberModel extends Model
{
    use HasUuids;

    protected $table = 'member';

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'document_number',
    ];
}
