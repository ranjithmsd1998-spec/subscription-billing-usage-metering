<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'name',
        'code',
        'description',
        'currency',
        'base_price',
        'billing_cycle',
        'included_units',
        'overage_rate',
        'unit_name',
        'status',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'included_units' => 'integer',
        'overage_rate' => 'decimal:6',
    ];

    /**
     * Merchant who owns this plan.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Subscriptions using this plan.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Subscription periods using this plan.
     */
    public function subscriptionPeriods(): HasMany
    {
        return $this->hasMany(SubscriptionPeriod::class);
    }

    /**
     * Check whether the plan is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}