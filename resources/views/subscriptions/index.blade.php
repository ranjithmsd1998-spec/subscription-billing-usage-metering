@extends('layouts.app')

@section('title', 'Subscriptions')

@section('page_heading', 'Subscriptions')

@section('content')

<style>
    .subscriptions-page {
        width: 100%;
    }

    /* =========================
       Page Header
    ========================== */

    .subscriptions-page .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .subscriptions-page .page-header-content h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
    }

    .subscriptions-page .page-header-content p {
        margin: 8px 0 0;
        font-size: 14px;
        color: #6b7280;
    }

    /* =========================
       Buttons
    ========================== */

    .subscriptions-page .btn {
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

    .subscriptions-page .btn-primary {
        background: #111827;
        color: #ffffff;
    }

    .subscriptions-page .btn-primary:hover {
        background: #1f2937;
    }

    .subscriptions-page .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .subscriptions-page .btn-secondary {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .subscriptions-page .btn-secondary:hover {
        background: #f9fafb;
    }

    .subscriptions-page .btn-danger {
        background: #ffffff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .subscriptions-page .btn-danger:hover {
        background: #fef2f2;
    }

    .subscriptions-page .btn-small {
        padding: 7px 11px;
        font-size: 12px;
        border-radius: 6px;
    }

    /* =========================
       Card
    ========================== */

    .subscriptions-page .card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    /* =========================
       Toolbar
    ========================== */

    .subscriptions-page .table-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 18px;
        border-bottom: 1px solid #e5e7eb;
    }

    .subscriptions-page .search-group {
        width: 100%;
        max-width: 420px;
    }

    .subscriptions-page .toolbar-right {
        display: flex;
        align-items: flex-end;
    }

    /* =========================
       Forms
    ========================== */

    .subscriptions-page .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .subscriptions-page .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .subscriptions-page .form-control {
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

    .subscriptions-page .form-control:focus {
        border-color: #6b7280;
        box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.12);
    }

    .subscriptions-page select.form-control {
        cursor: pointer;
    }

    .subscriptions-page .required {
        color: #dc2626;
    }

    /* =========================
       Table
    ========================== */

    .subscriptions-page .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .subscriptions-page .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1050px;
    }

    .subscriptions-page .data-table thead {
        background: #f9fafb;
    }

    .subscriptions-page .data-table th {
        padding: 12px 18px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
        white-space: nowrap;
    }

    .subscriptions-page .data-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #374151;
        vertical-align: middle;
    }

    .subscriptions-page .data-table tbody tr:hover {
        background: #fafafa;
    }

    .subscriptions-page .customer-name,
    .subscriptions-page .plan-name {
        font-weight: 600;
        color: #111827;
    }

    .subscriptions-page .secondary-text {
        margin-top: 3px;
        color: #6b7280;
        font-size: 11px;
    }

    .subscriptions-page .actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 7px;
    }

    /* =========================
       Status
    ========================== */

    .subscriptions-page .status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 9px;
        font-size: 11px;
        font-weight: 600;
    }

    .subscriptions-page .status-active {
        background: #dcfce7;
        color: #15803d;
    }

    .subscriptions-page .status-cancelled {
        background: #fee2e2;
        color: #b91c1c;
    }

    .subscriptions-page .status-expired {
        background: #f3f4f6;
        color: #6b7280;
    }

    .subscriptions-page .table-loading,
    .subscriptions-page .table-empty,
    .subscriptions-page .table-error {
        padding: 40px 18px !important;
        text-align: center !important;
        color: #6b7280 !important;
    }

    .subscriptions-page .table-error {
        color: #dc2626 !important;
    }

    /* =========================
       Pagination
    ========================== */

    .subscriptions-page .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 14px 18px;
        border-top: 1px solid #e5e7eb;
    }

    .subscriptions-page .pagination-info {
        font-size: 13px;
        color: #6b7280;
    }

    .subscriptions-page .pagination {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .subscriptions-page .pagination button {
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

    .subscriptions-page .pagination button:hover:not(:disabled) {
        background: #f9fafb;
    }

    .subscriptions-page .pagination button.active {
        background: #111827;
        border-color: #111827;
        color: #ffffff;
    }

    .subscriptions-page .pagination button:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    /* =========================
       Alert
    ========================== */

    .subscriptions-page .alert {
        display: none;
        margin-bottom: 18px;
        padding: 11px 14px;
        border-radius: 7px;
        font-size: 13px;
        border: 1px solid transparent;
    }

    .subscriptions-page .alert.show {
        display: block;
    }

    .subscriptions-page .alert-success {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .subscriptions-page .alert-error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    /* =========================
       Modal
    ========================== */

    .subscriptions-page .subscription-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .subscriptions-page .subscription-modal.hidden {
        display: none !important;
    }

    .subscriptions-page .subscription-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
    }

    .subscriptions-page .subscription-modal-dialog {
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

    .subscriptions-page .subscription-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .subscriptions-page .subscription-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .subscriptions-page .subscription-modal-description {
        margin: 5px 0 0;
        font-size: 12px;
        color: #6b7280;
    }

    .subscriptions-page .modal-close {
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

    .subscriptions-page .modal-close:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .subscriptions-page .subscription-modal-body {
        padding: 20px;
        max-height: calc(100vh - 190px);
        overflow-y: auto;
    }

    .subscriptions-page .subscription-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .subscriptions-page .form-full {
        grid-column: 1 / -1;
    }

    .subscriptions-page .form-error-box {
        display: none;
        margin-bottom: 16px;
        padding: 10px 12px;
        border-radius: 7px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        font-size: 12px;
    }

    .subscriptions-page .form-error-box.show {
        display: block;
    }

    .subscriptions-page .form-error-box ul {
        margin: 0;
        padding-left: 18px;
    }

    .subscriptions-page .subscription-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        padding: 14px 20px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
    }

    .subscriptions-page .field-help {
        margin-top: 2px;
        font-size: 11px;
        color: #9ca3af;
    }

    /* =========================
       Responsive
    ========================== */

    @media (max-width: 768px) {

        .subscriptions-page .page-header {
            flex-direction: column;
        }

        .subscriptions-page .table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .subscriptions-page .search-group {
            max-width: none;
        }

        .subscriptions-page .subscription-form-grid {
            grid-template-columns: 1fr;
        }

        .subscriptions-page .form-full {
            grid-column: auto;
        }

        .subscriptions-page .pagination-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .subscriptions-page .subscription-modal {
            padding: 10px;
        }

        .subscriptions-page .subscription-modal-dialog {
            max-height: calc(100vh - 20px);
        }
    }
</style>


<div class="subscriptions-page">

    {{-- =========================
         Page Header
    ========================== --}}

    <div class="page-header">

        <div class="page-header-content">

            <h1>
                Subscriptions
            </h1>

            <p>
                Manage customer subscriptions and billing periods.
            </p>

        </div>

        <button
            type="button"
            id="addSubscriptionButton"
            class="btn btn-primary"
        >
            + Add Subscription
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
         Subscription Card
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
                        placeholder="Search customer, code, plan, status..."
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
                            Merchant
                        </th>

                        <th>
                            Plan
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Period Start
                        </th>

                        <th>
                            Period End
                        </th>

                        <th style="text-align: right;">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody id="subscriptionsTableBody">

                    <tr>

                        <td
                            colspan="7"
                            class="table-loading"
                        >
                            Loading subscriptions...
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
         Subscription Modal
    ========================================================== --}}

    <div
        id="subscriptionModal"
        class="subscription-modal hidden"
        aria-hidden="true"
    >

        <div
            id="modalBackdrop"
            class="subscription-modal-backdrop"
        ></div>


        <div class="subscription-modal-dialog">


            {{-- Modal Header --}}

            <div class="subscription-modal-header">

                <div>

                    <h2
                        id="modalTitle"
                        class="subscription-modal-title"
                    >
                        Add Subscription
                    </h2>

                    <p class="subscription-modal-description">
                        Enter subscription details below.
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

            <form id="subscriptionForm">

                <div class="subscription-modal-body">


                    {{-- Validation Errors --}}

                    <div
                        id="formErrorContainer"
                        class="form-error-box"
                    ></div>


                    <div class="subscription-form-grid">


                        {{-- Merchant --}}

                        <div class="form-group">

                            <label
                                for="merchantIdInput"
                                class="form-label"
                            >
                                Merchant
                                <span class="required">*</span>
                            </label>

                            <select
                                id="merchantIdInput"
                                name="merchant_id"
                                class="form-control"
                                required
                            >
                                <option value="">
                                    Select Merchant
                                </option>
                            </select>

                        </div>


                        {{-- Customer --}}

                        <div class="form-group">

                            <label
                                for="customerIdInput"
                                class="form-label"
                            >
                                Customer
                                <span class="required">*</span>
                            </label>

                            <select
                                id="customerIdInput"
                                name="customer_id"
                                class="form-control"
                                required
                            >
                                <option value="">
                                    Select Customer
                                </option>
                            </select>

                        </div>


                        {{-- Plan --}}

                        <div class="form-group">

                            <label
                                for="planIdInput"
                                class="form-label"
                            >
                                Plan
                                <span class="required">*</span>
                            </label>

                            <select
                                id="planIdInput"
                                name="plan_id"
                                class="form-control"
                                required
                            >
                                <option value="">
                                    Select Plan
                                </option>
                            </select>

                        </div>


                        {{-- Status --}}

                        <div class="form-group">

                            <label
                                for="statusInput"
                                class="form-label"
                            >
                                Status
                                <span class="required">*</span>
                            </label>

                            <select
                                id="statusInput"
                                name="status"
                                class="form-control"
                                required
                            >

                                <option value="active">
                                    Active
                                </option>

                                <option value="cancelled">
                                    Cancelled
                                </option>

                                <option value="expired">
                                    Expired
                                </option>

                            </select>

                        </div>


                        {{-- Started At --}}

                        <div class="form-group">

                            <label
                                for="startedAtInput"
                                class="form-label"
                            >
                                Started At
                                <span class="required">*</span>
                            </label>

                            <input
                                type="datetime-local"
                                id="startedAtInput"
                                name="started_at"
                                class="form-control"
                                required
                            >

                        </div>


                        {{-- Current Period Start --}}

                        <div class="form-group">

                            <label
                                for="currentPeriodStartInput"
                                class="form-label"
                            >
                                Current Period Start
                                <span class="required">*</span>
                            </label>

                            <input
                                type="datetime-local"
                                id="currentPeriodStartInput"
                                name="current_period_start"
                                class="form-control"
                                required
                            >

                        </div>


                        {{-- Current Period End --}}

                        <div class="form-group">

                            <label
                                for="currentPeriodEndInput"
                                class="form-label"
                            >
                                Current Period End
                                <span class="required">*</span>
                            </label>

                            <input
                                type="datetime-local"
                                id="currentPeriodEndInput"
                                name="current_period_end"
                                class="form-control"
                                required
                            >

                        </div>


                        {{-- Cancelled At --}}

                        <div class="form-group">

                            <label
                                for="cancelledAtInput"
                                class="form-label"
                            >
                                Cancelled At
                            </label>

                            <input
                                type="datetime-local"
                                id="cancelledAtInput"
                                name="cancelled_at"
                                class="form-control"
                            >

                            <small class="field-help">
                                Required only when the subscription is cancelled.
                            </small>

                        </div>


                    </div>

                </div>


                {{-- Modal Footer --}}

                <div class="subscription-modal-footer">

                    <button
                        type="button"
                        id="cancelModalButton"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        id="saveSubscriptionButton"
                        class="btn btn-primary"
                    >
                        Save Subscription
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
                'subscriptionsTableBody'
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

        const addSubscriptionButton =
            document.getElementById(
                'addSubscriptionButton'
            );

        const subscriptionModal =
            document.getElementById(
                'subscriptionModal'
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

        const subscriptionForm =
            document.getElementById(
                'subscriptionForm'
            );

        const modalTitle =
            document.getElementById(
                'modalTitle'
            );

        const saveSubscriptionButton =
            document.getElementById(
                'saveSubscriptionButton'
            );

        const formErrorContainer =
            document.getElementById(
                'formErrorContainer'
            );

        const alertContainer =
            document.getElementById(
                'alertContainer'
            );

        const merchantIdInput =
            document.getElementById(
                'merchantIdInput'
            );

        const customerIdInput =
            document.getElementById(
                'customerIdInput'
            );

        const planIdInput =
            document.getElementById(
                'planIdInput'
            );

        const statusInput =
            document.getElementById(
                'statusInput'
            );

        const startedAtInput =
            document.getElementById(
                'startedAtInput'
            );

        const currentPeriodStartInput =
            document.getElementById(
                'currentPeriodStartInput'
            );

        const currentPeriodEndInput =
            document.getElementById(
                'currentPeriodEndInput'
            );

        const cancelledAtInput =
            document.getElementById(
                'cancelledAtInput'
            );


        let currentPage = 1;

        let editingSubscriptionId = null;

        let searchTimer = null;


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
           Status Badge
        ========================== */

        function statusBadge(
            status
        ) {

            if (
                status === 'active'
            ) {

                return `
                    <span class="status-badge status-active">
                        Active
                    </span>
                `;
            }


            if (
                status === 'cancelled'
            ) {

                return `
                    <span class="status-badge status-cancelled">
                        Cancelled
                    </span>
                `;
            }


            return `
                <span class="status-badge status-expired">
                    Expired
                </span>
            `;
        }


        /* =========================
           Load Merchants
        ========================== */

        async function loadMerchants(
            selectedId = null
        ) {

            const result =
                await request(
                    '/merchants/data?per_page=100'
                );


            const merchants =
                result?.data?.data ||
                result?.data?.items ||
                [];


            merchantIdInput.innerHTML = `
                <option value="">
                    Select Merchant
                </option>
            `;


            merchants.forEach(
                function (merchant) {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        merchant.id;

                    option.textContent =
                        `${merchant.name} (${merchant.code})`;

                    merchantIdInput.appendChild(
                        option
                    );
                }
            );


            if (
                selectedId !== null &&
                selectedId !== ''
            ) {

                merchantIdInput.value =
                    String(selectedId);
            }
        }


        /* =========================
           Load Customers
        ========================== */

        async function loadCustomers(
            selectedId = null,
            selectedMerchantId = null
        ) {

            const result =
                await request(
                    '/customers/data?per_page=100'
                );


            const customers =
                result?.data?.data ||
                result?.data?.items ||
                [];


            customerIdInput.innerHTML = `
                <option value="">
                    Select Customer
                </option>
            `;


            customers.forEach(
                function (customer) {

                    /*
                     * Only show customers
                     * belonging to selected merchant.
                     */

                    if (
                        selectedMerchantId &&
                        String(
                            customer.merchant_id
                        ) !==
                        String(
                            selectedMerchantId
                        )
                    ) {

                        return;
                    }


                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        customer.id;

                    option.dataset.merchantId =
                        customer.merchant_id || '';


                    option.textContent =
                        `${customer.name} (${customer.code})`;


                    customerIdInput.appendChild(
                        option
                    );
                }
            );


            if (
                selectedId !== null &&
                selectedId !== ''
            ) {

                customerIdInput.value =
                    String(selectedId);
            }
        }


        /* =========================
           Load Plans
        ========================== */

        async function loadPlans(
            selectedId = null,
            selectedMerchantId = null
        ) {

            const result =
                await request(
                    '/plans/data?per_page=100'
                );


            const plans =
                result?.data?.data ||
                result?.data?.items ||
                [];


            planIdInput.innerHTML = `
                <option value="">
                    Select Plan
                </option>
            `;


            plans.forEach(
                function (plan) {

                    /*
                     * Only show plans
                     * belonging to selected merchant.
                     */

                    if (
                        selectedMerchantId &&
                        String(
                            plan.merchant_id
                        ) !==
                        String(
                            selectedMerchantId
                        )
                    ) {

                        return;
                    }


                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        plan.id;

                    option.dataset.merchantId =
                        plan.merchant_id || '';


                    option.textContent =
                        `${plan.name} (${plan.code})`;


                    planIdInput.appendChild(
                        option
                    );
                }
            );


            if (
                selectedId !== null &&
                selectedId !== ''
            ) {

                planIdInput.value =
                    String(selectedId);
            }
        }


        /* =========================
           Merchant Change
        ========================== */

        merchantIdInput.addEventListener(
            'change',
            async function () {

                const merchantId =
                    merchantIdInput.value;


                customerIdInput.innerHTML = `
                    <option value="">
                        Loading Customers...
                    </option>
                `;


                planIdInput.innerHTML = `
                    <option value="">
                        Loading Plans...
                    </option>
                `;


                try {

                    await Promise.all([
                        loadCustomers(
                            null,
                            merchantId
                        ),

                        loadPlans(
                            null,
                            merchantId
                        ),
                    ]);

                } catch (error) {

                    customerIdInput.innerHTML = `
                        <option value="">
                            Select Customer
                        </option>
                    `;

                    planIdInput.innerHTML = `
                        <option value="">
                            Select Plan
                        </option>
                    `;

                    showAlert(
                        error.message ||
                        'Failed to load customer or plan data.',
                        'error'
                    );
                }
            }
        );


        /* =========================
           Open Modal
        ========================== */

        function openModal() {

            subscriptionModal.classList.remove(
                'hidden'
            );

            subscriptionModal.setAttribute(
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

            subscriptionModal.classList.add(
                'hidden'
            );

            subscriptionModal.setAttribute(
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

            subscriptionForm.reset();


            editingSubscriptionId =
                null;


            modalTitle.textContent =
                'Add Subscription';


            saveSubscriptionButton.textContent =
                'Save Subscription';


            statusInput.value =
                'active';


            customerIdInput.innerHTML = `
                <option value="">
                    Select Customer
                </option>
            `;


            planIdInput.innerHTML = `
                <option value="">
                    Select Plan
                </option>
            `;


            merchantIdInput.innerHTML = `
                <option value="">
                    Select Merchant
                </option>
            `;


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
           Add Subscription
        ========================== */

        async function openAddModal() {

            resetForm();


            saveSubscriptionButton.disabled =
                true;

            saveSubscriptionButton.textContent =
                'Loading...';


            try {

                await loadMerchants();

                await loadCustomers();

                await loadPlans();

                openModal();

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to load subscription form data.',
                    'error'
                );

            } finally {

                saveSubscriptionButton.disabled =
                    false;

                saveSubscriptionButton.textContent =
                    'Save Subscription';
            }
        }


        /* =========================
           Edit Subscription
        ========================== */

        async function openEditModal(
            id
        ) {

            resetForm();


            editingSubscriptionId =
                id;


            saveSubscriptionButton.disabled =
                true;

            saveSubscriptionButton.textContent =
                'Loading...';


            try {

                const result =
                    await request(
                        `/subscriptions/${id}`
                    );


                const subscription =
                    result?.data;


                if (!subscription) {

                    throw new Error(
                        'Subscription not found.'
                    );
                }


                const merchantId =
                    subscription.merchant_id;


                /*
                 * Load merchant first.
                 */

                await loadMerchants(
                    merchantId
                );


                /*
                 * Load customers and plans
                 * for selected merchant.
                 */

                await Promise.all([
                    loadCustomers(
                        subscription.customer_id,
                        merchantId
                    ),

                    loadPlans(
                        subscription.plan_id,
                        merchantId
                    ),
                ]);


                modalTitle.textContent =
                    'Edit Subscription';


                saveSubscriptionButton.textContent =
                    'Update Subscription';


                merchantIdInput.value =
                    subscription.merchant_id ??
                    '';


                customerIdInput.value =
                    subscription.customer_id ??
                    '';


                planIdInput.value =
                    subscription.plan_id ??
                    '';


                statusInput.value =
                    subscription.status ??
                    'active';


                startedAtInput.value =
                    toDateTimeLocal(
                        subscription.started_at
                    );


                currentPeriodStartInput.value =
                    toDateTimeLocal(
                        subscription.current_period_start
                    );


                currentPeriodEndInput.value =
                    toDateTimeLocal(
                        subscription.current_period_end
                    );


                cancelledAtInput.value =
                    toDateTimeLocal(
                        subscription.cancelled_at
                    );


                openModal();

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to load subscription.',
                    'error'
                );

            } finally {

                saveSubscriptionButton.disabled =
                    false;

                saveSubscriptionButton.textContent =
                    editingSubscriptionId
                        ? 'Update Subscription'
                        : 'Save Subscription';
            }
        }


        /* =========================
           Delete Subscription
        ========================== */

        async function deleteSubscription(
            id,
            customerName
        ) {

            const confirmed =
                window.confirm(
                    `Are you sure you want to delete the subscription for "${customerName}"?`
                );


            if (!confirmed) {
                return;
            }


            try {

                await request(
                    `/subscriptions/${id}`,
                    {
                        method:
                            'DELETE',
                    }
                );


                showAlert(
                    'Subscription deleted successfully.'
                );


                await loadSubscriptions(
                    currentPage
                );

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to delete subscription.',
                    'error'
                );
            }
        }


        /* =========================
           Render Subscriptions
        ========================== */

        function renderSubscriptions(
            items
        ) {

            if (!items.length) {

                tableBody.innerHTML = `
                    <tr>

                        <td
                            colspan="7"
                            class="table-empty"
                        >
                            No subscriptions found.
                        </td>

                    </tr>
                `;

                return;
            }


            tableBody.innerHTML =
                items.map(
                    function (subscription) {

                        const customer =
                            subscription.customer ||
                            null;


                        const merchant =
                            subscription.merchant ||
                            null;


                        const plan =
                            subscription.plan ||
                            null;


                        const customerName =
                            customer?.name ||
                            '-';


                        const customerCode =
                            customer?.code ||
                            '';


                        const merchantName =
                            merchant?.name ||
                            '-';


                        const planName =
                            plan?.name ||
                            '-';


                        const planCode =
                            plan?.code ||
                            '';


                        const safeCustomerName =
                            String(
                                customerName
                            )
                            .replace(
                                /\\/g,
                                '\\\\'
                            )
                            .replace(
                                /'/g,
                                "\\'"
                            );


                        return `

                            <tr>

                                <td>

                                    <div class="customer-name">
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

                                    ${escapeHtml(
                                        merchantName
                                    )}

                                </td>


                                <td>

                                    <div class="plan-name">
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

                                    ${statusBadge(
                                        subscription.status
                                    )}

                                </td>


                                <td>

                                    ${formatDate(
                                        subscription.current_period_start
                                    )}

                                </td>


                                <td>

                                    ${formatDate(
                                        subscription.current_period_end
                                    )}

                                </td>


                                <td>

                                    <div class="actions">

                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-small"
                                            onclick="editSubscription(${subscription.id})"
                                        >
                                            Edit
                                        </button>


                                        <button
                                            type="button"
                                            class="btn btn-danger btn-small"
                                            onclick="removeSubscription(${subscription.id}, '${safeCustomerName}')"
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
                    onclick="goToSubscriptionPage(${current - 1})"
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
                        onclick="goToSubscriptionPage(${page})"
                    >
                        ${page}
                    </button>

                `;
            }


            html += `

                <button
                    type="button"
                    ${current >= last ? 'disabled' : ''}
                    onclick="goToSubscriptionPage(${current + 1})"
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
           Load Subscriptions
        ========================== */

        async function loadSubscriptions(
            page = 1
        ) {

            currentPage =
                page;


            tableBody.innerHTML = `

                <tr>

                    <td
                        colspan="7"
                        class="table-loading"
                    >
                        Loading subscriptions...
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
                        `/subscriptions/data?${params.toString()}`
                    );


                const data =
                    result?.data ||
                    {};


                renderSubscriptions(
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
                            colspan="7"
                            class="table-error"
                        >
                            ${escapeHtml(
                                error.message ||
                                'Failed to load subscriptions.'
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

        subscriptionForm.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                formErrorContainer.classList.remove(
                    'show'
                );


                formErrorContainer.innerHTML =
                    '';


                const payload = {

                    merchant_id:
                        Number(
                            merchantIdInput.value
                        ),

                    customer_id:
                        Number(
                            customerIdInput.value
                        ),

                    plan_id:
                        Number(
                            planIdInput.value
                        ),

                    status:
                        statusInput.value,

                    started_at:
                        startedAtInput.value,

                    current_period_start:
                        currentPeriodStartInput.value,

                    current_period_end:
                        currentPeriodEndInput.value,

                    cancelled_at:
                        cancelledAtInput.value ||
                        null,
                };


                /*
                 * Client-side date validation.
                 */

                if (
                    payload.current_period_start &&
                    payload.current_period_end &&
                    new Date(
                        payload.current_period_end
                    ) <
                    new Date(
                        payload.current_period_start
                    )
                ) {

                    formErrorContainer.innerHTML = `
                        Current period end must be after or equal to current period start.
                    `;

                    formErrorContainer.classList.add(
                        'show'
                    );

                    return;
                }


                saveSubscriptionButton.disabled =
                    true;


                saveSubscriptionButton.textContent =
                    editingSubscriptionId
                        ? 'Updating...'
                        : 'Saving...';


                try {

                    if (
                        editingSubscriptionId
                    ) {

                        await request(
                            `/subscriptions/${editingSubscriptionId}`,
                            {
                                method:
                                    'PUT',

                                body:
                                    JSON.stringify(
                                        payload
                                    ),
                            }
                        );


                        showAlert(
                            'Subscription updated successfully.'
                        );

                    } else {

                        await request(
                            '/subscriptions',
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
                            'Subscription created successfully.'
                        );
                    }


                    closeModal();


                    await loadSubscriptions(
                        1
                    );

                } catch (error) {

                    showFormErrors(
                        error
                    );

                } finally {

                    saveSubscriptionButton.disabled =
                        false;


                    saveSubscriptionButton.textContent =
                        editingSubscriptionId
                            ? 'Update Subscription'
                            : 'Save Subscription';
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

                            loadSubscriptions(
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

                loadSubscriptions(
                    1
                );
            }
        );


        /* =========================
           Modal Events
        ========================== */

        addSubscriptionButton.addEventListener(
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
                    !subscriptionModal
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

        window.editSubscription =
            openEditModal;


        window.removeSubscription =
            deleteSubscription;


        window.goToSubscriptionPage =
            loadSubscriptions;


        /* =========================
           Initial Load
        ========================== */

        loadSubscriptions(1);

    });

</script>

@endsection