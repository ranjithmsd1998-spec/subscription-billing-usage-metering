<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'billing_cycle',
        'base_price',
        'included_units',
        'overage_rate',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'base_price' => 'decimal:2',
        'included_units' => 'integer',
        'overage_rate' => 'decimal:6',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(
            Subscription::class,
            'subscription_id'
        );
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            Plan::class,
            'plan_id'
        );
    }

    public function usageEvents(): HasMany
    {
        return $this->hasMany(
            UsageEvent::class,
            'subscription_period_id'
        );
    }

    public function dailyUsageAggregates(): HasMany
    {
        return $this->hasMany(
            DailyUsageAggregate::class,
            'subscription_period_id'
        );
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(
            Invoice::class,
            'subscription_period_id'
        );
    }

    /**
     * Check whether a given date/time falls inside this subscription period.
     */
    public function containsDate(Carbon|string $date): bool
    {
        $date = $date instanceof Carbon
            ? $date
            : Carbon::parse($date);

        return $date->greaterThanOrEqualTo($this->starts_at)
            && $date->lessThanOrEqualTo($this->ends_at);
    }
}