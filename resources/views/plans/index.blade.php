@extends('layouts.app')

@section('title', 'Plans')

@section('page_heading', 'Plans')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Plans</h1>
        <p class="page-description">
            Manage subscription plans, pricing, included usage and overage rates.
        </p>
    </div>

    <button type="button" class="btn btn-primary" id="addPlanBtn">
        + Add Plan
    </button>
</div>

<div id="alertContainer"></div>

<div class="card">

    <div class="table-toolbar">

        <div class="search-wrapper">
            <input
                type="text"
                id="searchInput"
                class="form-control"
                placeholder="Search by plan name, code or description..."
                autocomplete="off"
            >
        </div>

        <div class="page-size-wrapper">
            <label for="perPage">Show</label>

            <select id="perPage" class="form-control form-control-small">
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>

            <span>entries</span>
        </div>

    </div>

    <div class="table-wrapper">

        <table class="data-table">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Plan</th>
                    <th>Code</th>
                    <th>Merchant</th>
                    <th>Price</th>
                    <th>Billing</th>
                    <th>Included Units</th>
                    <th>Overage Rate</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="plansTableBody">

                <tr>
                    <td colspan="10" class="text-center">
                        Loading plans...
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

    <div class="table-footer">

        <div id="paginationInfo">
            Showing 0 to 0 of 0 entries
        </div>

        <div id="paginationContainer" class="pagination"></div>

    </div>

</div>


{{-- Plan Modal --}}
<div
    id="planModal"
    class="modal-overlay"
    style="display: none;"
>
    <div class="modal">

        <div class="modal-header">

            <div>
                <h2 id="modalTitle">Add Plan</h2>

                <p class="modal-description">
                    Create or update a subscription plan.
                </p>
            </div>

            <button
                type="button"
                class="modal-close"
                id="closeModalBtn"
            >
                &times;
            </button>

        </div>

        <form id="planForm">

            <input
                type="hidden"
                id="planId"
            >

            <div class="form-grid">

                {{-- Merchant --}}
                <div class="form-group">

                    <label for="merchantId">
                        Merchant <span class="required">*</span>
                    </label>

                    <select
                        id="merchantId"
                        name="merchant_id"
                        class="form-control"
                        required
                    >
                        <option value="">Select Merchant</option>
                    </select>

                    <div
                        class="field-error"
                        id="merchant_id_error"
                    ></div>

                </div>


                {{-- Plan Name --}}
                <div class="form-group">

                    <label for="planName">
                        Plan Name <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="planName"
                        name="name"
                        class="form-control"
                        maxlength="255"
                        required
                    >

                    <div
                        class="field-error"
                        id="name_error"
                    ></div>

                </div>


                {{-- Plan Code --}}
                <div class="form-group">

                    <label for="planCode">
                        Plan Code <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="planCode"
                        name="code"
                        class="form-control"
                        maxlength="100"
                        required
                    >

                    <div
                        class="field-error"
                        id="code_error"
                    ></div>

                </div>


                {{-- Currency --}}
                <div class="form-group">

                    <label for="currency">
                        Currency
                    </label>

                    <input
                        type="text"
                        id="currency"
                        name="currency"
                        class="form-control"
                        maxlength="3"
                        value="USD"
                        placeholder="USD"
                    >

                    <div
                        class="field-error"
                        id="currency_error"
                    ></div>

                </div>


                {{-- Base Price --}}
                <div class="form-group">

                    <label for="basePrice">
                        Base Price <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        id="basePrice"
                        name="base_price"
                        class="form-control"
                        min="0"
                        step="0.01"
                        required
                    >

                    <div
                        class="field-error"
                        id="base_price_error"
                    ></div>

                </div>


                {{-- Billing Cycle --}}
                <div class="form-group">

                    <label for="billingCycle">
                        Billing Cycle <span class="required">*</span>
                    </label>

                    <select
                        id="billingCycle"
                        name="billing_cycle"
                        class="form-control"
                        required
                    >
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>

                    <div
                        class="field-error"
                        id="billing_cycle_error"
                    ></div>

                </div>


                {{-- Included Units --}}
                <div class="form-group">

                    <label for="includedUnits">
                        Included Units <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        id="includedUnits"
                        name="included_units"
                        class="form-control"
                        min="0"
                        step="1"
                        value="0"
                        required
                    >

                    <div
                        class="field-error"
                        id="included_units_error"
                    ></div>

                </div>


                {{-- Overage Rate --}}
                <div class="form-group">

                    <label for="overageRate">
                        Overage Rate <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        id="overageRate"
                        name="overage_rate"
                        class="form-control"
                        min="0"
                        step="0.000001"
                        value="0"
                        required
                    >

                    <div
                        class="field-error"
                        id="overage_rate_error"
                    ></div>

                </div>


                {{-- Unit Name --}}
                <div class="form-group">

                    <label for="unitName">
                        Unit Name <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="unitName"
                        name="unit_name"
                        class="form-control"
                        maxlength="100"
                        value="units"
                        required
                    >

                    <div
                        class="field-error"
                        id="unit_name_error"
                    ></div>

                </div>


                {{-- Status --}}
                <div class="form-group">

                    <label for="status">
                        Status <span class="required">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-control"
                        required
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>

                    <div
                        class="field-error"
                        id="status_error"
                    ></div>

                </div>


                {{-- Description --}}
                <div class="form-group form-group-full">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        rows="4"
                        placeholder="Enter plan description..."
                    ></textarea>

                    <div
                        class="field-error"
                        id="description_error"
                    ></div>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    id="cancelModalBtn"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                    id="savePlanBtn"
                >
                    Save Plan
                </button>

            </div>

        </form>

    </div>
