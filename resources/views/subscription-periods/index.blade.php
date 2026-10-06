@extends('layouts.app')

@section('title', 'Subscription Periods')

@section('page_heading', 'Subscription Periods')

@section('content')

<style>
    .subscription-periods-page {
        width: 100%;
    }

    /* =========================
       Page Header
    ========================== */

    .subscription-periods-page .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .subscription-periods-page .page-header-content h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
    }

    .subscription-periods-page .page-header-content p {
        margin: 8px 0 0;
        font-size: 14px;
        color: #6b7280;
    }

    /* =========================
       Buttons
    ========================== */

    .subscription-periods-page .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 0;
        border-radius: 7px;
        padding: 10px 16px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .subscription-periods-page .btn-primary {
        background: #111827;
        color: #ffffff;
    }

    .subscription-periods-page .btn-primary:hover {
        background: #1f2937;
    }

    .subscription-periods-page .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .subscription-periods-page .btn-secondary {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .subscription-periods-page .btn-secondary:hover {
        background: #f9fafb;
    }

    .subscription-periods-page .btn-danger {
        background: #ffffff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .subscription-periods-page .btn-danger:hover {
        background: #fef2f2;
    }

    .subscription-periods-page .btn-small {
        padding: 7px 11px;
        font-size: 12px;
        border-radius: 6px;
    }

    /* =========================
       Card
    ========================== */

    .subscription-periods-page .card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    /* =========================
       Toolbar
    ========================== */

    .subscription-periods-page .table-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .subscription-periods-page .search-group {
        width: 100%;
        max-width: 420px;
    }

    .subscription-periods-page .toolbar-right {
        display: flex;
        align-items: flex-end;
    }

    /* =========================
       Forms
    ========================== */

    .subscription-periods-page .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .subscription-periods-page .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .subscription-periods-page .form-control {
        width: 100%;
        box-sizing: border-box;
        height: 40px;
        padding: 8px 11px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: #ffffff;
        color: #111827;
        font-size: 14px;
        outline: none;
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease;
    }

    .subscription-periods-page .form-control:focus {
        border-color: #6b7280;
        box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.12);
    }

    .subscription-periods-page select.form-control {
        cursor: pointer;
    }

    .subscription-periods-page .form-control.readonly {
        background: #f9fafb;
        color: #6b7280;
    }

    .subscription-periods-page .required {
        color: #dc2626;
    }

    .subscription-periods-page .field-help {
        margin-top: 2px;
        font-size: 11px;
        color: #9ca3af;
    }

    /* =========================
       Table
    ========================== */

    .subscription-periods-page .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .subscription-periods-page .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1050px;
    }

    .subscription-periods-page .data-table thead {
        background: #f9fafb;
    }

    .subscription-periods-page .data-table th {
        padding: 12px 18px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
        white-space: nowrap;
    }

    .subscription-periods-page .data-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #374151;
        vertical-align: middle;
    }

    .subscription-periods-page .data-table tbody tr:hover {
        background: #fafafa;
    }

    .subscription-periods-page .primary-text {
        font-weight: 600;
        color: #111827;
    }

    .subscription-periods-page .secondary-text {
        margin-top: 3px;
        color: #6b7280;
        font-size: 11px;
    }

    .subscription-periods-page .actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 7px;
    }

    .subscription-periods-page .table-loading,
    .subscription-periods-page .table-empty,
    .subscription-periods-page .table-error {
        padding: 40px 18px !important;
        text-align: center !important;
        color: #6b7280 !important;
    }

    .subscription-periods-page .table-error {
        color: #dc2626 !important;
    }

    /* =========================
       Billing Cycle
    ========================== */

    .subscription-periods-page .cycle-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 9px;
        font-size: 11px;
        font-weight: 600;
        background: #f3f4f6;
        color: #374151;
    }

    /* =========================
       Pagination
    ========================== */

    .subscription-periods-page .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 14px 18px;
        border-top: 1px solid #e5e7eb;
    }

    .subscription-periods-page .pagination-info {
        font-size: 13px;
        color: #6b7280;
    }

    .subscription-periods-page .pagination {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .subscription-periods-page .pagination button {
        min-width: 34px;
        height: 34px;
        padding: 0 9px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #ffffff;
        color: #374151;
        font-size: 12px;
        cursor: pointer;
    }

    .subscription-periods-page .pagination button:hover:not(:disabled) {
        background: #f9fafb;
    }

    .subscription-periods-page .pagination button.active {
        background: #111827;
        border-color: #111827;
        color: #ffffff;
    }

    .subscription-periods-page .pagination button:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    /* =========================
       Alert
    ========================== */

    .subscription-periods-page .alert {
        display: none;
        margin-bottom: 18px;
        padding: 11px 14px;
        border-radius: 7px;
        font-size: 13px;
        border: 1px solid transparent;
    }

    .subscription-periods-page .alert.show {
        display: block;
    }

    .subscription-periods-page .alert-success {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .subscription-periods-page .alert-error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    /* =========================
       Modal
    ========================== */

    .subscription-periods-page .period-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .subscription-periods-page .period-modal.hidden {
        display: none !important;
    }

    .subscription-periods-page .period-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
    }

    .subscription-periods-page .period-modal-dialog {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 760px;
        max-height: calc(100vh - 40px);
        overflow: hidden;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.20);
    }

    .subscription-periods-page .period-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .subscription-periods-page .period-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .subscription-periods-page .period-modal-description {
        margin: 5px 0 0;
        font-size: 12px;
        color: #6b7280;
    }

    .subscription-periods-page .modal-close {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #6b7280;
        font-size: 18px;
        cursor: pointer;
    }

    .subscription-periods-page .modal-close:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .subscription-periods-page .period-modal-body {
        padding: 20px;
        max-height: calc(100vh - 190px);
        overflow-y: auto;
    }

    .subscription-periods-page .period-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .subscription-periods-page .form-full {
        grid-column: 1 / -1;
    }

    .subscription-periods-page .form-error-box {
        display: none;
        margin-bottom: 16px;
        padding: 10px 12px;
        border-radius: 7px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        font-size: 12px;
    }

    .subscription-periods-page .form-error-box.show {
        display: block;
    }

    .subscription-periods-page .form-error-box ul {
        margin: 0;
        padding-left: 18px;
    }

    .subscription-periods-page .period-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        padding: 14px 20px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
    }

    /* =========================
       Responsive
    ========================== */

    @media (max-width: 768px) {

        .subscription-periods-page .page-header {
            flex-direction: column;
        }

        .subscription-periods-page .table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .subscription-periods-page .search-group {
            max-width: none;
        }

        .subscription-periods-page .period-form-grid {
            grid-template-columns: 1fr;
        }

        .subscription-periods-page .form-full {
            grid-column: auto;
        }

        .subscription-periods-page .pagination-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .subscription-periods-page .period-modal {
            padding: 10px;
        }

        .subscription-periods-page .period-modal-dialog {
            max-height: calc(100vh - 20px);
        }
    }
