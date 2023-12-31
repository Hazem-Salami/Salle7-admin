<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'user_id',
        'evaluator_id',
        'feedback',
    ];

    /**
     * Get the user that owns the evaluation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class,'evaluator_id','id');
    }

}
