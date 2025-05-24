<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipHistory extends Pivot
{
    use SoftDeletes;

    protected $table = 'membership_histories';

    protected $fillable = [
        'start_date',
        'end_date',
        'status',
        'user_id',
        'membership_package_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership_package(): BelongsTo
    {
        return $this->belongsTo(MembershipPackage::class);
    }
}
