<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPeriod;
use App\Models\User;
use App\Models\UsageEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Sanctum::actingAs(
            User::factory()->create([
                'role' => 'admin',
            ])
        );
    }

    private function createMerchant(
        array $overrides = []
    ): Merchant {
        return Merchant::create(array_merge([
            'name' => 'Test Merchant',
            'code' => 'MERCHANT-' . uniqid(),
            'email' => 'merchant-' . uniqid() . '@example.com',
            'status' => 'active',
            'timezone' => 'UTC',
            'currency' => 'USD',
        ], $overrides));
    }

    private function createCustomer(
        Merchant $merchant,
        array $overrides = []
    ): Customer {
        return Customer::create(array_merge([
            'merchant_id' => $merchant->id,
            'name' => 'Test Customer',
            'email' => 'customer-' . uniqid() . '@example.com',
            'code' => 'CUSTOMER-' . uniqid(),
            'phone' => '+1234567890',
            'status' => 'active',
            'metadata' => null,
        ], $overrides));
    }

    private function createPlan(
        Merchant $merchant,
        array $overrides = []
    ): Plan {
        return Plan::create(array_merge([
            'merchant_id' => $merchant->id,
            'name' => 'Basic Plan',
            'code' => 'PLAN-' . uniqid(),
            'description' => 'Basic subscription plan',
            'currency' => 'USD',
            'base_price' => 499.00,
            'billing_cycle' => 'monthly',
            'included_units' => 1000,
            'overage_rate' => 0.500000,
            'unit_name' => 'units',
            'status' => 'active',
        ], $overrides));
    }

    private function createSubscription(
        Merchant $merchant,
        Customer $customer,
        Plan $plan,
        array $overrides = []
    ): Subscription {
        return Subscription::create(array_merge([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'started_at' => '2026-10-01 00:00:00',
            'current_period_start' => '2026-10-01 00:00:00',
            'current_period_end' => '2026-10-31 23:59:59',
            'cancelled_at' => null,
        ], $overrides));
    }

    private function createSubscriptionPeriod(
        Subscription $subscription,
        Plan $plan,
        array $overrides = []
    ): SubscriptionPeriod {
        return SubscriptionPeriod::create(array_merge([
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'starts_at' => '2026-10-01 00:00:00',
            'ends_at' => '2026-10-31 23:59:59',
            'base_price' => 499.00,
            'included_units' => 1000,
            'overage_rate' => 0.500000,
            'billing_cycle' => 'monthly',
        ], $overrides));
    }

    private function createUsageEvent(
        Merchant $merchant,
        Customer $customer,
        Subscription $subscription,
        SubscriptionPeriod $period,
        array $overrides = []
    ): UsageEvent {
        return UsageEvent::create(array_merge([
            'merchant_id' => $merchant->id,
            'customer_id' => $customer->id,
            'subscription_id' => $subscription->id,
            'subscription_period_id' => $period->id,
            'idempotency_key' => 'EVENT-' . uniqid(),
            'usage_units' => 100,
            'unit_name' => 'units',
            'occurred_at' => '2026-10-10 10:00:00',
            'metadata' => null,
        ], $overrides));
    }

    /**
     * Generate an invoice for a full billing cycle.
     */
    public function test_admin_can_generate_full_cycle_invoice(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant,
            [
                'base_price' => 499.00,
                'included_units' => 1000,
                'overage_rate' => 0.50,
            ]
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas(
            'invoices',
            [
                'subscription_period_id' => $period->id,
                'base_amount' => '499.00',
                'proration_amount' => '0.00',
                'overage_amount' => '0.00',
                'subtotal' => '499.00',
                'tax_amount' => '0.00',
                'total' => '499.00',
                'status' => 'draft',
                'currency' => 'USD',
            ]
        );
    }

    /**
     * Usage within included units must not create overage.
     */
    public function test_usage_within_included_units_has_no_overage(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant,
            [
                'base_price' => 499.00,
                'included_units' => 1000,
                'overage_rate' => 0.50,
            ]
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period,
            [
                'usage_units' => 1000,
            ]
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas(
            'invoices',
            [
                'subscription_period_id' => $period->id,
                'base_amount' => '499.00',
                'overage_amount' => '0.00',
                'subtotal' => '499.00',
                'total' => '499.00',
            ]
        );
    }

    /**
     * Usage above included units must generate overage.
     */
    public function test_usage_above_included_units_calculates_overage(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant,
            [
                'base_price' => 499.00,
                'included_units' => 1000,
                'overage_rate' => 0.50,
            ]
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $this->createUsageEvent(
            $merchant,
            $customer,
            $subscription,
            $period,
            [
                'usage_units' => 1500,
            ]
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $response->assertStatus(201);

        /*
         * Usage = 1500
         * Included = 1000
         * Overage = 500
         * Rate = 0.50
         *
         * Overage amount = 500 × 0.50 = 250
         *
         * Total = 499 + 250 = 749
         */
        $this->assertDatabaseHas(
            'invoices',
            [
                'subscription_period_id' => $period->id,
                'base_amount' => '499.00',
                'overage_amount' => '250.00',
                'subtotal' => '749.00',
                'total' => '749.00',
            ]
        );
    }

    /**
     * Tax must be calculated on subtotal.
     */
    public function test_tax_is_calculated_on_subtotal(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant,
            [
                'base_price' => 500.00,
                'included_units' => 1000,
                'overage_rate' => 0.50,
            ]
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan,
            [
                'base_price' => 500.00,
            ]
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 18,
            ]
        );

        $response->assertStatus(201);

        /*
         * Subtotal = 500
         * Tax = 18% = 90
         * Total = 590
         */
        $this->assertDatabaseHas(
            'invoices',
            [
                'subscription_period_id' => $period->id,
                'subtotal' => '500.00',
                'tax_rate' => '18.00',
                'tax_amount' => '90.00',
                'total' => '590.00',
            ]
        );
    }

    /**
     * Partial subscription period must be prorated.
     */
    public function test_partial_period_calculates_proration(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant,
            [
                'base_price' => 999.00,
                'included_units' => 5000,
                'overage_rate' => 0.25,
            ]
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan,
            [
                'starts_at' => '2026-10-01 00:00:00',
                'ends_at' => '2026-10-15 23:59:59',
                'base_price' => 999.00,
                'included_units' => 5000,
                'overage_rate' => 0.25,
            ]
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $response->assertStatus(201);

        /*
         * October has 31 days.
         *
         * Partial period:
         * 01 Oct -> 15 Oct
         *
         * Actual period = 14 days.
         *
         * 999 × 14 / 31 = 451.16
         */
        $this->assertDatabaseHas(
            'invoices',
            [
                'subscription_period_id' => $period->id,
                'base_amount' => '0.00',
                'proration_amount' => '451.16',
                'subtotal' => '451.16',
                'total' => '451.16',
            ]
        );
    }

    /**
     * Zero usage must still bill the applicable base amount.
     */
    public function test_zero_usage_bills_base_price_without_overage(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant,
            [
                'base_price' => 499.00,
                'included_units' => 1000,
                'overage_rate' => 0.50,
            ]
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas(
            'invoices',
            [
                'subscription_period_id' => $period->id,
                'base_amount' => '499.00',
                'overage_amount' => '0.00',
                'subtotal' => '499.00',
                'total' => '499.00',
            ]
        );
    }

    /**
     * Duplicate invoice generation must be rejected.
     */
    public function test_duplicate_invoice_for_same_period_is_rejected(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $firstResponse = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $firstResponse->assertStatus(201);

        $secondResponse = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $secondResponse
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'subscription_period_id',
            ]);

        $this->assertDatabaseCount(
            'invoices',
            1
        );
    }

    /**
     * Invalid tax rate must be rejected.
     */
    public function test_tax_rate_above_100_is_rejected(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 101,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'tax_rate',
            ]);
    }

    /**
     * Negative tax rate must be rejected.
     */
    public function test_negative_tax_rate_is_rejected(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => -1,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'tax_rate',
            ]);
    }

    /**
     * Missing subscription period must be rejected.
     */
    public function test_missing_subscription_period_is_rejected(): void
    {
        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'tax_rate' => 0,
            ]
        );

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'subscription_period_id',
            ]);
    }

    /**
     * Invoice number must be unique per merchant.
     */
    public function test_invoice_number_is_generated_for_merchant(): void
    {
        $merchant = $this->createMerchant();

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJsonPath(
                'data.invoice_number',
                'INV-' . $merchant->id . '-000001'
            );
    }

    /**
     * Invoice currency must use the plan currency snapshot.
     */
    public function test_invoice_uses_plan_currency(): void
    {
        $merchant = $this->createMerchant([
            'currency' => 'INR',
        ]);

        $customer = $this->createCustomer(
            $merchant
        );

        $plan = $this->createPlan(
            $merchant,
            [
                'currency' => 'INR',
            ]
        );

        $subscription = $this->createSubscription(
            $merchant,
            $customer,
            $plan
        );

        $period = $this->createSubscriptionPeriod(
            $subscription,
            $plan
        );

        $response = $this->postJson(
            '/api/invoices/generate',
            [
                'subscription_period_id' => $period->id,
                'tax_rate' => 0,
            ]
        );

        $response->assertStatus(201);

        $this->assertDatabaseHas(
            'invoices',
            [
                'subscription_period_id' => $period->id,
                'currency' => 'INR',
            ]
        );
    }
}