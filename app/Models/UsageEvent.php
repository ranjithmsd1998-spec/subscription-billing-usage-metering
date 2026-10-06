<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'customer_id',
        'subscription_id',
        'subscription_period_id',
        'idempotency_key',
        'usage_units',
        'unit_name',
        'occurred_at',
        'metadata',
    ];

    protected $casts = [
        'usage_units' => 'integer',
        'occurred_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the merchant that owns the usage event.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Get the customer associated with the usage event.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the subscription associated with the usage event.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the subscription period associated with the usage event.
     */
    public function subscriptionPeriod(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPeriod::class);
    }
}