</div>


<style>

    .table-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .search-wrapper {
        flex: 1;
        max-width: 450px;
    }

    .page-size-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        font-size: 14px;
        color: #6b7280;
    }

    .form-control-small {
        width: 80px;
    }

    .table-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
        font-size: 14px;
        color: #6b7280;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pagination button {
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border: 1px solid #d1d5db;
        background: #fff;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }

    .pagination button:hover:not(:disabled) {
        background: #f3f4f6;
    }

    .pagination button.active {
        background: #111827;
        color: #fff;
        border-color: #111827;
    }

    .pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }

    .modal {
        width: 100%;
        max-width: 850px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
    }

    .modal-description {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 14px;
    }

    .modal-close {
        border: 0;
        background: transparent;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
        color: #6b7280;
    }

    .modal-close:hover {
        color: #111827;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        padding: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .form-group-full {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 500;
        color: #374151;
    }

    .required {
        color: #dc2626;
    }

    .form-control {
        width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #fff;
        font-size: 14px;
        outline: none;
    }

    .form-control:focus {
        border-color: #111827;
        box-shadow: 0 0 0 2px rgba(17, 24, 39, 0.08);
    }

    textarea.form-control {
        resize: vertical;
    }

    .field-error {
        min-height: 18px;
        color: #dc2626;
        font-size: 12px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px 24px;
        border-top: 1px solid #e5e7eb;
    }

    .btn {
        border: 0;
        border-radius: 6px;
        padding: 10px 16px;
        font-size: 14px;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-primary {
        background: #111827;
        color: #fff;
    }

    .btn-primary:hover {
        background: #1f2937;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .btn-danger {
        background: #dc2626;
        color: #fff;
    }

    .btn-danger:hover {
        background: #b91c1c;
    }

    .action-buttons {
        display: flex;
        gap: 6px;
    }

    .btn-small {
        padding: 6px 10px;
        font-size: 12px;
    }

    .plan-name {
        font-weight: 600;
        color: #111827;
    }

    .plan-description {
        max-width: 220px;
        color: #6b7280;
        font-size: 12px;
        margin-top: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .price {
        font-weight: 600;
        white-space: nowrap;
    }

    .number {
        text-align: right;
        white-space: nowrap;
    }

    .badge-success {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 999px;
        background: #dcfce7;
        color: #166534;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-neutral {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #4b5563;
        font-size: 12px;
        font-weight: 500;
    }

    .alert {
        margin-bottom: 20px;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
    }

    .alert-error {
        background: #fee2e2;
        color: #991b1b;
    }

    .text-center {
        text-align: center;
    }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #6b7280;
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    @media (max-width: 900px) {

        .table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-wrapper {
            max-width: none;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .table-footer {
            flex-direction: column;
            align-items: flex-start;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group-full {
            grid-column: auto;
        }

    }

</style>


<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    const state = {
        page: 1,
        perPage: 10,
        search: '',
        editingId: null
    };


    /*
    |--------------------------------------------------------------------------
    | DOM Elements
    |--------------------------------------------------------------------------
    */

    const modal =
        document.getElementById('planModal');

    const form =
        document.getElementById('planForm');

    const tableBody =
        document.getElementById('plansTableBody');

    const paginationContainer =
        document.getElementById('paginationContainer');

    const paginationInfo =
        document.getElementById('paginationInfo');

    const searchInput =
        document.getElementById('searchInput');

    const perPageSelect =
        document.getElementById('perPage');

    const merchantIdInput =
        document.getElementById('merchantId');

    const planIdInput =
        document.getElementById('planId');

    const planNameInput =
        document.getElementById('planName');

    const planCodeInput =
        document.getElementById('planCode');

    const descriptionInput =
        document.getElementById('description');

    const currencyInput =
        document.getElementById('currency');

    const basePriceInput =
        document.getElementById('basePrice');

    const billingCycleInput =
        document.getElementById('billingCycle');

    const includedUnitsInput =
        document.getElementById('includedUnits');

    const overageRateInput =
        document.getElementById('overageRate');

    const unitNameInput =
        document.getElementById('unitName');

    const statusInput =
        document.getElementById('status');

    const modalTitle =
        document.getElementById('modalTitle');

    const savePlanBtn =
        document.getElementById('savePlanBtn');


    /*
    |--------------------------------------------------------------------------
    | CSRF
    |--------------------------------------------------------------------------
    */

    function getCsrfToken() {

        return document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute('content');

    }


    /*
    |--------------------------------------------------------------------------
    | API Request Helper
    |--------------------------------------------------------------------------
    */

    async function request(url, options = {}) {

        const csrfToken =
            getCsrfToken();

        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {})
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] =
                csrfToken;
        }

        const response =
            await fetch(url, {
                ...options,
                credentials: 'same-origin',
                headers
            });

        let result = null;

        try {

            result =
                await response.json();

        } catch (error) {

            result = null;

        }

        if (!response.ok) {

            if (response.status === 419) {

                throw {
                    message:
                        'Your session or CSRF token has expired. Please refresh the page and try again.',
                    status: 419
                };

            }

            throw {
                message:
                    result?.message ||
                    'Something went wrong.',

                errors:
                    result?.errors || {},

                status:
                    response.status
            };

        }

        return result;

    }


    /*
    |--------------------------------------------------------------------------
    | Alerts
    |--------------------------------------------------------------------------
    */

    function showAlert(
        message,
        type = 'success'
    ) {

        const alertContainer =
            document.getElementById(
                'alertContainer'
            );

        alertContainer.innerHTML = `
            <div class="alert alert-${type}">
                ${escapeHtml(message)}
            </div>
        `;

        setTimeout(() => {

            alertContainer.innerHTML = '';

        }, 4000);

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
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }


    /*
    |--------------------------------------------------------------------------
    | Clear Validation Errors
    |--------------------------------------------------------------------------
    */

    function clearErrors() {

        document
            .querySelectorAll('.field-error')
            .forEach(element => {

                element.textContent = '';

            });

    }


    /*
    |--------------------------------------------------------------------------
    | Display Validation Errors
    |--------------------------------------------------------------------------
    */

    function showValidationErrors(errors) {

        Object.keys(errors || {})
            .forEach(field => {

                const errorElement =
                    document.getElementById(
                        `${field}_error`
                    );

                if (errorElement) {

                    errorElement.textContent =
                        Array.isArray(errors[field])
                            ? errors[field][0]
                            : errors[field];

                }

            });

    }


    /*
    |--------------------------------------------------------------------------
    | Load Merchants
    |--------------------------------------------------------------------------
    |
    | Merchant API response:
    |
    | {
    |     data: {
    |         data: [...]
    |     }
    | }
    |
    */

    async function loadMerchants(
        selectedMerchantId = null
    ) {

        try {

            const result =
                await request(
                    '/merchants/data?per_page=100'
                );

            const merchants =
                result?.data?.data || [];

            merchantIdInput.innerHTML =
                '<option value="">Select Merchant</option>';

            merchants.forEach(function (merchant) {

                const option =
                    document.createElement('option');

                option.value =
                    merchant.id;

                option.textContent =
                    `${merchant.name} (${merchant.code})`;

                merchantIdInput.appendChild(
                    option
                );

            });


            /*
            |--------------------------------------------------------------------------
            | Select merchant when editing
            |--------------------------------------------------------------------------
            */

            if (selectedMerchantId !== null) {

                merchantIdInput.value =
                    String(selectedMerchantId);

            }

        } catch (error) {

            console.error(
                'Merchant loading error:',
                error
            );

            merchantIdInput.innerHTML =
                '<option value="">Unable to load merchants</option>';

            showAlert(
                error.message ||
                'Unable to load merchants.',
                'error'
            );

            throw error;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Load Plans
    |--------------------------------------------------------------------------
    */

    async function loadPlans() {

        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    Loading plans...
                </td>
            </tr>
        `;

        try {

            const params =
                new URLSearchParams({
                    page: state.page,
                    per_page: state.perPage,
                    search: state.search
                });

            const result =
                await request(
                    `/plans/data?${params.toString()}`
                );

            const data =
                result?.data || {};

            renderPlans(
                data.items || []
            );

            renderPagination(
                data
            );

        } catch (error) {

            tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="10"
                        class="empty-state"
                    >
                        Unable to load plans.
                    </td>
                </tr>
            `;

            showAlert(
                error.message ||
                'Unable to load plans.',
                'error'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Render Plans
    |--------------------------------------------------------------------------
    */

    function renderPlans(plans) {

        if (!plans.length) {

            tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="10"
                        class="empty-state"
                    >
                        No plans found.
                    </td>
                </tr>
            `;

            return;

        }

        const startIndex =
            (state.page - 1) *
            state.perPage;

        tableBody.innerHTML =
            plans.map((plan, index) => {

                const rowNumber =
                    startIndex +
                    index +
                    1;

                const merchantName =
                    plan.merchant?.name ||
                    `Merchant #${plan.merchant_id}`;

                const currency =
                    plan.currency ||
                    'USD';

                const basePrice =
                    Number(
                        plan.base_price || 0
                    ).toFixed(2);

                const overageRate =
                    Number(
                        plan.overage_rate || 0
                    ).toFixed(6);

                const billingCycle =
                    capitalize(
                        plan.billing_cycle
                    );

                const statusBadge =
                    plan.status === 'active'
                        ? `
                            <span class="badge-success">
                                Active
                            </span>
                          `
                        : `
                            <span class="badge-neutral">
                                Inactive
                            </span>
                          `;

                return `
                    <tr>

                        <td>
                            ${rowNumber}
                        </td>

                        <td>

                            <div class="plan-name">
                                ${escapeHtml(plan.name)}
                            </div>

                            ${
                                plan.description
                                    ? `
                                        <div class="plan-description">
                                            ${escapeHtml(
                                                plan.description
                                            )}
                                        </div>
                                      `
                                    : ''
                            }

                        </td>

                        <td>
                            ${escapeHtml(plan.code)}
                        </td>

                        <td>
                            ${escapeHtml(merchantName)}
                        </td>

                        <td class="price">
                            ${escapeHtml(currency)}
                            ${basePrice}
                        </td>

                        <td>
                            ${escapeHtml(billingCycle)}
                        </td>

                        <td class="number">
                            ${Number(
                                plan.included_units || 0
                            ).toLocaleString()}
                        </td>

                        <td class="number">
                            ${overageRate}
                        </td>

                        <td>
                            ${statusBadge}
                        </td>

                        <td>

                            <div class="action-buttons">

                                <button
                                    type="button"
                                    class="btn btn-secondary btn-small"
                                    onclick="editPlan(${plan.id})"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-danger btn-small"
                                    onclick="deletePlan(${plan.id})"
                                >
                                    Delete
                                </button>

                            </div>

                        </td>

                    </tr>
                `;

            }).join('');

    }


    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    function renderPagination(data) {

        const currentPage =
            Number(
                data.current_page || 1
            );

        const lastPage =
            Number(
                data.last_page || 1
            );

        const total =
            Number(
                data.total || 0
            );

        const perPage =
            Number(
                data.per_page ||
                state.perPage
            );

        state.page =
            currentPage;

        if (total === 0) {

            paginationInfo.textContent =
                'Showing 0 to 0 of 0 entries';

            paginationContainer.innerHTML =
                '';

            return;

        }

        const from =
            ((currentPage - 1) *
                perPage) + 1;

        const to =
            Math.min(
                currentPage * perPage,
                total
            );

        paginationInfo.textContent =
            `Showing ${from} to ${to} of ${total} entries`;

        let html = '';


        /*
        |--------------------------------------------------------------------------
        | Previous
        |--------------------------------------------------------------------------
        */

        html += `
            <button
                type="button"
                ${currentPage <= 1 ? 'disabled' : ''}
                onclick="changePlanPage(${currentPage - 1})"
            >
                ‹
            </button>
        `;


        /*
        |--------------------------------------------------------------------------
        | Page Numbers
        |--------------------------------------------------------------------------
        */

        const startPage =
            Math.max(
                1,
                currentPage - 2
            );

        const endPage =
            Math.min(
                lastPage,
                currentPage + 2
            );

        if (startPage > 1) {

            html += `
                <button
                    type="button"
                    onclick="changePlanPage(1)"
                >
                    1
                </button>
            `;

            if (startPage > 2) {

                html += `
                    <span>...</span>
                `;

            }

        }

        for (
            let page = startPage;
            page <= endPage;
            page++
        ) {

            html += `
                <button
                    type="button"
                    class="${page === currentPage ? 'active' : ''}"
                    onclick="changePlanPage(${page})"
                >
                    ${page}
                </button>
            `;

        }

        if (endPage < lastPage) {

            if (
                endPage <
                lastPage - 1
            ) {

                html += `
                    <span>...</span>
                `;

            }

            html += `
                <button
                    type="button"
                    onclick="changePlanPage(${lastPage})"
                >
                    ${lastPage}
                </button>
            `;

        }


        /*
        |--------------------------------------------------------------------------
        | Next
        |--------------------------------------------------------------------------
        */

        html += `
            <button
                type="button"
                ${currentPage >= lastPage ? 'disabled' : ''}
                onclick="changePlanPage(${currentPage + 1})"
            >
                ›
            </button>
        `;

        paginationContainer.innerHTML =
            html;

    }


    /*
    |--------------------------------------------------------------------------
    | Change Page
    |--------------------------------------------------------------------------
    */

    window.changePlanPage =
        function (page) {

            if (page < 1) {
                return;
            }

            state.page =
                page;

            loadPlans();

        };


    /*
    |--------------------------------------------------------------------------
    | Open Add Modal
    |--------------------------------------------------------------------------
    */

    async function openAddModal() {

        state.editingId =
            null;

        form.reset();

        clearErrors();

        planIdInput.value =
            '';

        modalTitle.textContent =
            'Add Plan';

        savePlanBtn.textContent =
            'Save Plan';

        currencyInput.value =
            'USD';

        billingCycleInput.value =
            'monthly';

        includedUnitsInput.value =
            '0';

        overageRateInput.value =
            '0';

        unitNameInput.value =
            'units';

        statusInput.value =
            'active';

        /*
        |--------------------------------------------------------------------------
        | Load merchant dropdown
        |--------------------------------------------------------------------------
        */

        await loadMerchants();

        modal.style.display =
            'flex';

        planNameInput.focus();

    }


    /*
    |--------------------------------------------------------------------------
    | Open Edit Modal
    |--------------------------------------------------------------------------
    */

    window.editPlan =
        async function (id) {

            try {

                clearErrors();

                const result =
                    await request(
                        `/plans/${id}`
                    );

                const plan =
                    result?.data;

                if (!plan) {

                    throw {
                        message:
                            'Plan not found.'
                    };

                }

                state.editingId =
                    id;

                planIdInput.value =
                    id;

                modalTitle.textContent =
                    'Edit Plan';

                savePlanBtn.textContent =
                    'Update Plan';

                /*
                |--------------------------------------------------------------------------
                | Load merchants and select current merchant
                |--------------------------------------------------------------------------
                */

                await loadMerchants(
                    plan.merchant_id
                );

                planNameInput.value =
                    plan.name || '';

                planCodeInput.value =
                    plan.code || '';

                descriptionInput.value =
                    plan.description || '';

                currencyInput.value =
                    plan.currency || 'USD';

                basePriceInput.value =
                    plan.base_price ?? '';

                billingCycleInput.value =
                    plan.billing_cycle ||
                    'monthly';

                includedUnitsInput.value =
                    plan.included_units ??
                    0;

                overageRateInput.value =
                    plan.overage_rate ??
                    0;

                unitNameInput.value =
                    plan.unit_name ||
                    'units';

                statusInput.value =
                    plan.status ||
                    'active';

                modal.style.display =
                    'flex';

                planNameInput.focus();

            } catch (error) {

                showAlert(
                    error.message ||
                    'Unable to load plan.',
                    'error'
                );

            }

        };


    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    function closeModal() {

        modal.style.display =
            'none';

        state.editingId =
            null;

        form.reset();

        clearErrors();

        currencyInput.value =
            'USD';

        billingCycleInput.value =
            'monthly';

        includedUnitsInput.value =
            '0';

        overageRateInput.value =
            '0';

        unitNameInput.value =
            'units';

        statusInput.value =
            'active';

    }


    /*
    |--------------------------------------------------------------------------
    | Save Plan
    |--------------------------------------------------------------------------
    */

    async function savePlan(event) {

        event.preventDefault();

        clearErrors();

        const payload = {

            merchant_id:
                merchantIdInput.value,

            name:
                planNameInput.value.trim(),

            code:
                planCodeInput.value.trim(),

            description:
                descriptionInput.value.trim() ||
                null,

            currency:
                currencyInput.value
                    .trim()
                    .toUpperCase() ||
                'USD',

            base_price:
                basePriceInput.value,

            billing_cycle:
                billingCycleInput.value,

            included_units:
                includedUnitsInput.value,

            overage_rate:
                overageRateInput.value,

            unit_name:
                unitNameInput.value.trim(),

            status:
                statusInput.value

        };

        const isEditing =
            state.editingId !== null;

        const url =
            isEditing
                ? `/plans/${state.editingId}`
                : '/plans';

        const method =
            isEditing
                ? 'PUT'
                : 'POST';

        savePlanBtn.disabled =
            true;

        savePlanBtn.textContent =
            isEditing
                ? 'Updating...'
                : 'Saving...';

        try {

            const result =
                await request(
                    url,
                    {
                        method: method,

                        headers: {
                            'Content-Type':
                                'application/json'
                        },

                        body:
                            JSON.stringify(payload)
                    }
                );

            closeModal();

            showAlert(
                result?.message ||
                (
                    isEditing
                        ? 'Plan updated successfully.'
                        : 'Plan created successfully.'
                ),
                'success'
            );

            await loadPlans();

        } catch (error) {

            if (error.errors) {

                showValidationErrors(
                    error.errors
                );

            }

            showAlert(
                error.message ||
                'Unable to save plan.',
                'error'
            );

        } finally {

            savePlanBtn.disabled =
                false;

            savePlanBtn.textContent =
                isEditing
                    ? 'Update Plan'
                    : 'Save Plan';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Delete Plan
    |--------------------------------------------------------------------------
    */

    window.deletePlan =
        async function (id) {

            const confirmed =
                window.confirm(
                    'Are you sure you want to delete this plan?'
                );

            if (!confirmed) {
                return;
            }

            try {

                const result =
                    await request(
                        `/plans/${id}`,
                        {
                            method: 'DELETE'
                        }
                    );

                showAlert(
                    result?.message ||
                    'Plan deleted successfully.',
                    'success'
                );

                /*
                |--------------------------------------------------------------------------
                | If current page becomes empty,
                | move to previous page
                |--------------------------------------------------------------------------
                */

                if (state.page > 1) {

                    const currentCount =
                        document.querySelectorAll(
                            '#plansTableBody tr'
                        ).length;

                    if (currentCount <= 1) {

                        state.page--;

                    }

                }

                await loadPlans();

            } catch (error) {

                showAlert(
                    error.message ||
                    'Unable to delete plan.',
                    'error'
                );

            }

        };


    /*
    |--------------------------------------------------------------------------
    | Capitalize
    |--------------------------------------------------------------------------
    */

    function capitalize(value) {

        if (!value) {
            return '';
        }

        return value
            .charAt(0)
            .toUpperCase()
            + value.slice(1);

    }


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    let searchTimer =
        null;

    searchInput.addEventListener(
        'input',
        function () {

            clearTimeout(
                searchTimer
            );

            searchTimer =
                setTimeout(() => {

                    state.search =
                        searchInput.value.trim();

                    state.page =
                        1;

                    loadPlans();

                }, 350);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Page Size
    |--------------------------------------------------------------------------
    */

    perPageSelect.addEventListener(
        'change',
        function () {

            state.perPage =
                Number(
                    perPageSelect.value
                );

            state.page =
                1;

            loadPlans();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Add Button
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('addPlanBtn')
        .addEventListener(
            'click',
            openAddModal
        );


    /*
    |--------------------------------------------------------------------------
    | Close Buttons
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('closeModalBtn')
        .addEventListener(
            'click',
            closeModal
        );

    document
        .getElementById('cancelModalBtn')
        .addEventListener(
            'click',
            closeModal
        );


    /*
    |--------------------------------------------------------------------------
    | Close Modal on Overlay Click
    |--------------------------------------------------------------------------
    */

    modal.addEventListener(
        'click',
        function (event) {

            if (
                event.target === modal
            ) {

                closeModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Escape Key
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                modal.style.display !== 'none'
            ) {

                closeModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Form Submit
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        savePlan
    );


    /*
    |--------------------------------------------------------------------------
    | Initial Load
    |--------------------------------------------------------------------------
    */

    loadPlans();

});

</script>

@endsection