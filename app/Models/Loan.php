<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
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

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function loanedBy()
    {
        return $this->belongsTo(User::class, 'loaned_by_user_id');
    }

    public function items()
    {
        return $this->hasMany(LoanItem::class);
    }

    /** open | overdue | closed — nunca se guarda, siempre se calcula (Articulo VII) */
    public function status(\DateTimeInterface $now): string
    {
        $items = $this->items;

        if ($items->every(fn (LoanItem $item) => $item->returned_at !== null)) {
            return 'closed';
        }

        $hasOverdue = $items->contains(
            fn (LoanItem $item) => $item->returned_at === null && $item->due_at < $now
        );

        return $hasOverdue ? 'overdue' : 'open';
    }
}
