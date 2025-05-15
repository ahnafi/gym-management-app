<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GymVisit extends Model
{
    protected $table = 'gym_visits';

    protected $fillable = [
      'visit_date',
      'entry_time',
      'exit_time',
      'status',
      'user_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
