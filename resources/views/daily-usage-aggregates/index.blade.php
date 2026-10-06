@extends('layouts.app')

@section('title', 'Daily Usage Aggregates')
@section('page_heading', 'Daily Usage Aggregates')

@section('content')

<style>
    .aggregate-page {
        padding-bottom: 30px;
    }

    .aggregate-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 24px;
    }

    .aggregate-title {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #172033;
    }

    .aggregate-subtitle {
        margin: 6px 0 0;
        color: #7a8496;
        font-size: 14px;
    }

    .aggregate-card {
        background: #ffffff;
        border: 1px solid #e5e9f0;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(23, 32, 51, 0.04);
    }

    .filter-card {
        padding: 20px;
        margin-bottom: 20px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1.5fr 1.5fr 1fr auto;
        gap: 14px;
        align-items: end;
    }

    .filter-group {
        min-width: 0;
    }

    .filter-label {
        display: block;
        margin-bottom: 7px;
        font-size: 13px;
        font-weight: 600;
        color: #39445a;
    }

    .filter-control {
        width: 100%;
        height: 42px;
        border: 1px solid #d9dee8;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 14px;
        color: #263248;
        background: #fff;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .filter-control:focus {
        border-color: #4f7cff;
        box-shadow: 0 0 0 3px rgba(79, 124, 255, .10);
    }

    .filter-actions {
        display: flex;
        gap: 8px;
    }

    .btn-primary-custom {
        height: 42px;
        border: 0;
        border-radius: 8px;
        padding: 0 17px;
        background: #315efb;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary-custom:hover {
        background: #244bd1;
    }

    .btn-secondary-custom {
        height: 42px;
        border: 1px solid #d9dee8;
        border-radius: 8px;
        padding: 0 17px;
        background: #fff;
        color: #4c566a;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-secondary-custom:hover {
        background: #f7f8fa;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    .summary-card {
        padding: 18px 20px;
    }

    .summary-label {
        color: #7a8496;
        font-size: 13px;
        margin-bottom: 7px;
    }

    .summary-value {
        color: #172033;
        font-size: 24px;
        font-weight: 700;
    }

    .table-card {
        overflow: hidden;
    }

    .table-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e9edf3;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-header-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #172033;
    }

    .selected-period-label {
        color: #7a8496;
        font-size: 13px;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .aggregate-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .aggregate-table thead th {
        background: #f8f9fb;
        color: #667085;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
        padding: 13px 16px;
        border-bottom: 1px solid #e5e9f0;
        white-space: nowrap;
        text-align: left;
    }

    .aggregate-table tbody td {
        padding: 15px 16px;
        border-bottom: 1px solid #edf0f4;
        color: #344054;
        font-size: 14px;
        vertical-align: middle;
    }

    .aggregate-table tbody tr:hover {
        background: #fafbfc;
    }

    .id-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        border-radius: 6px;
        background: #f1f4f9;
        color: #344054;
        font-size: 12px;
        font-weight: 600;
    }

    .usage-value {
        font-weight: 700;
        color: #172033;
    }

    .event-value {
        font-weight: 600;
        color: #475467;
    }

    .view-btn {
        height: 34px;
        padding: 0 12px;
        border: 1px solid #d5dcf0;
        border-radius: 7px;
        background: #fff;
        color: #315efb;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }

    .view-btn:hover {
        background: #f3f6ff;
    }

    .table-footer {
        padding: 15px 20px;
        border-top: 1px solid #e9edf3;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
    }

    .pagination-info {
        color: #7a8496;
        font-size: 13px;
    }

    .pagination {
        display: flex;
        gap: 5px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .pagination button {
        min-width: 34px;
        height: 34px;
        border: 1px solid #d9dee8;
        border-radius: 7px;
        background: #fff;
        color: #475467;
        font-size: 13px;
        cursor: pointer;
    }

    .pagination button:hover {
        background: #f5f7fa;
    }

    .pagination button.active {
        background: #315efb;
        border-color: #315efb;
        color: #fff;
    }

    .pagination button:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .empty-state {
        padding: 55px 20px !important;
        text-align: center;
        color: #98a2b3 !important;
    }

    .empty-state-title {
        margin-top: 10px;
        color: #667085;
        font-size: 15px;
        font-weight: 600;
    }

    .empty-state-text {
        margin-top: 5px;
        color: #98a2b3;
        font-size: 13px;
    }

    .loading-state {
        padding: 45px 20px !important;
        text-align: center;
        color: #7a8496 !important;
    }

    .spinner {
        width: 24px;
        height: 24px;
        border: 3px solid #e1e6ef;
        border-top-color: #315efb;
        border-radius: 50%;
        animation: spin .7s linear infinite;
        margin: 0 auto 10px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .alert-custom {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 14px;
        display: none;
    }

    .alert-error {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #be123c;
    }

    .alert-success {
        background: #ecfdf3;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .50);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        padding: 20px;
    }

    .modal-overlay.show {
        display: flex;
    }

    .details-modal {
        width: 100%;
        max-width: 720px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, .20);
    }

    .details-modal-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e9edf3;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .details-modal-title {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #172033;
    }

    .modal-close {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 6px;
        background: #f3f4f6;
        color: #667085;
        cursor: pointer;
        font-size: 18px;
    }

    .details-modal-body {
        padding: 20px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .detail-item {
        background: #f8f9fb;
        border: 1px solid #edf0f4;
        border-radius: 8px;
        padding: 13px;
    }

    .detail-label {
        color: #7a8496;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .detail-value {
        color: #172033;
        font-size: 14px;
        font-weight: 600;
        word-break: break-word;
    }

    .details-modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e9edf3;
        display: flex;
        justify-content: flex-end;
    }

    @media (max-width: 1100px) {
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }

        .filter-actions {
            align-self: end;
        }
    }

    @media (max-width: 768px) {
        .aggregate-header {
            flex-direction: column;
            gap: 10px;
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            width: 100%;
        }

        .filter-actions button {
            flex: 1;
        }

        .summary-grid {
            grid-template-columns: 1fr;
        }

        .table-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>


<div class="aggregate-page">

    {{-- Header --}}
    <div class="aggregate-header">
        <div>
            <h2 class="aggregate-title">
                Daily Usage Aggregates
            </h2>

            <p class="aggregate-subtitle">
                Daily summarized usage generated from usage events.
            </p>
        </div>
    </div>


    {{-- Alert --}}
    <div
        id="alertContainer"
        class="alert-custom"
    ></div>


    {{-- Filters --}}
    <div class="aggregate-card filter-card">

        <div class="filter-grid">

            {{-- Subscription Period --}}
            <div class="filter-group">

                <label
                    for="subscriptionPeriodInput"
                    class="filter-label"
                >
                    Subscription Period
                </label>

                <select
                    id="subscriptionPeriodInput"
                    class="filter-control"
                >
                    <option value="">
                        Select subscription period
                    </option>
                </select>

            </div>


            {{-- Search --}}
            <div class="filter-group">

                <label
                    for="searchInput"
                    class="filter-label"
                >
                    Search
                </label>

                <input
                    type="text"
                    id="searchInput"
                    class="filter-control"
                    placeholder="Customer, date, subscription..."
                >

            </div>


            {{-- Usage Date --}}
            <div class="filter-group">

                <label
                    for="usageDateInput"
                    class="filter-label"
                >
                    Usage Date
                </label>

                <input
                    type="date"
                    id="usageDateInput"
                    class="filter-control"
                >

            </div>


            {{-- Actions --}}
            <div class="filter-actions">

                <button
                    type="button"
                    id="searchButton"
                    class="btn-primary-custom"
                >
                    Search
                </button>

                <button
                    type="button"
                    id="resetButton"
                    class="btn-secondary-custom"
                >
                    Reset
                </button>

            </div>

        </div>

    </div>


    {{-- Summary --}}
    <div class="summary-grid">

        <div class="aggregate-card summary-card">

            <div class="summary-label">
                Total Records
            </div>

            <div
                id="totalRecords"
                class="summary-value"
            >
                0
            </div>

        </div>


        <div class="aggregate-card summary-card">

            <div class="summary-label">
                Total Usage
            </div>

            <div
                id="totalUsage"
                class="summary-value"
            >
                0
            </div>

        </div>


        <div class="aggregate-card summary-card">

            <div class="summary-label">
                Total Events
            </div>

            <div
                id="totalEvents"
                class="summary-value"
            >
                0
            </div>

        </div>

    </div>


    {{-- Table --}}
    <div class="aggregate-card table-card">

        <div class="table-header">

            <div>

                <h3 class="table-header-title">
                    Daily Usage
                </h3>

                <div
                    id="selectedPeriodLabel"
                    class="selected-period-label"
                >
                    Select a subscription period to view aggregates.
                </div>

            </div>


            <div>

                <select
                    id="perPageSelect"
                    class="filter-control"
                    style="width: 90px;"
                >
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>

            </div>

        </div>


        <div class="table-wrapper">

            <table class="aggregate-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Usage Date</th>

                        <th>Customer</th>

                        <th>Subscription</th>

                        <th>Period</th>

                        <th>Total Usage</th>

                        <th>Events</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody id="aggregateTableBody">

                    <tr>

                        <td
                            colspan="8"
                            class="empty-state"
                        >

                            <div class="empty-state-title">
                                Select a Subscription Period
                            </div>

                            <div class="empty-state-text">
                                Choose a subscription period above to view daily usage.
                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        {{-- Footer --}}
        <div class="table-footer">

            <div
                id="paginationInfo"
                class="pagination-info"
            >
                Showing 0 records
            </div>

            <div
                id="pagination"
                class="pagination"
            ></div>

        </div>

    </div>

</div>


{{-- Details Modal --}}
<div
    id="detailsModal"
    class="modal-overlay"
>

    <div class="details-modal">

        <div class="details-modal-header">

            <h3 class="details-modal-title">
                Daily Usage Aggregate Details
            </h3>

            <button
                type="button"
                id="closeDetailsButton"
                class="modal-close"
            >
                ×
            </button>

        </div>


        <div class="details-modal-body">

            <div
                id="detailsLoading"
                class="loading-state"
                style="display: none;"
            >
                <div class="spinner"></div>
                Loading aggregate details...
            </div>


            <div
                id="detailsContent"
                class="details-grid"
            >

                <div class="detail-item">
                    <div class="detail-label">
                        Aggregate ID
                    </div>

                    <div
                        id="detailId"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Usage Date
                    </div>

                    <div
                        id="detailUsageDate"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Merchant ID
                    </div>

                    <div
                        id="detailMerchantId"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Customer
                    </div>

                    <div
                        id="detailCustomer"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Subscription ID
                    </div>

                    <div
                        id="detailSubscriptionId"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Subscription Period ID
                    </div>

                    <div
                        id="detailPeriodId"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Total Usage Units
                    </div>

                    <div
                        id="detailTotalUsage"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Event Count
                    </div>

                    <div
                        id="detailEventCount"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Created At
                    </div>

                    <div
                        id="detailCreatedAt"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>


                <div class="detail-item">
                    <div class="detail-label">
                        Updated At
                    </div>

                    <div
                        id="detailUpdatedAt"
                        class="detail-value"
                    >
                        -
                    </div>
                </div>

            </div>

        </div>


        <div class="details-modal-footer">

            <button
                type="button"
                id="closeDetailsButtonBottom"
                class="btn-secondary-custom"
            >
                Close
            </button>

        </div>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const periodInput =
        document.getElementById(
            'subscriptionPeriodInput'
        );

    const searchInput =
        document.getElementById(
            'searchInput'
        );

    const usageDateInput =
        document.getElementById(
            'usageDateInput'
        );

    const perPageSelect =
        document.getElementById(
            'perPageSelect'
        );

    const searchButton =
        document.getElementById(
            'searchButton'
        );

    const resetButton =
        document.getElementById(
            'resetButton'
        );

    const tableBody =
        document.getElementById(
            'aggregateTableBody'
        );

    const pagination =
        document.getElementById(
            'pagination'
        );

    const paginationInfo =
        document.getElementById(
            'paginationInfo'
        );

    const totalRecords =
        document.getElementById(
            'totalRecords'
        );

    const totalUsage =
        document.getElementById(
            'totalUsage'
        );

    const totalEvents =
        document.getElementById(
            'totalEvents'
        );

    const selectedPeriodLabel =
        document.getElementById(
            'selectedPeriodLabel'
        );

    const alertContainer =
        document.getElementById(
            'alertContainer'
        );

    const detailsModal =
        document.getElementById(
            'detailsModal'
        );

    const detailsLoading =
        document.getElementById(
            'detailsLoading'
        );

    const detailsContent =
        document.getElementById(
            'detailsContent'
        );


    let currentPage = 1;


    /*
    |--------------------------------------------------------------------------
    | CSRF
    |--------------------------------------------------------------------------
    */

    const csrfToken =
        document
            .querySelector(
                'meta[name="csrf-token"]'
            )
            ?.getAttribute('content');


    /*
    |--------------------------------------------------------------------------
    | Request Helper
    |--------------------------------------------------------------------------
    */

    async function request(
        url,
        options = {}
    ) {

        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] =
                csrfToken;
        }

        const response =
            await fetch(url, {
                credentials: 'same-origin',
                ...options,
                headers: {
                    ...headers,
                    ...(options.headers || {})
                }
            });


        let result = null;

        try {

            result =
                await response.json();

        } catch (error) {

            throw new Error(
                `Invalid server response. HTTP ${response.status}`
            );
        }


        if (
            !response.ok ||
            result?.success === false
        ) {

            const error =
                new Error(
                    result?.message ||
                    'Something went wrong.'
                );

            error.status =
                response.status;

            error.data =
                result;

            throw error;
        }


        return result;
    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return '';
        }

        return String(value)
            .replace(
                /&/g,
                '&amp;'
            )
            .replace(
                /</g,
                '&lt;'
            )
            .replace(
                />/g,
                '&gt;'
            )
            .replace(
                /"/g,
                '&quot;'
            )
            .replace(
                /'/g,
                '&#039;'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Number
    |--------------------------------------------------------------------------
    */

    function formatNumber(value) {

        return Number(
            value || 0
        ).toLocaleString(
            'en-IN'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Date
    |--------------------------------------------------------------------------
    */

    function formatDate(value) {

        if (!value) {
            return '-';
        }

        const date =
            new Date(value);

        if (
            Number.isNaN(
                date.getTime()
            )
        ) {
            return value;
        }

        return date.toLocaleDateString(
            'en-IN',
            {
                year: 'numeric',
                month: 'short',
                day: '2-digit'
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Date Time
    |--------------------------------------------------------------------------
    */

    function formatDateTime(value) {

        if (!value) {
            return '-';
        }

        const date =
            new Date(value);

        if (
            Number.isNaN(
                date.getTime()
            )
        ) {
            return value;
        }

        return date.toLocaleString(
            'en-IN',
            {
                year: 'numeric',
                month: 'short',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Alert
    |--------------------------------------------------------------------------
    */

    function showAlert(
        message,
        type = 'error'
    ) {

        alertContainer.className =
            `alert-custom ${
                type === 'success'
                    ? 'alert-success'
                    : 'alert-error'
            }`;

        alertContainer.textContent =
            message;

        alertContainer.style.display =
            'block';
    }


    function hideAlert() {

        alertContainer.style.display =
            'none';

        alertContainer.textContent =
            '';
    }


    /*
    |--------------------------------------------------------------------------
    | Load Subscription Periods
    |--------------------------------------------------------------------------
    */

    async function loadSubscriptionPeriods() {

        periodInput.innerHTML = `
            <option value="">
                Loading subscription periods...
            </option>
        `;

        try {

            const result =
                await request(
                    '/subscription-periods/data?per_page=100'
                );

            const data =
                result?.data || {};

            const items =
                Array.isArray(data.items)
                    ? data.items
                    : Array.isArray(data.data)
                        ? data.data
                        : Array.isArray(data)
                            ? data
                            : [];


            periodInput.innerHTML = `
                <option value="">
                    Select subscription period
                </option>
            `;


            items.forEach(function (period) {

                const option =
                    document.createElement(
                        'option'
                    );

                option.value =
                    period.id;

                const startsAt =
                    formatDate(
                        period.starts_at
                    );

                const endsAt =
                    formatDate(
                        period.ends_at
                    );

                const subscriptionId =
                    period.subscription_id
                    ?? period.subscription?.id
                    ?? '-';

                option.textContent =
                    `Period #${period.id} — Subscription #${subscriptionId} — ${startsAt} to ${endsAt}`;

                periodInput.appendChild(
                    option
                );

            });

        } catch (error) {

            periodInput.innerHTML = `
                <option value="">
                    Unable to load periods
                </option>
            `;

            showAlert(
                error.message ||
                'Unable to load subscription periods.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Loading State
    |--------------------------------------------------------------------------
    */

    function showTableLoading() {

        tableBody.innerHTML = `
            <tr>
                <td
                    colspan="8"
                    class="loading-state"
                >
                    <div class="spinner"></div>
                    Loading daily usage...
                </td>
            </tr>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | Empty State
    |--------------------------------------------------------------------------
    */

    function showEmptyState(
        message = 'No daily usage aggregates found.'
    ) {

        tableBody.innerHTML = `
            <tr>
                <td
                    colspan="8"
                    class="empty-state"
                >

                    <div class="empty-state-title">
                        ${escapeHtml(message)}
                    </div>

                    <div class="empty-state-text">
                        Daily usage will appear here after aggregation.
                    </div>

                </td>
            </tr>
        `;
    }


    /*
    |--------------------------------------------------------------------------
    | Load Aggregates
    |--------------------------------------------------------------------------
    */

    async function loadAggregates(
        page = 1
    ) {

        const periodId =
            periodInput.value;


        if (!periodId) {

            showEmptyState(
                'Select a Subscription Period'
            );

            pagination.innerHTML =
                '';

            paginationInfo.textContent =
                'Showing 0 records';

            totalRecords.textContent =
                '0';

            totalUsage.textContent =
                '0';

            totalEvents.textContent =
                '0';

            selectedPeriodLabel.textContent =
                'Select a subscription period to view aggregates.';

            return;
        }


        currentPage =
            page;

        hideAlert();

        showTableLoading();


        const params =
            new URLSearchParams();

        params.set(
            'subscription_period_id',
            periodId
        );

        params.set(
            'page',
            page
        );

        params.set(
            'per_page',
            perPageSelect.value
        );


        const search =
            searchInput.value.trim();

        if (search !== '') {

            params.set(
                'search',
                search
            );
        }


        const usageDate =
            usageDateInput.value;

        if (usageDate !== '') {

            params.set(
                'usage_date',
                usageDate
            );
        }


        try {

            const result =
                await request(
                    `/daily-usage-aggregates/data?${params.toString()}`
                );

            const data =
                result?.data || {};

            const items =
                Array.isArray(data.items)
                    ? data.items
                    : [];


            renderTable(
                items
            );

            renderPagination(
                data
            );

            updateSummary(
                items,
                data
            );


            const selectedOption =
                periodInput.options[
                    periodInput.selectedIndex
                ];

            selectedPeriodLabel.textContent =
                selectedOption?.textContent ||
                'Selected subscription period.';

        } catch (error) {

            tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="8"
                        class="empty-state"
                    >

                        <div
                            class="empty-state-title"
                            style="color:#be123c;"
                        >
                            ${escapeHtml(
                                error.message ||
                                'Unable to load daily usage aggregates.'
                            )}
                        </div>

                    </td>
                </tr>
            `;

            pagination.innerHTML =
                '';

            paginationInfo.textContent =
                'Showing 0 records';

            totalRecords.textContent =
                '0';

            totalUsage.textContent =
                '0';

            totalEvents.textContent =
                '0';

            showAlert(
                error.message ||
                'Unable to load daily usage aggregates.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Render Table
    |--------------------------------------------------------------------------
    */

    function renderTable(
        items
    ) {

        if (!items.length) {

            showEmptyState();

            return;
        }


        tableBody.innerHTML =
            items.map(
                function (aggregate) {

                    const customerName =
                        aggregate.customer?.name
                        ||
                        aggregate.subscription?.customer?.name
                        ||
                        '-';


                    const customerCode =
                        aggregate.customer?.code
                        ||
                        aggregate.subscription?.customer?.code
                        ||
                        '';


                    return `
                        <tr>

                            <td>
                                <span class="id-badge">
                                    #${escapeHtml(
                                        aggregate.id
                                    )}
                                </span>
                            </td>


                            <td>
                                ${escapeHtml(
                                    formatDate(
                                        aggregate.usage_date
                                    )
                                )}
                            </td>


                            <td>

                                <div style="font-weight:600;color:#172033;">
                                    ${escapeHtml(
                                        customerName
                                    )}
                                </div>

                                ${
                                    customerCode
                                        ? `
                                            <div
                                                style="
                                                    font-size:12px;
                                                    color:#98a2b3;
                                                    margin-top:3px;
                                                "
                                            >
                                                ${escapeHtml(
                                                    customerCode
                                                )}
                                            </div>
                                          `
                                        : ''
                                }

                            </td>


                            <td>
                                <span class="id-badge">
                                    #${escapeHtml(
                                        aggregate.subscription_id
                                    )}
                                </span>
                            </td>


                            <td>
                                <span class="id-badge">
                                    #${escapeHtml(
                                        aggregate.subscription_period_id
                                    )}
                                </span>
                            </td>


                            <td>
                                <span class="usage-value">
                                    ${escapeHtml(
                                        formatNumber(
                                            aggregate.total_usage_units
                                        )
                                    )}
                                </span>
                            </td>


                            <td>
                                <span class="event-value">
                                    ${escapeHtml(
                                        formatNumber(
                                            aggregate.event_count
                                        )
                                    )}
                                </span>
                            </td>


                            <td>

                                <button
                                    type="button"
                                    class="view-btn"
                                    data-id="${escapeHtml(
                                        aggregate.id
                                    )}"
                                >
                                    View
                                </button>

                            </td>

                        </tr>
                    `;
                }
            )
            .join('');
    }


    /*
    |--------------------------------------------------------------------------
    | Summary
    |--------------------------------------------------------------------------
    */

    function updateSummary(
        items,
        data
    ) {

        totalRecords.textContent =
            formatNumber(
                data.total || 0
            );


        let usage = 0;
        let events = 0;


        items.forEach(
            function (item) {

                usage += Number(
                    item.total_usage_units || 0
                );

                events += Number(
                    item.event_count || 0
                );

            }
        );


        totalUsage.textContent =
            formatNumber(
                usage
            );

        totalEvents.textContent =
            formatNumber(
                events
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    function renderPagination(
        data
    ) {

        pagination.innerHTML =
            '';


        const current =
            Number(
                data.current_page || 1
            );

        const last =
            Number(
                data.last_page || 1
            );

        const total =
            Number(
                data.total || 0
            );

        const perPage =
            Number(
                data.per_page || 10
            );


        if (total === 0) {

            paginationInfo.textContent =
                'Showing 0 records';

            return;
        }


        const from =
            ((current - 1) * perPage) + 1;

        const to =
            Math.min(
                current * perPage,
                total
            );


        paginationInfo.textContent =
            `Showing ${from} to ${to} of ${total} records`;


        /*
        |--------------------------------------------------------------------------
        | Previous
        |--------------------------------------------------------------------------
        */

        const previous =
            document.createElement(
                'button'
            );

        previous.textContent =
            '‹';

        previous.disabled =
            current <= 1;

        previous.dataset.page =
            current - 1;

        pagination.appendChild(
            previous
        );


        /*
        |--------------------------------------------------------------------------
        | Page Numbers
        |--------------------------------------------------------------------------
        */

        let start =
            Math.max(
                1,
                current - 2
            );

        let end =
            Math.min(
                last,
                current + 2
            );


        if (start > 1) {

            appendPaginationButton(
                1,
                current
            );

            if (start > 2) {

                const dots =
                    document.createElement(
                        'span'
                    );

                dots.textContent =
                    '...';

                dots.style.padding =
                    '7px 5px';

                dots.style.color =
                    '#98a2b3';

                pagination.appendChild(
                    dots
                );
            }
        }


        for (
            let page = start;
            page <= end;
            page++
        ) {

            appendPaginationButton(
                page,
                current
            );
        }


        if (end < last) {

            if (end < last - 1) {

                const dots =
                    document.createElement(
                        'span'
                    );

                dots.textContent =
                    '...';

                dots.style.padding =
                    '7px 5px';

                dots.style.color =
                    '#98a2b3';

                pagination.appendChild(
                    dots
                );
            }

            appendPaginationButton(
                last,
                current
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Next
        |--------------------------------------------------------------------------
        */

        const next =
            document.createElement(
                'button'
            );

        next.textContent =
            '›';

        next.disabled =
            current >= last;

        next.dataset.page =
            current + 1;

        pagination.appendChild(
            next
        );
    }


    function appendPaginationButton(
        page,
        current
    ) {

        const button =
            document.createElement(
                'button'
            );

        button.textContent =
            page;

        button.dataset.page =
            page;

        if (page === current) {

            button.classList.add(
                'active'
            );
        }

        pagination.appendChild(
            button
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Pagination Click
    |--------------------------------------------------------------------------
    */

    pagination.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    'button'
                );

            if (!button) {
                return;
            }

            if (button.disabled) {
                return;
            }

            const page =
                Number(
                    button.dataset.page
                );

            if (!page || page < 1) {
                return;
            }

            loadAggregates(
                page
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | View Details
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '.view-btn'
                );

            if (!button) {
                return;
            }

            const id =
                button.dataset.id;

            showDetails(
                id
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Show Details
    |--------------------------------------------------------------------------
    */

    async function showDetails(
        id
    ) {

        detailsModal.classList.add(
            'show'
        );

        detailsLoading.style.display =
            'block';

        detailsContent.style.display =
            'none';


        try {

            const result =
                await request(
                    `/daily-usage-aggregates/${encodeURIComponent(id)}`
                );

            const aggregate =
                result?.data;


            if (!aggregate) {

                throw new Error(
                    'Daily usage aggregate not found.'
                );
            }


            const customerName =
                aggregate.customer?.name
                ||
                aggregate.subscription?.customer?.name
                ||
                '-';


            document.getElementById(
                'detailId'
            ).textContent =
                aggregate.id ?? '-';


            document.getElementById(
                'detailUsageDate'
            ).textContent =
                formatDate(
                    aggregate.usage_date
                );


            document.getElementById(
                'detailMerchantId'
            ).textContent =
                aggregate.merchant_id ?? '-';


            document.getElementById(
                'detailCustomer'
            ).textContent =
                customerName;


            document.getElementById(
                'detailSubscriptionId'
            ).textContent =
                aggregate.subscription_id ?? '-';


            document.getElementById(
                'detailPeriodId'
            ).textContent =
                aggregate.subscription_period_id ?? '-';


            document.getElementById(
                'detailTotalUsage'
            ).textContent =
                formatNumber(
                    aggregate.total_usage_units
                );


            document.getElementById(
                'detailEventCount'
            ).textContent =
                formatNumber(
                    aggregate.event_count
                );


            document.getElementById(
                'detailCreatedAt'
            ).textContent =
                formatDateTime(
                    aggregate.created_at
                );


            document.getElementById(
                'detailUpdatedAt'
            ).textContent =
                formatDateTime(
                    aggregate.updated_at
                );


            detailsLoading.style.display =
                'none';

            detailsContent.style.display =
                'grid';

        } catch (error) {

            detailsLoading.style.display =
                'none';

            detailsContent.style.display =
                'block';

            detailsContent.innerHTML = `
                <div
                    class="alert-custom alert-error"
                    style="display:block;"
                >
                    ${escapeHtml(
                        error.message ||
                        'Unable to load aggregate details.'
                    )}
                </div>
            `;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Close Details
    |--------------------------------------------------------------------------
    */

    function closeDetails() {

        detailsModal.classList.remove(
            'show'
        );
    }


    document.getElementById(
        'closeDetailsButton'
    ).addEventListener(
        'click',
        closeDetails
    );


    document.getElementById(
        'closeDetailsButtonBottom'
    ).addEventListener(
        'click',
        closeDetails
    );


    detailsModal.addEventListener(
        'click',
        function (event) {

            if (
                event.target ===
                detailsModal
            ) {
                closeDetails();
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Period Change
    |--------------------------------------------------------------------------
    */

    periodInput.addEventListener(
        'change',
        function () {

            searchInput.value =
                '';

            usageDateInput.value =
                '';

            currentPage =
                1;

            loadAggregates(
                1
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    searchButton.addEventListener(
        'click',
        function () {

            loadAggregates(
                1
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Enter Search
    |--------------------------------------------------------------------------
    */

    searchInput.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key ===
                'Enter'
            ) {

                event.preventDefault();

                loadAggregates(
                    1
                );
            }
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Usage Date
    |--------------------------------------------------------------------------
    */

    usageDateInput.addEventListener(
        'change',
        function () {

            loadAggregates(
                1
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Per Page
    |--------------------------------------------------------------------------
    */

    perPageSelect.addEventListener(
        'change',
        function () {

            loadAggregates(
                1
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    resetButton.addEventListener(
        'click',
        function () {

            searchInput.value =
                '';

            usageDateInput.value =
                '';

            periodInput.value =
                '';

            perPageSelect.value =
                '10';

            currentPage =
                1;

            hideAlert();

            selectedPeriodLabel.textContent =
                'Select a subscription period to view aggregates.';

            showEmptyState(
                'Select a Subscription Period'
            );

            pagination.innerHTML =
                '';

            paginationInfo.textContent =
                'Showing 0 records';

            totalRecords.textContent =
                '0';

            totalUsage.textContent =
                '0';

            totalEvents.textContent =
                '0';
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Initial Load
    |--------------------------------------------------------------------------
    */

    loadSubscriptionPeriods();

});
</script>

@endsection