@extends('layouts.app')

@section('title', 'Customers')

@section('page_heading', 'Customers')

@section('content')

<style>
    .customers-page {
        width: 100%;
    }

    /* =========================
       Page Header
    ========================== */

    .customers-page .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .customers-page .page-header-content h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        line-height: 1.3;
    }

    .customers-page .page-header-content p {
        margin: 8px 0 0;
        font-size: 14px;
        color: #6b7280;
    }

    /* =========================
       Buttons
    ========================== */

    .customers-page .btn {
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
        text-decoration: none;
        white-space: nowrap;
    }

    .customers-page .btn-primary {
        background: #111827;
        color: #ffffff;
    }

    .customers-page .btn-primary:hover {
        background: #1f2937;
    }

    .customers-page .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .customers-page .btn-secondary {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .customers-page .btn-secondary:hover {
        background: #f9fafb;
    }

    .customers-page .btn-danger {
        background: #ffffff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .customers-page .btn-danger:hover {
        background: #fef2f2;
    }

    .customers-page .btn-small {
        padding: 7px 11px;
        font-size: 12px;
        border-radius: 6px;
    }

    /* =========================
       Card
    ========================== */

    .customers-page .card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    /* =========================
       Toolbar
    ========================== */

    .customers-page .table-toolbar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        padding: 16px 18px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .customers-page .search-group {
        width: 100%;
        max-width: 380px;
    }

    .customers-page .toolbar-right {
        display: flex;
        align-items: flex-end;
        gap: 12px;
    }

    /* =========================
       Form
    ========================== */

    .customers-page .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .customers-page .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .customers-page .form-control {
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

    .customers-page textarea.form-control {
        height: auto;
        min-height: 100px;
        resize: vertical;
    }

    .customers-page .form-control:focus {
        border-color: #6b7280;
        box-shadow: 0 0 0 2px rgba(107, 114, 128, 0.12);
    }

    .customers-page select.form-control {
        cursor: pointer;
    }

    .required {
        color: #dc2626;
    }

    /* =========================
       Table
    ========================== */

    .customers-page .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .customers-page .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .customers-page .data-table thead {
        background: #f9fafb;
    }

    .customers-page .data-table th {
        padding: 12px 18px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
        white-space: nowrap;
    }

    .customers-page .data-table td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        color: #374151;
        vertical-align: middle;
    }

    .customers-page .data-table tbody tr:hover {
        background: #fafafa;
    }

    .customers-page .customer-name {
        font-weight: 600;
        color: #111827;
    }

    .customers-page .customer-code {
        color: #6b7280;
        font-family: monospace;
        font-size: 12px;
    }

    .customers-page .merchant-name {
        font-weight: 500;
        color: #374151;
    }

    .customers-page .actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 7px;
    }

    .customers-page .status-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 9px;
        font-size: 11px;
        font-weight: 600;
    }

    .customers-page .status-active {
        background: #dcfce7;
        color: #15803d;
    }

    .customers-page .status-inactive {
        background: #f3f4f6;
        color: #6b7280;
    }

    .customers-page .table-loading,
    .customers-page .table-empty,
    .customers-page .table-error {
        padding: 40px 18px !important;
        text-align: center !important;
        color: #6b7280 !important;
    }

    .customers-page .table-error {
        color: #dc2626 !important;
    }

    /* =========================
       Pagination
    ========================== */

    .customers-page .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 14px 18px;
        border-top: 1px solid #e5e7eb;
    }

    .customers-page .pagination-info {
        font-size: 13px;
        color: #6b7280;
    }

    .customers-page .pagination {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .customers-page .pagination button {
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

    .customers-page .pagination button:hover:not(:disabled) {
        background: #f9fafb;
    }

    .customers-page .pagination button.active {
        background: #111827;
        border-color: #111827;
        color: #ffffff;
    }

    .customers-page .pagination button:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    /* =========================
       Alert
    ========================== */

    .customers-page .alert {
        display: none;
        margin-bottom: 18px;
        padding: 11px 14px;
        border-radius: 7px;
        font-size: 13px;
        border: 1px solid transparent;
    }

    .customers-page .alert.show {
        display: block;
    }

    .customers-page .alert-success {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #047857;
    }

    .customers-page .alert-error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    /* =========================
       Modal
    ========================== */

    .customers-page .customer-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .customers-page .customer-modal.hidden {
        display: none !important;
    }

    .customers-page .customer-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
    }

    .customers-page .customer-modal-dialog {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 680px;
        max-height: calc(100vh - 40px);
        overflow: hidden;
        background: #ffffff;
        border-radius: 10px;
        box-shadow:
            0 20px 50px rgba(0, 0, 0, 0.20);
    }

    .customers-page .customer-modal-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .customers-page .customer-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .customers-page .customer-modal-description {
        margin: 5px 0 0;
        font-size: 12px;
        color: #6b7280;
    }

    .customers-page .modal-close {
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

    .customers-page .modal-close:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .customers-page .customer-modal-body {
        padding: 20px;
        max-height: calc(100vh - 190px);
        overflow-y: auto;
    }

    .customers-page .customer-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .customers-page .customer-form-full {
        grid-column: 1 / -1;
    }

    .customers-page .form-error-box {
        display: none;
        margin-bottom: 16px;
        padding: 10px 12px;
        border-radius: 7px;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
        font-size: 12px;
    }

    .customers-page .form-error-box.show {
        display: block;
    }

    .customers-page .form-error-box ul {
        margin: 0;
        padding-left: 18px;
    }

    .customers-page .customer-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        padding: 14px 20px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
    }

    .customers-page .metadata-help {
        display: block;
        margin-top: 5px;
        color: #9ca3af;
        font-size: 11px;
    }

    /* =========================
       Responsive
    ========================== */

    @media (max-width: 768px) {

        .customers-page .page-header {
            flex-direction: column;
        }

        .customers-page .table-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .customers-page .search-group {
            max-width: none;
        }

        .customers-page .toolbar-right {
            justify-content: flex-start;
        }

        .customers-page .customer-form-grid {
            grid-template-columns: 1fr;
        }

        .customers-page .customer-form-full {
            grid-column: auto;
        }

        .customers-page .pagination-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }

        .customers-page .customer-modal {
            padding: 10px;
        }

        .customers-page .customer-modal-dialog {
            max-height: calc(100vh - 20px);
        }
    }
