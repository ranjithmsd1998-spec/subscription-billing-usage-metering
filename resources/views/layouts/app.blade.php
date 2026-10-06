<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
     <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >
    <title>
        @yield('title', 'Subscription Billing')
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
        }

        body {
            font-family:
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                Arial,
                sans-serif;

            background: #f5f7fb;
            color: #172033;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        button,
        input,
        select,
        textarea {
            font: inherit;
        }

        .app {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR
        ========================= */

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #111827;
            color: #ffffff;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .brand {
            padding: 24px 20px;
            border-bottom: 1px solid #273244;
        }

        .brand-title {
            font-size: 21px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .brand-subtitle {
            margin-top: 5px;
            color: #9ca3af;
            font-size: 12px;
        }

        .navigation {
            padding: 20px 12px;
        }

        .nav-section-title {
            padding: 0 10px;
            margin-bottom: 8px;
            color: #6b7280;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 11px;
            width: 100%;
            padding: 11px 12px;
            margin-bottom: 4px;
            border-radius: 7px;
            color: #cbd5e1;
            font-size: 14px;
            transition:
                background 0.15s ease,
                color 0.15s ease;
        }

        .nav-link:hover {
            background: #1f2937;
            color: #ffffff;
        }

        .nav-link.active {
            background: #2563eb;
            color: #ffffff;
            font-weight: 600;
        }

        .nav-icon {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        /* =========================
           MAIN
        ========================= */

        .main-wrapper {
            width: calc(100% - 250px);
            margin-left: 250px;
            min-height: 100vh;
        }

        .topbar {
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
        }

        .topbar-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #ecfdf5;
            color: #047857;
            font-size: 12px;
            font-weight: 600;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #e0e7ff;
            color: #3730a3;
            font-size: 13px;
            font-weight: 700;
        }

        .content {
            padding: 30px 32px 40px;
        }

        /* =========================
           PAGE HEADER
        ========================= */

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .page-title {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
            font-weight: 700;
            color: #111827;
        }

        .page-description {
            margin: 7px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .page-date {
            color: #6b7280;
            font-size: 13px;
            white-space: nowrap;
        }

        /* =========================
           CARDS
        ========================= */

        .stats-grid {
            display: grid;
            grid-template-columns:
                repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 22px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 20px;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 13px;
            font-weight: 500;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 16px;
        }

        .stat-value {
            margin-top: 14px;
            color: #111827;
            font-size: 27px;
            line-height: 1;
            font-weight: 700;
        }

        .stat-description {
            margin-top: 8px;
            color: #9ca3af;
            font-size: 12px;
        }

        /* =========================
           GENERAL GRID
        ========================= */

        .grid-2 {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .grid-full {
            margin-bottom: 20px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            box-shadow:
                0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 18px 20px;
            border-bottom: 1px solid #eef0f4;
        }

        .card-title {
            margin: 0;
            color: #111827;
            font-size: 16px;
            font-weight: 650;
        }

        .card-subtitle {
            margin-top: 4px;
            color: #9ca3af;
            font-size: 12px;
        }

        .card-body {
            padding: 20px;
        }

        /* =========================
           USAGE
        ========================= */

        .usage-overview {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .usage-box {
            padding: 18px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fafbfc;
        }

        .usage-box-label {
            color: #6b7280;
            font-size: 12px;
        }

        .usage-box-value {
            margin-top: 7px;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }

        .growth-positive {
            color: #047857;
        }

        .growth-negative {
            color: #dc2626;
        }

        .growth-neutral {
            color: #6b7280;
        }

        .revenue-value {
            color: #2563eb;
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            padding: 12px 20px;
            background: #f8fafc;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table td {
            padding: 14px 20px;
            color: #374151;
            font-size: 13px;
            border-bottom: 1px solid #f0f2f5;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .data-table tbody tr:hover {
            background: #fafafa;
        }

        .customer-name {
            color: #111827;
            font-weight: 600;
        }

        .customer-code {
            margin-top: 3px;
            color: #9ca3af;
            font-size: 11px;
        }

        .number {
            font-weight: 600;
            color: #111827;
        }

        /* =========================
           BADGES
        ========================= */

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: #ecfdf5;
            color: #047857;
        }

        .badge-warning {
            background: #fffbeb;
            color: #b45309;
        }

        .badge-danger {
            background: #fef2f2;
            color: #b91c1c;
        }

        .badge-info {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .badge-neutral {
            background: #f1f5f9;
            color: #475569;
        }

        /* =========================
           ALERT
        ========================= */

        .alert-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .alert-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 14px;
            border: 1px solid #fee2e2;
            border-radius: 8px;
            background: #fffafa;
        }

        .alert-customer {
            color: #111827;
            font-weight: 600;
            font-size: 13px;
        }

        .alert-description {
            margin-top: 3px;
            color: #6b7280;
            font-size: 12px;
        }

        .drop-percentage {
            color: #dc2626;
            font-size: 14px;
            font-weight: 700;
            white-space: nowrap;
        }

        /* =========================
           EMPTY STATE
        ========================= */

        .empty-state {
            padding: 35px 20px;
            text-align: center;
            color: #9ca3af;
        }

        .empty-icon {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .empty-title {
            color: #64748b;
            font-size: 14px;
            font-weight: 600;
        }

        .empty-description {
            margin-top: 5px;
            font-size: 12px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1100px) {
            .stats-grid {
                grid-template-columns:
                    repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 850px) {
            .sidebar {
                width: 210px;
            }

            .main-wrapper {
                width: calc(100% - 210px);
                margin-left: 210px;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 650px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .app {
                display: block;
            }

            .main-wrapper {
                width: 100%;
                margin-left: 0;
            }

            .topbar {
                padding: 0 18px;
            }

            .content {
                padding: 22px 16px 30px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .usage-overview {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
            }

            .page-date {
                white-space: normal;
            }
        }
    </style>
</head>

<body>

<div class="app">

    {{-- Sidebar --}}
    <aside class="sidebar">

        <div class="brand">
            <div class="brand-title">
                Subscription Billing
            </div>

            <div class="brand-subtitle">
                Usage & Billing Platform
            </div>
        </div>

        <nav class="navigation">

            <div class="nav-section-title">
                Overview
            </div>

            <a
                href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >
                <span class="nav-icon">▦</span>
                <span>Dashboard</span>
            </a>

            <div
                class="nav-section-title"
                style="margin-top: 22px;"
            >
                Management
            </div>

            <a href="{{ route('merchants.index') }}"
                class="nav-link {{ request()->routeIs('merchants.*') ? 'active' : '' }}">
                <span class="nav-icon">▣</span>
                <span>Merchants</span>
            </a>

            <a href="{{ route('plans.index') }}" class="nav-link">
                <span class="nav-icon">◇</span>
                <span>Plans</span>
            </a>

            <a href="{{ route('customers.index') }}" class="nav-link">
                <span class="nav-icon">◉</span>
                <span>Customers</span>
            </a>

            <a href="{{ route('subscriptions.index') }}" class="nav-link">
                <span class="nav-icon">▤</span>
                <span>Subscriptions</span>
            </a>

            <a href="{{ route('subscription-periods.index') }}" class="nav-link">
                <span class="nav-icon">▤</span>
                <span>Subscriptions Period</span>
            </a>

            <a href="{{ route('usage-events.index') }}" class="nav-link">
                <span class="nav-icon">◌</span>
                <span>Usage Events</span>
            </a>

            <a href="{{ route('invoices.index') }}" class="nav-link">
                <span class="nav-icon">▧</span>
                <span>Invoices</span>
            </a>

            <a href="{{ route('daily-usage-aggregates.index') }}" class="nav-link">
                <span class="nav-icon">▧</span>
                <span>Daily Usage Aggregates</span>
            </a>

            <!-- <a href="#" class="nav-link">
                <span class="nav-icon">₹</span>
                <span>Payments</span>
            </a> -->

            <a href="{{ route('plan-changes.index') }}" class="nav-link">
                <span class="nav-icon">⇄</span>
                <span>Plan Changes</span>
            </a>

        </nav>

    </aside>

    {{-- Main --}}
    <div class="main-wrapper">

        <header class="topbar">

            <div class="topbar-title">
                @yield('page_heading', 'Dashboard')
            </div>

            <div class="topbar-right">

                <div class="status-badge">
                    <span class="status-dot"></span>
                    System Online
                </div>

                <div style="display:flex; align-items:center; gap:10px;">
                    <div>
                        <div style="font-size:13px; font-weight:600; color:#111827;">
                            {{ auth()->user()->name }}
                        </div>
                        <div style="font-size:11px; color:#6b7280; text-align:right;">
                            {{ ucfirst(auth()->user()->role) }}
                        </div>
                    </div>

                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>

                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button
                            type="submit"
                            style="border:1px solid #d1d5db; background:#fff; color:#374151; border-radius:7px; padding:7px 10px; cursor:pointer; font-size:12px;"
                        >
                            Logout
                        </button>
                    </form>
                </div>

            </div>

        </header>

        <main class="content">

            @yield('content')

        </main>

    </div>

</div>

</body>
</html>