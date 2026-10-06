<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'from_plan_id',
        'to_plan_id',
        'effective_at',
        'change_type',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'effective_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(
            Subscription::class
        );
    }

    public function fromPlan(): BelongsTo
    {
        return $this->belongsTo(
            Plan::class,
            'from_plan_id'
        );
    }

    public function toPlan(): BelongsTo
    {
        return $this->belongsTo(
            Plan::class,
            'to_plan_id'
        );
    }
}