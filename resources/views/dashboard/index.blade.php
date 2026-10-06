@extends('layouts.app')

@section('title', 'Dashboard | Subscription Billing')

@section('page_heading', 'Dashboard')

@section('content')

    @php
        $growth = $stats['usage_growth_percentage'];
    @endphp

    {{-- Page Header --}}
    <div class="page-header">

        <div>
            <h1 class="page-title">
                Dashboard
            </h1>

            <p class="page-description">
                Monitor usage, subscriptions and billing performance.
            </p>
        </div>

        <div class="page-date">
            {{ now()->format('d M Y, h:i A') }}
        </div>

    </div>


    {{-- Summary Cards --}}
    <div class="stats-grid">

        {{-- Merchants --}}
        <div class="stat-card">

            <div class="stat-card-header">

                <div class="stat-label">
                    Total Merchants
                </div>

                <div class="stat-icon">
                    ▣
                </div>

            </div>

            <div class="stat-value">
                {{ number_format($stats['total_merchants']) }}
            </div>

            <div class="stat-description">
                Registered merchants
            </div>

        </div>


        {{-- Customers --}}
        <div class="stat-card">

            <div class="stat-card-header">

                <div class="stat-label">
                    Total Customers
                </div>

                <div class="stat-icon">
                    ◉
                </div>

            </div>

            <div class="stat-value">
                {{ number_format($stats['total_customers']) }}
            </div>

            <div class="stat-description">
                Across all merchants
            </div>

        </div>


        {{-- Subscriptions --}}
        <div class="stat-card">

            <div class="stat-card-header">

                <div class="stat-label">
                    Active Subscriptions
                </div>

                <div class="stat-icon">
                    ▤
                </div>

            </div>

            <div class="stat-value">
                {{ number_format($stats['active_subscriptions']) }}
            </div>

            <div class="stat-description">
                Currently active
            </div>

        </div>


        {{-- Overage Revenue --}}
        <div class="stat-card">

            <div class="stat-card-header">

                <div class="stat-label">
                    Projected Overage
                </div>

                <div class="stat-icon">
                    ₹
                </div>

            </div>

            <div class="stat-value revenue-value">
                {{ number_format(
                    $stats['projected_overage_revenue'],
                    2
                ) }}
            </div>

            <div class="stat-description">
                Current billing cycle projection
            </div>

        </div>

    </div>


    {{-- Usage Overview + Growth --}}
    <div class="grid-2">

        {{-- Usage --}}
        <div class="card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Usage Overview
                    </h2>

                    <div class="card-subtitle">
                        Current month compared with previous month
                    </div>
                </div>

            </div>

            <div class="card-body">

                <div class="usage-overview">

                    <div class="usage-box">

                        <div class="usage-box-label">
                            Usage This Month
                        </div>

                        <div class="usage-box-value">
                            {{ number_format(
                                $stats['current_month_usage']
                            ) }}
                        </div>

                    </div>


                    <div class="usage-box">

                        <div class="usage-box-label">
                            Usage Last Month
                        </div>

                        <div class="usage-box-value">
                            {{ number_format(
                                $stats['previous_month_usage']
                            ) }}
                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- Growth --}}
        <div class="card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Usage Growth
                    </h2>

                    <div class="card-subtitle">
                        Month-over-month usage change
                    </div>
                </div>

            </div>

            <div class="card-body">

                @if ($growth === null)

                    <div class="usage-box">

                        <div class="usage-box-label">
                            Growth
                        </div>

                        <div class="usage-box-value growth-neutral">
                            N/A
                        </div>

                        <div class="stat-description">
                            No previous month usage available.
                        </div>

                    </div>

                @elseif ($growth > 0)

                    <div class="usage-box">

                        <div class="usage-box-label">
                            Growth
                        </div>

                        <div class="usage-box-value growth-positive">
                            +{{ number_format($growth, 1) }}%
                        </div>

                        <div class="stat-description">
                            Usage increased compared with last month.
                        </div>

                    </div>

                @elseif ($growth < 0)

                    <div class="usage-box">

                        <div class="usage-box-label">
                            Growth
                        </div>

                        <div class="usage-box-value growth-negative">
                            {{ number_format($growth, 1) }}%
                        </div>

                        <div class="stat-description">
                            Usage decreased compared with last month.
                        </div>

                    </div>

                @else

                    <div class="usage-box">

                        <div class="usage-box-label">
                            Growth
                        </div>

                        <div class="usage-box-value growth-neutral">
                            0.0%
                        </div>

                        <div class="stat-description">
                            Usage is unchanged from last month.
                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>


    {{-- Top Customers --}}
    <div class="grid-full">

        <div class="card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Top 5 Customers by Usage
                    </h2>

                    <div class="card-subtitle">
                        Highest usage during the current month
                    </div>
                </div>

                <span class="badge badge-info">
                    Current Month
                </span>

            </div>


            @if ($top_customers->isEmpty())

                <div class="empty-state">

                    <div class="empty-icon">
                        ◌
                    </div>

                    <div class="empty-title">
                        No usage data available
                    </div>

                    <div class="empty-description">
                        Usage events will appear here once customers start consuming units.
                    </div>

                </div>

            @else

                <div class="table-wrapper">

                    <table class="data-table">

                        <thead>
                            <tr>
                                <th>
                                    Customer
                                </th>

                                <th>
                                    Code
                                </th>

                                <th>
                                    Usage Units
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($top_customers as $item)

                                <tr>

                                    <td>

                                        <div class="customer-name">
                                            {{ $item->customer?->name ?? 'Unknown Customer' }}
                                        </div>

                                    </td>

                                    <td>

                                        <div class="customer-code">
                                            {{ $item->customer?->code ?? '-' }}
                                        </div>

                                    </td>

                                    <td>

                                        <span class="number">
                                            {{ number_format(
                                                (int) $item->total_usage_units
                                            ) }}
                                        </span>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>


    {{-- Usage Drop Alerts --}}
    <div class="grid-full">

        <div class="card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Usage Drop Alerts
                    </h2>

                    <div class="card-subtitle">
                        Customers whose usage dropped more than 50% month-over-month
                    </div>
                </div>

                @if ($usage_drop_customers->isNotEmpty())

                    <span class="badge badge-danger">
                        {{ $usage_drop_customers->count() }} Alert(s)
                    </span>

                @else

                    <span class="badge badge-success">
                        No Alerts
                    </span>

                @endif

            </div>


            @if ($usage_drop_customers->isEmpty())

                <div class="empty-state">

                    <div class="empty-icon">
                        ✓
                    </div>

                    <div class="empty-title">
                        No significant usage drops
                    </div>

                    <div class="empty-description">
                        No customer has experienced a usage drop greater than 50%.
                    </div>

                </div>

            @else

                <div class="card-body">

                    <div class="alert-list">

                        @foreach ($usage_drop_customers as $item)

                            <div class="alert-item">

                                <div>

                                    <div class="alert-customer">
                                        {{ $item['customer']->name }}
                                    </div>

                                    <div class="alert-description">
                                        Previous:
                                        {{ number_format(
                                            $item['previous_usage']
                                        ) }}

                                        &nbsp; → &nbsp;

                                        Current:
                                        {{ number_format(
                                            $item['current_usage']
                                        ) }}
                                    </div>

                                </div>

                                <div class="drop-percentage">
                                    -{{ number_format(
                                        $item['drop_percentage'],
                                        1
                                    ) }}%
                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- Recent Invoices --}}
    <div class="grid-full">

        <div class="card">

            <div class="card-header">

                <div>
                    <h2 class="card-title">
                        Recent Invoices
                    </h2>

                    <div class="card-subtitle">
                        Latest generated invoices
                    </div>
                </div>

            </div>


            @if ($recent_invoices->isEmpty())

                <div class="empty-state">

                    <div class="empty-icon">
                        ▧
                    </div>

                    <div class="empty-title">
                        No invoices found
                    </div>

                    <div class="empty-description">
                        Generated invoices will appear here.
                    </div>

                </div>

            @else

                <div class="table-wrapper">

                    <table class="data-table">

                        <thead>

                            <tr>

                                <th>
                                    Invoice
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Amount
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Date
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach ($recent_invoices as $invoice)

                                @php
                                    $status = strtolower(
                                        (string) ($invoice->status ?? '')
                                    );

                                    $statusClass = match ($status) {
                                        'paid' => 'badge-success',
                                        'issued' => 'badge-info',
                                        'pending' => 'badge-warning',
                                        'void',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-neutral',
                                    };
                                @endphp

                                <tr>

                                    <td>

                                        <span class="number">
                                            #{{ $invoice->id }}
                                        </span>

                                    </td>

                                    <td>

                                        <div class="customer-name">
                                            {{ $invoice->customer?->name ?? 'Unknown Customer' }}
                                        </div>

                                    </td>

                                    <td>

                                        <span class="number">
                                            {{ number_format(
                                                (float) (
                                                    $invoice->total_amount
                                                    ?? $invoice->grand_total
                                                    ?? 0
                                                ),
                                                2
                                            ) }}

                                            {{ $invoice->currency ?? '' }}
                                        </span>

                                    </td>

                                    <td>

                                        <span class="badge {{ $statusClass }}">
                                            {{ ucfirst(
                                                $status ?: 'unknown'
                                            ) }}
                                        </span>

                                    </td>

                                    <td>

                                        {{ optional(
                                            $invoice->created_at
                                        )->format('d M Y') }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

@endsection