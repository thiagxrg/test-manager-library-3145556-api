<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasUuids;

    protected $table = 'member';

    public $timestamps = false;

    protected $fillable = [
        'full_name',
        'document_number',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