</style>


<div class="customers-page">

    {{-- =========================
         Page Header
    ========================== --}}

    <div class="page-header">

        <div class="page-header-content">

            <h1>
                Customers
            </h1>

            <p>
                Manage customers belonging to your merchants.
            </p>

        </div>

        <button
            type="button"
            id="addCustomerButton"
            class="btn btn-primary"
        >
            + Add Customer
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
         Customer Card
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
                        placeholder="Search name, code, email, phone..."
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
             Customer Table
        ========================== --}}

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
                            Merchant
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            Status
                        </th>

                        <th style="text-align: right;">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody id="customersTableBody">

                    <tr>

                        <td
                            colspan="7"
                            class="table-loading"
                        >
                            Loading customers...
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
         IMPORTANT:
         Modal is INSIDE .customers-page
         so scoped CSS will apply correctly.
    ========================================================== --}}

    <div
        id="customerModal"
        class="customer-modal hidden"
        aria-hidden="true"
    >

        <div
            id="modalBackdrop"
            class="customer-modal-backdrop"
        ></div>


        <div class="customer-modal-dialog">


            {{-- Modal Header --}}

            <div class="customer-modal-header">

                <div>

                    <h2
                        id="modalTitle"
                        class="customer-modal-title"
                    >
                        Add Customer
                    </h2>

                    <p class="customer-modal-description">
                        Enter customer details below.
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


            {{-- Customer Form --}}

            <form id="customerForm">

                <div class="customer-modal-body">


                    {{-- Validation errors --}}

                    <div
                        id="formErrorContainer"
                        class="form-error-box"
                    ></div>


                    <div class="customer-form-grid">


                        {{-- Merchant --}}

                        <div class="customer-form-full">

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

                        </div>


                        {{-- Customer Code --}}

                        <div class="form-group">

                            <label
                                for="codeInput"
                                class="form-label"
                            >
                                Customer Code
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="codeInput"
                                name="code"
                                class="form-control"
                                maxlength="50"
                                placeholder="CUS001"
                                required
                            >

                        </div>


                        {{-- Customer Name --}}

                        <div class="form-group">

                            <label
                                for="nameInput"
                                class="form-label"
                            >
                                Customer Name
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                id="nameInput"
                                name="name"
                                class="form-control"
                                maxlength="255"
                                placeholder="John Customer"
                                required
                            >

                        </div>


                        {{-- Email --}}

                        <div class="form-group">

                            <label
                                for="emailInput"
                                class="form-label"
                            >
                                Email
                            </label>

                            <input
                                type="email"
                                id="emailInput"
                                name="email"
                                class="form-control"
                                maxlength="255"
                                placeholder="customer@example.com"
                            >

                        </div>


                        {{-- Phone --}}

                        <div class="form-group">

                            <label
                                for="phoneInput"
                                class="form-label"
                            >
                                Phone
                            </label>

                            <input
                                type="text"
                                id="phoneInput"
                                name="phone"
                                class="form-control"
                                maxlength="20"
                                placeholder="+91 9876543210"
                            >

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

                                <option value="inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>


                        {{-- Metadata --}}

                        <div class="customer-form-full">

                            <div class="form-group">

                                <label
                                    for="metadataInput"
                                    class="form-label"
                                >
                                    Metadata
                                </label>

                                <textarea
                                    id="metadataInput"
                                    name="metadata"
                                    class="form-control"
                                    rows="4"
                                    placeholder='{"source":"web"}'
                                ></textarea>

                                <small class="metadata-help">
                                    Optional JSON object.
                                </small>

                            </div>

                        </div>


                    </div>

                </div>


                {{-- Modal Footer --}}

                <div class="customer-modal-footer">

                    <button
                        type="button"
                        id="cancelModalButton"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        id="saveCustomerButton"
                        class="btn btn-primary"
                    >
                        Save Customer
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
                'customersTableBody'
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

        const addCustomerButton =
            document.getElementById(
                'addCustomerButton'
            );

        const customerModal =
            document.getElementById(
                'customerModal'
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

        const customerForm =
            document.getElementById(
                'customerForm'
            );

        const modalTitle =
            document.getElementById(
                'modalTitle'
            );

        const saveCustomerButton =
            document.getElementById(
                'saveCustomerButton'
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

        const codeInput =
            document.getElementById(
                'codeInput'
            );

        const nameInput =
            document.getElementById(
                'nameInput'
            );

        const emailInput =
            document.getElementById(
                'emailInput'
            );

        const phoneInput =
            document.getElementById(
                'phoneInput'
            );

        const statusInput =
            document.getElementById(
                'statusInput'
            );

        const metadataInput =
            document.getElementById(
                'metadataInput'
            );


        let currentPage = 1;

        let editingCustomerId = null;

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


            return `
                <span class="status-badge status-inactive">
                    Inactive
                </span>
            `;
        }


        /* =========================
           Load Merchants
        ========================== */

        async function loadMerchants(
            selectedMerchantId = null
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
                selectedMerchantId !== null &&
                selectedMerchantId !== ''
            ) {

                merchantIdInput.value =
                    String(
                        selectedMerchantId
                    );
            }
        }


        /* =========================
           Open Modal
        ========================== */

        function openModal() {

            customerModal.classList.remove(
                'hidden'
            );

            customerModal.setAttribute(
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

            customerModal.classList.add(
                'hidden'
            );

            customerModal.setAttribute(
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

            customerForm.reset();

            editingCustomerId =
                null;


            modalTitle.textContent =
                'Add Customer';


            saveCustomerButton.textContent =
                'Save Customer';


            statusInput.value =
                'active';


            formErrorContainer.classList.remove(
                'show'
            );


            formErrorContainer.innerHTML =
                '';


            merchantIdInput.innerHTML = `
                <option value="">
                    Select Merchant
                </option>
            `;
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
           Add Customer
        ========================== */

        async function openAddModal() {

            resetForm();


            saveCustomerButton.disabled =
                true;

            saveCustomerButton.textContent =
                'Loading...';


            try {

                await loadMerchants();

                openModal();

                codeInput.focus();

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to load merchants.',
                    'error'
                );

            } finally {

                saveCustomerButton.disabled =
                    false;

                saveCustomerButton.textContent =
                    'Save Customer';
            }
        }


        /* =========================
           Edit Customer
        ========================== */

        async function openEditModal(
            id
        ) {

            resetForm();

            editingCustomerId =
                id;


            saveCustomerButton.disabled =
                true;

            saveCustomerButton.textContent =
                'Loading...';


            try {

                const result =
                    await request(
                        `/customers/${id}`
                    );


                const customer =
                    result?.data;


                if (!customer) {

                    throw new Error(
                        'Customer not found.'
                    );
                }


                await loadMerchants(
                    customer.merchant_id
                );


                modalTitle.textContent =
                    'Edit Customer';


                saveCustomerButton.textContent =
                    'Update Customer';


                merchantIdInput.value =
                    customer.merchant_id ??
                    '';


                codeInput.value =
                    customer.code ??
                    '';


                nameInput.value =
                    customer.name ??
                    '';


                emailInput.value =
                    customer.email ??
                    '';


                phoneInput.value =
                    customer.phone ??
                    '';


                statusInput.value =
                    customer.status ??
                    'active';


                if (
                    customer.metadata !==
                    null &&
                    customer.metadata !==
                    undefined
                ) {

                    if (
                        typeof customer.metadata ===
                        'object'
                    ) {

                        metadataInput.value =
                            JSON.stringify(
                                customer.metadata,
                                null,
                                2
                            );

                    } else {

                        metadataInput.value =
                            customer.metadata;
                    }

                } else {

                    metadataInput.value =
                        '';
                }


                openModal();

                codeInput.focus();

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to load customer.',
                    'error'
                );

            } finally {

                saveCustomerButton.disabled =
                    false;

                saveCustomerButton.textContent =
                    editingCustomerId
                        ? 'Update Customer'
                        : 'Save Customer';
            }
        }


        /* =========================
           Delete Customer
        ========================== */

        async function deleteCustomer(
            id,
            customerName
        ) {

            const confirmed =
                window.confirm(
                    `Are you sure you want to delete customer "${customerName}"?`
                );


            if (!confirmed) {
                return;
            }


            try {

                await request(
                    `/customers/${id}`,
                    {
                        method:
                            'DELETE',
                    }
                );


                showAlert(
                    'Customer deleted successfully.'
                );


                await loadCustomers(
                    currentPage
                );

            } catch (error) {

                showAlert(
                    error.message ||
                    'Failed to delete customer.',
                    'error'
                );
            }
        }


        /* =========================
           Render Customers
        ========================== */

        function renderCustomers(
            items
        ) {

            if (!items.length) {

                tableBody.innerHTML = `
                    <tr>
                        <td
                            colspan="7"
                            class="table-empty"
                        >
                            No customers found.
                        </td>
                    </tr>
                `;

                return;
            }


            tableBody.innerHTML =
                items.map(
                    function (customer) {

                        const merchant =
                            customer.merchant ||
                            null;


                        const merchantName =
                            merchant?.name ||
                            customer.merchant_name ||
                            '-';


                        const customerName =
                            escapeHtml(
                                customer.name ||
                                '-'
                            );


                        const customerCode =
                            escapeHtml(
                                customer.code ||
                                '-'
                            );


                        const email =
                            escapeHtml(
                                customer.email ||
                                '-'
                            );


                        const phone =
                            escapeHtml(
                                customer.phone ||
                                '-'
                            );


                        const safeName =
                            String(
                                customer.name ||
                                ''
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
                                        ${customerName}
                                    </div>
                                </td>

                                <td>
                                    <div class="customer-code">
                                        ${customerCode}
                                    </div>
                                </td>

                                <td>
                                    <div class="merchant-name">
                                        ${escapeHtml(
                                            merchantName
                                        )}
                                    </div>
                                </td>

                                <td>
                                    ${email}
                                </td>

                                <td>
                                    ${phone}
                                </td>

                                <td>
                                    ${statusBadge(
                                        customer.status
                                    )}
                                </td>

                                <td>

                                    <div class="actions">

                                        <button
                                            type="button"
                                            class="btn btn-secondary btn-small"
                                            onclick="editCustomer(${customer.id})"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="btn btn-danger btn-small"
                                            onclick="removeCustomer(${customer.id}, '${safeName}')"
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
                    onclick="goToPage(${current - 1})"
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
                        onclick="goToPage(${page})"
                    >
                        ${page}
                    </button>

                `;
            }


            html += `

                <button
                    type="button"
                    ${current >= last ? 'disabled' : ''}
                    onclick="goToPage(${current + 1})"
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
           Load Customers
        ========================== */

        async function loadCustomers(
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
                        Loading customers...
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
                        `/customers/data?${params.toString()}`
                    );


                const data =
                    result?.data ||
                    {};


                renderCustomers(
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
                                'Failed to load customers.'
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

        customerForm.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                formErrorContainer.classList.remove(
                    'show'
                );


                formErrorContainer.innerHTML =
                    '';


                /*
                 * Metadata
                 */

                const metadataText =
                    metadataInput.value.trim();


                let metadata =
                    null;


                if (
                    metadataText !== ''
                ) {

                    try {

                        metadata =
                            JSON.parse(
                                metadataText
                            );


                        if (
                            typeof metadata !==
                                'object' ||
                            Array.isArray(
                                metadata
                            ) ||
                            metadata === null
                        ) {

                            throw new Error(
                                'Metadata must be a JSON object.'
                            );
                        }

                    } catch (error) {

                        formErrorContainer.innerHTML =
                            'Metadata must be valid JSON object.';

                        formErrorContainer.classList.add(
                            'show'
                        );

                        return;
                    }
                }


                const payload = {

                    merchant_id:
                        Number(
                            merchantIdInput.value
                        ),

                    code:
                        codeInput.value.trim(),

                    name:
                        nameInput.value.trim(),

                    email:
                        emailInput.value.trim() ||
                        null,

                    phone:
                        phoneInput.value.trim() ||
                        null,

                    status:
                        statusInput.value,

                    metadata:
                        metadata,
                };


                saveCustomerButton.disabled =
                    true;


                saveCustomerButton.textContent =
                    editingCustomerId
                        ? 'Updating...'
                        : 'Saving...';


                try {

                    if (
                        editingCustomerId
                    ) {

                        await request(
                            `/customers/${editingCustomerId}`,
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
                            'Customer updated successfully.'
                        );

                    } else {

                        await request(
                            '/customers',
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
                            'Customer created successfully.'
                        );
                    }


                    closeModal();


                    await loadCustomers(
                        1
                    );

                } catch (error) {

                    showFormErrors(
                        error
                    );

                } finally {

                    saveCustomerButton.disabled =
                        false;


                    saveCustomerButton.textContent =
                        editingCustomerId
                            ? 'Update Customer'
                            : 'Save Customer';
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

                            loadCustomers(
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

                loadCustomers(
                    1
                );
            }
        );


        /* =========================
           Modal Events
        ========================== */

        addCustomerButton.addEventListener(
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
           Escape
        ========================== */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape' &&
                    !customerModal
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

        window.editCustomer =
            openEditModal;


        window.removeCustomer =
            deleteCustomer;


        window.goToPage =
            loadCustomers;


        /* =========================
           Initial Load
        ========================== */

        loadCustomers(1);

    });

</script>

@endsection