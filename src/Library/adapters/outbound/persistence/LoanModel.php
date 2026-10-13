<?php

namespace Library\Adapters\Outbound\Persistence;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LoanModel extends Model
{
    use HasUuids;

    protected $table = 'loan';

    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'loaned_by_user_id',
        'loaned_at',
    ];

    protected $casts = [
        'loaned_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(LoanItemModel::class, 'loan_id');
    }

    public function member()
    {
        return $this->belongsTo(MemberModel::class, 'member_id');
    }
}