</style>


<div class="subscription-periods-page">

    {{-- =========================
         Page Header
    ========================== --}}

    <div class="page-header">

        <div class="page-header-content">

            <h1>
                Subscription Periods
            </h1>

            <p>
                Manage billing periods and pricing snapshots for subscriptions.
            </p>

        </div>

        <button
            type="button"
            id="addPeriodButton"
            class="btn btn-primary"
        >
            + Add Subscription Period
        </button>

    </div>


    {{-- =========================
         Alert
    ========================== --}}

    <div
        id="alertContainer"
        class="alert"
    ></div>


    {{-- =========================
         Table Card
    ========================== --}}

    <div class="card">

        <div class="table-toolbar">

            <div class="search-group">

                <div class="form-group">

                    <label
                        for="searchInput"
                        class="form-label"
                    >
                        Search
                    </label>

                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Search customer, plan, billing cycle..."
                    >

                </div>

            </div>


            <div class="toolbar-right">

                <div class="form-group">

                    <label
                        for="perPageSelect"
                        class="form-label"
                    >
                        Per page
                    </label>

                    <select
                        id="perPageSelect"
                        class="form-control"
                    >
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>

                </div>

            </div>

        </div>


        {{-- =========================
             Table
        ========================== --}}

        <div class="table-wrapper">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>
                            Customer
                        </th>

                        <th>
                            Plan
                        </th>

                        <th>
                            Period Start
                        </th>

                        <th>
                            Period End
                        </th>

                        <th>
                            Base Price
                        </th>

                        <th>
                            Included Units
                        </th>

                        <th>
                            Overage Rate
                        </th>

                        <th>
                            Cycle
                        </th>

                        <th style="text-align: right;">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody id="periodsTableBody">

                    <tr>

                        <td
                            colspan="9"
                            class="table-loading"
                        >
                            Loading subscription periods...
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        {{-- =========================
             Pagination
        ========================== --}}

        <div
            id="paginationContainer"
            class="pagination-wrapper"
        ></div>

    </div>


    {{-- =========================================================
         Modal
    ========================================================== --}}

    <div
        id="periodModal"
        class="period-modal hidden"
        aria-hidden="true"
    >

        <div
            id="modalBackdrop"
            class="period-modal-backdrop"
        ></div>


        <div class="period-modal-dialog">


            {{-- Modal Header --}}

            <div class="period-modal-header">

                <div>

                    <h2
                        id="modalTitle"
                        class="period-modal-title"
                    >
                        Add Subscription Period
                    </h2>

                    <p class="period-modal-description">
                        Create a billing period from the subscription pricing snapshot.
                    </p>

                </div>


                <button
                    type="button"
                    id="closeModalButton"
                    class="modal-close"
                    aria-label="Close"
                >
                    ×
                </button>

            </div>


            {{-- Form --}}

            <form id="periodForm">

                <div class="period-modal-body">


                    {{-- Validation Errors --}}

                    <div
                        id="formErrorContainer"
                        class="form-error-box"
                    ></div>


                    <div class="period-form-grid">


                        {{-- Subscription --}}

                        <div class="form-group form-full">

                            <label
                                for="subscriptionIdInput"
                                class="form-label"
                            >
                                Subscription
                                <span class="required">*</span>
                            </label>

                            <select
                                id="subscriptionIdInput"
                                name="subscription_id"
                                class="form-control"
                                required
                            >

                                <option value="">
                                    Select Subscription
                                </option>

                            </select>

                            <small class="field-help">
                                Pricing values below are automatically copied from the selected subscription plan.
                            </small>

                        </div>


                        {{-- Starts At --}}

                        <div class="form-group">

                            <label
                                for="startsAtInput"
                                class="form-label"
                            >
                                Period Start
                                <span class="required">*</span>
                            </label>

                            <input
                                type="datetime-local"
                                id="startsAtInput"
                                name="starts_at"
                                class="form-control"
                                required
                            >

                        </div>


                        {{-- Ends At --}}

                        <div class="form-group">

                            <label
                                for="endsAtInput"
                                class="form-label"
                            >
                                Period End
                            </label>

                            <input
                                type="datetime-local"
                                id="endsAtInput"
                                name="ends_at"
                                class="form-control"
                            >

                            <small class="field-help">
                                Leave empty for an open-ended period.
                            </small>

                        </div>


                        {{-- Plan --}}

                        <div class="form-group">

                            <label
                                for="planNameInput"
                                class="form-label"
                            >
                                Plan
                            </label>

                            <input
                                type="text"
                                id="planNameInput"
                                class="form-control readonly"
                                readonly
                                placeholder="Select subscription"
                            >

                        </div>


                        {{-- Billing Cycle --}}

                        <div class="form-group">

                            <label
                                for="billingCycleInput"
                                class="form-label"
                            >
                                Billing Cycle
                            </label>

                            <input
                                type="text"
                                id="billingCycleInput"
                                class="form-control readonly"
                                readonly
                                placeholder="-"
                            >

                        </div>


                        {{-- Base Price --}}

                        <div class="form-group">

                            <label
                                for="basePriceInput"
                                class="form-label"
                            >
                                Base Price
                            </label>

                            <input
                                type="text"
                                id="basePriceInput"
                                class="form-control readonly"
                                readonly
                                placeholder="-"
                            >

                        </div>


                        {{-- Included Units --}}

                        <div class="form-group">

                            <label
                                for="includedUnitsInput"
                                class="form-label"
                            >
                                Included Units
                            </label>

                            <input
                                type="text"
                                id="includedUnitsInput"
                                class="form-control readonly"
                                readonly
                                placeholder="-"
                            >

                        </div>


                        {{-- Overage Rate --}}

                        <div class="form-group">

                            <label
                                for="overageRateInput"
                                class="form-label"
                            >
                                Overage Rate
                            </label>

                            <input
                                type="text"
                                id="overageRateInput"
                                class="form-control readonly"
                                readonly
                                placeholder="-"
                            >

                        </div>


                        {{-- Customer --}}

                        <div class="form-group">

                            <label
                                for="customerNameInput"
                                class="form-label"
                            >
                                Customer
                            </label>

                            <input
                                type="text"
                                id="customerNameInput"
                                class="form-control readonly"
                                readonly
                                placeholder="Select subscription"
                            >

                        </div>


                    </div>

                </div>


                {{-- Modal Footer --}}

                <div class="period-modal-footer">

                    <button
                        type="button"
                        id="cancelModalButton"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        id="savePeriodButton"
                        class="btn btn-primary"
                    >
                        Save Subscription Period
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /* =========================
           Elements
        ========================== */

        const tableBody =
            document.getElementById(
                'periodsTableBody'
            );

        const paginationContainer =
            document.getElementById(
                'paginationContainer'
            );

        const searchInput =
            document.getElementById(
                'searchInput'
            );

        const perPageSelect =
            document.getElementById(
                'perPageSelect'
            );

        const addPeriodButton =
            document.getElementById(
                'addPeriodButton'
            );

        const periodModal =
            document.getElementById(
                'periodModal'
            );

        const modalBackdrop =
            document.getElementById(
                'modalBackdrop'
            );

        const closeModalButton =
            document.getElementById(
                'closeModalButton'
            );

        const cancelModalButton =
            document.getElementById(
                'cancelModalButton'
            );

        const periodForm =
            document.getElementById(
                'periodForm'
            );

        const modalTitle =
            document.getElementById(
                'modalTitle'
            );

        const savePeriodButton =
            document.getElementById(
                'savePeriodButton'
            );

        const formErrorContainer =
            document.getElementById(
                'formErrorContainer'
            );

        const alertContainer =
            document.getElementById(
                'alertContainer'
            );

        const subscriptionIdInput =
            document.getElementById(
                'subscriptionIdInput'
            );

        const startsAtInput =
            document.getElementById(
                'startsAtInput'
            );

        const endsAtInput =
            document.getElementById(
                'endsAtInput'
            );

        const planNameInput =
            document.getElementById(
                'planNameInput'
            );

        const billingCycleInput =
            document.getElementById(
                'billingCycleInput'
            );

        const basePriceInput =
            document.getElementById(
                'basePriceInput'
            );

        const includedUnitsInput =
            document.getElementById(
                'includedUnitsInput'
            );

        const overageRateInput =
            document.getElementById(
                'overageRateInput'
            );

        const customerNameInput =
            document.getElementById(
                'customerNameInput'
            );


        let currentPage = 1;

        let editingPeriodId = null;

        let searchTimer = null;

        let subscriptions = [];


        /* =========================
           CSRF
        ========================== */

        const csrfToken =
            document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                ?.getAttribute(
                    'content'
                );


        /* =========================
           Request Helper
        ========================== */

        async function request(
            url,
            options = {}
        ) {

            const headers = {
                'Accept':
                    'application/json',

                'X-Requested-With':
                    'XMLHttpRequest',

                ...(options.headers || {}),
            };


            if (csrfToken) {

                headers['X-CSRF-TOKEN'] =
                    csrfToken;
            }


            if (
                options.body &&
                !(options.body instanceof FormData)
            ) {

                headers['Content-Type'] =
                    'application/json';
            }


            const response =
                await fetch(
                    url,
                    {
                        ...options,

                        credentials:
                            'same-origin',

                        headers,
                    }
                );


            const contentType =
                response.headers.get(
                    'content-type'
                ) || '';


            let result;


            if (
                contentType.includes(
                    'application/json'
                )
            ) {

                result =
                    await response.json();

            } else {

                const text =
                    await response.text();

                throw new Error(
                    text ||
                    'Unexpected server response.'
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

                error.validationErrors =
                    result?.errors || {};

                throw error;
            }


            return result;
        }


        /* =========================
           Escape HTML
        ========================== */

        function escapeHtml(
            value
        ) {

            const div =
                document.createElement(
                    'div'
                );

            div.textContent =
                value ?? '';

            return div.innerHTML;
        }


        /* =========================
           Alert
        ========================== */

        function showAlert(
            message,
            type = 'success'
        ) {

            alertContainer.className =
                'alert show';

            alertContainer.classList.add(
                type === 'success'
                    ? 'alert-success'
                    : 'alert-error'
            );

            alertContainer.textContent =
                message;


            setTimeout(
                function () {

                    alertContainer.classList.remove(
                        'show'
                    );

                },
                4000
            );
        }


        /* =========================
           Date Helpers
        ========================== */

        function toDateTimeLocal(
            value
        ) {

            if (!value) {
                return '';
            }


            const date =
                new Date(value);


            if (
                Number.isNaN(
                    date.getTime()
                )
            ) {

                return '';
            }


            const year =
                date.getFullYear();


            const month =
                String(
                    date.getMonth() + 1
                ).padStart(
                    2,
                    '0'
                );


            const day =
                String(
                    date.getDate()
                ).padStart(
                    2,
                    '0'
                );


            const hours =
                String(
                    date.getHours()
                ).padStart(
                    2,
                    '0'
                );


            const minutes =
                String(
                    date.getMinutes()
                ).padStart(
                    2,
                    '0'
                );


            return `${year}-${month}-${day}T${hours}:${minutes}`;
        }


        function formatDate(
            value
        ) {

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

                return escapeHtml(
                    String(value)
                );
            }


            return date.toLocaleDateString(
                'en-IN',
                {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                }
            );
        }


        /* =========================
           Number Formatting
        ========================== */

        function formatMoney(
            value
        ) {

            if (
                value === null ||
                value === undefined ||
                value === ''
            ) {

                return '-';
            }


            const number =
                Number(value);


            if (
                Number.isNaN(number)
            ) {

                return escapeHtml(
                    String(value)
                );
            }


            return number.toFixed(2);
        }


        /* =========================
           Load Subscriptions
        ========================== */

        async function loadSubscriptions(
            selectedId = null
        ) {

            const result =
                await request(
                    '/subscriptions/data?per_page=100'
                );


            subscriptions =
                result?.data?.items ||
                result?.data?.data ||
                [];


            subscriptionIdInput.innerHTML = `
                <option value="">
                    Select Subscription
                </option>
            `;


            subscriptions.forEach(
                function (subscription) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        subscription.id;


                    const customerName =
                        subscription.customer?.name ||
                        `Customer #${subscription.customer_id}`;


                    const planName =
                        subscription.plan?.name ||
                        `Plan #${subscription.plan_id}`;


                    option.textContent =
                        `${customerName} — ${planName} — #${subscription.id}`;


                    subscriptionIdInput.appendChild(
                        option
                    );
                }
            );


            if (
                selectedId !== null &&
                selectedId !== ''
            ) {

                subscriptionIdInput.value =
                    String(selectedId);
            }
        }


        /* =========================
           Fill Pricing Snapshot
        ========================== */

        function updateSubscriptionDetails() {

            const selectedId =
                subscriptionIdInput.value;


            if (!selectedId) {

                planNameInput.value =
                    '';

                billingCycleInput.value =
                    '';

                basePriceInput.value =
                    '';

                includedUnitsInput.value =
                    '';

                overageRateInput.value =
                    '';

                customerNameInput.value =
                    '';

                return;
            }


            const subscription =
                subscriptions.find(
                    function (item) {

                        return String(
                            item.id
                        ) ===
                        String(
                            selectedId
                        );
                    }
                );


            if (!subscription) {
                return;
            }


            const plan =
                subscription.plan ||
                null;


            const customer =
                subscription.customer ||
                null;


            planNameInput.value =
                plan?.name ||
                `Plan #${subscription.plan_id}`;


            billingCycleInput.value =
                plan?.billing_cycle ||
                '-';


            basePriceInput.value =
                plan?.base_price !== undefined
                    ? formatMoney(
                        plan.base_price
                    )
                    : '-';


            includedUnitsInput.value =
                plan?.included_units !== undefined
                    ? plan.included_units
                    : '-';


            overageRateInput.value =
                plan?.overage_rate !== undefined
                    ? Number(
                        plan.overage_rate
                    ).toFixed(6)
                    : '-';


            customerNameInput.value =
                customer?.name ||
                `Customer #${subscription.customer_id}`;
        }


        subscriptionIdInput.addEventListener(
            'change',
            updateSubscriptionDetails
        );


        /* =========================
           Open Modal
        ========================== */

        function openModal() {

            periodModal.classList.remove(
                'hidden'
            );

            periodModal.setAttribute(
                'aria-hidden',
                'false'
            );

            document.body.style.overflow =
                'hidden';
        }


        /* =========================
           Close Modal
        ========================== */

        function closeModal() {

            periodModal.classList.add(
                'hidden'
            );

            periodModal.setAttribute(
                'aria-hidden',
                'true'
            );

            document.body.style.overflow =
                '';

            resetForm();
        }


        /* =========================
           Reset Form
        ========================== */

        function resetForm() {

            periodForm.reset();


            editingPeriodId =
                null;


            modalTitle.textContent =
                'Add Subscription Period';


            savePeriodButton.textContent =
                'Save Subscription Period';


            subscriptionIdInput.disabled =
                false;


            planNameInput.value =
                '';

            billingCycleInput.value =
                '';

            basePriceInput.value =
                '';

            includedUnitsInput.value =
                '';

            overageRateInput.value =
                '';

            customerNameInput.value =
                '';


            formErrorContainer.classList.remove(
                'show'
            );


            formErrorContainer.innerHTML =
                '';
        }


        /* =========================
           Show Form Errors
        ========================== */

        function showFormErrors(
            error
        ) {

            const errors =
                error.validationErrors ||
                {};

            const messages = [];


            Object.values(
                errors
            ).forEach(
                function (fieldErrors) {

                    if (
                        Array.isArray(
                            fieldErrors
                        )
                    ) {

                        fieldErrors.forEach(
                            function (message) {

                                messages.push(
                                    message
                                );
                            }
                        );

                    } else if (
                        fieldErrors
                    ) {

                        messages.push(
                            fieldErrors
                        );
                    }
                }
            );


            if (
                !messages.length
            ) {

                messages.push(
                    error.message ||
                    'Please check the entered values.'
                );
            }


            formErrorContainer.innerHTML = `
                <ul>
                    ${messages.map(
                        function (message) {

                            return `
                                <li>
                                    ${escapeHtml(message)}
                                </li>
                            `;
                        }
                    ).join('')}
                </ul>
            `;


            formErrorContainer.classList.add(
                'show'
            );
        }


        /* =========================
           Open Add Modal
        ========================== */

        async function openAddModal() {

            resetForm();


            savePeriodButton.disabled =
                true;

            savePeriodButton.textContent =
                'Loading...';


            try {

                await loadSubscriptions();


                openModal();

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to load subscriptions.',
                    'error'
                );

            } finally {

                savePeriodButton.disabled =
                    false;

                savePeriodButton.textContent =
                    'Save Subscription Period';
            }
        }


        /* =========================
           Open Edit Modal
        ========================== */

        async function openEditModal(
            id
        ) {

            resetForm();


            editingPeriodId =
                id;


            savePeriodButton.disabled =
                true;

            savePeriodButton.textContent =
                'Loading...';


            try {

                const result =
                    await request(
                        `/subscription-periods/${id}`
                    );


                const period =
                    result?.data;


                if (!period) {

                    throw new Error(
                        'Subscription period not found.'
                    );
                }


                await loadSubscriptions(
                    period.subscription_id
                );


                subscriptionIdInput.value =
                    String(
                        period.subscription_id
                    );


                updateSubscriptionDetails();


                startsAtInput.value =
                    toDateTimeLocal(
                        period.starts_at
                    );


                endsAtInput.value =
                    toDateTimeLocal(
                        period.ends_at
                    );


                /*
                 * Existing backend intentionally
                 * allows only dates to be changed.
                 */
                subscriptionIdInput.disabled =
                    true;


                modalTitle.textContent =
                    'Edit Subscription Period';


                savePeriodButton.textContent =
                    'Update Subscription Period';


                openModal();

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to load subscription period.',
                    'error'
                );

            } finally {

                savePeriodButton.disabled =
                    false;

                savePeriodButton.textContent =
                    editingPeriodId
                        ? 'Update Subscription Period'
                        : 'Save Subscription Period';
            }
        }


        /* =========================
           Delete Period
        ========================== */

        async function deletePeriod(
            id
        ) {

            const confirmed =
                window.confirm(
                    'Are you sure you want to delete this subscription period?'
                );


            if (!confirmed) {
                return;
            }


            try {

                await request(
                    `/subscription-periods/${id}`,
                    {
                        method:
                            'DELETE',
                    }
                );


                showAlert(
                    'Subscription period deleted successfully.'
                );


                await loadPeriods(
                    currentPage
                );

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to delete subscription period.',
                    'error'
                );
            }
        }


        /* =========================
           Render Periods
        ========================== */

        function renderPeriods(
            items
        ) {

            if (!items.length) {

                tableBody.innerHTML = `
                    <tr>

                        <td
                            colspan="9"
                            class="table-empty"
                        >
                            No subscription periods found.
                        </td>

                    </tr>
                `;

                return;
            }


            tableBody.innerHTML =
                items.map(
                    function (period) {

                        const customer =
                            period.subscription?.customer ||
                            null;


                        const plan =
                            period.plan ||
                            period.subscription?.plan ||
                            null;


                        const customerName =
                            customer?.name ||
                            `Customer #${period.subscription?.customer_id ?? '-'}`;


                        const customerCode =
                            customer?.code ||
                            '';


                        const planName =
                            plan?.name ||
                            `Plan #${period.plan_id}`;


                        const planCode =
                            plan?.code ||
                            '';


                        const billingCycle =
                            period.billing_cycle ||
                            '-';


                        return `

                            <tr>

                                <td>

                                    <div class="primary-text">
                                        ${escapeHtml(
                                            customerName
                                        )}
                                    </div>

                                    ${
                                        customerCode
                                            ? `
                                                <div class="secondary-text">
                                                    ${escapeHtml(
                                                        customerCode
                                                    )}
                                                </div>
                                            `
                                            : ''
                                    }

                                </td>


                                <td>

                                    <div class="primary-text">
                                        ${escapeHtml(
                                            planName
                                        )}
                                    </div>

                                    ${
                                        planCode
                                            ? `
                                                <div class="secondary-text">
                                                    ${escapeHtml(
                                                        planCode
                                                    )}
                                                </div>
                                            `
                                            : ''
                                    }

                                </td>


                                <td>

                                    ${formatDate(
                                        period.starts_at
                                    )}

                                </td>


                                <td>

                                    ${formatDate(
                                        period.ends_at
                                    )}

                                </td>


                                <td>

                                    ${formatMoney(
                                        period.base_price
                                    )}

                                </td>


                                <td>

                                    ${period.included_units ?? '-'}

                                </td>


                                <td>

                                    ${
                                        period.overage_rate !== null &&
                                        period.overage_rate !== undefined
                                            ? Number(
                                                period.overage_rate
                                            ).toFixed(6)
                                            : '-'
                                    }

                                </td>


                                <td>

                                    <span class="cycle-badge">
                                        ${escapeHtml(
                                            billingCycle
                                        )}
                                    </span>

                                </td>


                                <td>

                                    <div class="actions">

                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-small"
                                            onclick="editSubscriptionPeriod(${period.id})"
                                        >
                                            Edit
                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-small"
                                            onclick="removeSubscriptionPeriod(${period.id})"
                                        >
                                            Delete
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        `;
                    }
                ).join('');
        }


        /* =========================
           Render Pagination
        ========================== */

        function renderPagination(
            data
        ) {

            const current =
                Number(
                    data.current_page ||
                    1
                );


            const last =
                Number(
                    data.last_page ||
                    1
                );


            const total =
                Number(
                    data.total ||
                    0
                );


            const perPage =
                Number(
                    data.per_page ||
                    10
                );


            if (!total) {

                paginationContainer.innerHTML =
                    '';

                return;
            }


            const start =
                ((current - 1) *
                    perPage) +
                1;


            const end =
                Math.min(
                    current * perPage,
                    total
                );


            let html = `

                <div class="pagination-info">
                    Showing ${start}–${end} of ${total}
                </div>

                <div class="pagination">

            `;


            html += `

                <button
                    type="button"
                    ${current <= 1 ? 'disabled' : ''}
                    onclick="goToSubscriptionPeriodPage(${current - 1})"
                >
                    Prev
                </button>

            `;


            const maxPages =
                5;


            let startPage =
                Math.max(
                    1,
                    current - 2
                );


            let endPage =
                Math.min(
                    last,
                    startPage +
                    maxPages -
                    1
                );


            if (
                endPage -
                startPage +
                1 <
                maxPages
            ) {

                startPage =
                    Math.max(
                        1,
                        endPage -
                        maxPages +
                        1
                    );
            }


            for (
                let page = startPage;
                page <= endPage;
                page++
            ) {

                html += `

                    <button
                        type="button"
                        class="${page === current ? 'active' : ''}"
                        onclick="goToSubscriptionPeriodPage(${page})"
                    >
                        ${page}
                    </button>

                `;
            }


            html += `

                <button
                    type="button"
                    ${current >= last ? 'disabled' : ''}
                    onclick="goToSubscriptionPeriodPage(${current + 1})"
                >
                    Next
                </button>

            `;


            html += `
                </div>
            `;


            paginationContainer.innerHTML =
                html;
        }


        /* =========================
           Load Periods
        ========================== */

        async function loadPeriods(
            page = 1
        ) {

            currentPage =
                page;


            tableBody.innerHTML = `

                <tr>

                    <td
                        colspan="9"
                        class="table-loading"
                    >
                        Loading subscription periods...
                    </td>

                </tr>

            `;


            try {

                const params =
                    new URLSearchParams();


                params.set(
                    'page',
                    String(page)
                );


                params.set(
                    'per_page',
                    perPageSelect.value
                );


                params.set(
                    'search',
                    searchInput.value.trim()
                );


                const result =
                    await request(
                        `/subscription-periods/data?${params.toString()}`
                    );


                const data =
                    result?.data ||
                    {};


                renderPeriods(
                    data.items ||
                    []
                );


                renderPagination(
                    data
                );

            } catch (error) {

                tableBody.innerHTML = `

                    <tr>

                        <td
                            colspan="9"
                            class="table-error"
                        >
                            ${escapeHtml(
                                error.message ||
                                'Failed to load subscription periods.'
                            )}
                        </td>

                    </tr>

                `;


                paginationContainer.innerHTML =
                    '';
            }
        }


        /* =========================
           Form Submit
        ========================== */

        periodForm.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                formErrorContainer.classList.remove(
                    'show'
                );


                formErrorContainer.innerHTML =
                    '';


                const payload = {

                    subscription_id:
                        Number(
                            subscriptionIdInput.value
                        ),

                    starts_at:
                        startsAtInput.value,

                    ends_at:
                        endsAtInput.value ||
                        null,
                };


                /*
                 * Client-side date validation.
                 */

                if (
                    payload.starts_at &&
                    payload.ends_at &&
                    new Date(
                        payload.ends_at
                    ) <=
                    new Date(
                        payload.starts_at
                    )
                ) {

                    formErrorContainer.innerHTML = `
                        The period end must be after the period start.
                    `;

                    formErrorContainer.classList.add(
                        'show'
                    );

                    return;
                }


                savePeriodButton.disabled =
                    true;


                savePeriodButton.textContent =
                    editingPeriodId
                        ? 'Updating...'
                        : 'Saving...';


                try {

                    if (
                        editingPeriodId
                    ) {

                        await request(
                            `/subscription-periods/${editingPeriodId}`,
                            {
                                method:
                                    'PUT',

                                body:
                                    JSON.stringify({
                                        starts_at:
                                            payload.starts_at,

                                        ends_at:
                                            payload.ends_at,
                                    }),
                            }
                        );


                        showAlert(
                            'Subscription period updated successfully.'
                        );

                    } else {

                        await request(
                            '/subscription-periods',
                            {
                                method:
                                    'POST',

                                body:
                                    JSON.stringify(
                                        payload
                                    ),
                            }
                        );


                        showAlert(
                            'Subscription period created successfully.'
                        );
                    }


                    closeModal();


                    await loadPeriods(
                        1
                    );

                } catch (error) {

                    showFormErrors(
                        error
                    );

                } finally {

                    savePeriodButton.disabled =
                        false;


                    savePeriodButton.textContent =
                        editingPeriodId
                            ? 'Update Subscription Period'
                            : 'Save Subscription Period';
                }
            }
        );


        /* =========================
           Search
        ========================== */

        searchInput.addEventListener(
            'input',
            function () {

                clearTimeout(
                    searchTimer
                );


                searchTimer =
                    setTimeout(
                        function () {

                            loadPeriods(
                                1
                            );

                        },
                        300
                    );
            }
        );


        /* =========================
           Per Page
        ========================== */

        perPageSelect.addEventListener(
            'change',
            function () {

                loadPeriods(
                    1
                );
            }
        );


        /* =========================
           Modal Events
        ========================== */

        addPeriodButton.addEventListener(
            'click',
            openAddModal
        );


        closeModalButton.addEventListener(
            'click',
            closeModal
        );


        cancelModalButton.addEventListener(
            'click',
            closeModal
        );


        modalBackdrop.addEventListener(
            'click',
            closeModal
        );


        /* =========================
           Escape Key
        ========================== */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape' &&
                    !periodModal
                        .classList
                        .contains('hidden')
                ) {

                    closeModal();
                }
            }
        );


        /* =========================
           Global Functions
        ========================== */

        window.editSubscriptionPeriod =
            openEditModal;


        window.removeSubscriptionPeriod =
            deletePeriod;


        window.goToSubscriptionPeriodPage =
            loadPeriods;


        /* =========================
           Initial Load
        ========================== */

        loadPeriods(1);

    });

</script>

@endsection