@extends('layouts.app')

@section('title', 'Usage Events')
@section('page_heading', 'Usage Events')

@section('content')

<style>
    .page-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .page-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }

    .search-input {
        width: 320px;
        max-width: 100%;
        height: 40px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0 13px;
        font-size: 14px;
        outline: none;
    }

    .search-input:focus,
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.10);
    }

    .per-page-select {
        height: 40px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0 10px;
        font-size: 14px;
        background: #ffffff;
        outline: none;
    }

    .btn {
        border: 0;
        border-radius: 8px;
        padding: 9px 15px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.15s ease;
    }

    .btn-primary {
        background: #4f46e5;
        color: #ffffff;
    }

    .btn-primary:hover {
        background: #4338ca;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #e5e7eb;
    }

    .btn-warning {
        background: #f59e0b;
        color: #ffffff;
    }

    .btn-warning:hover {
        background: #d97706;
    }

    .btn-small {
        padding: 6px 10px;
        font-size: 12px;
    }

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1250px;
    }

    .data-table th {
        background: #f9fafb;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        text-align: left;
        padding: 12px 14px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .data-table td {
        color: #374151;
        font-size: 13px;
        padding: 13px 14px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .data-table tr:hover {
        background: #fafafa;
    }

    .customer-name {
        font-weight: 600;
        color: #111827;
    }

    .sub-text {
        margin-top: 3px;
        color: #9ca3af;
        font-size: 11px;
    }

    .idempotency-key {
        max-width: 190px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-family: monospace;
        font-size: 11px;
        color: #4b5563;
    }

    .usage-value {
        font-weight: 700;
        color: #111827;
    }

    .unit-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 11px;
        font-weight: 600;
    }

    .period-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        background: #f3f4f6;
        color: #4b5563;
        font-size: 11px;
        font-weight: 600;
    }

    .actions {
        display: flex;
        gap: 6px;
    }

    .empty-state {
        padding: 45px 20px;
        text-align: center;
        color: #9ca3af;
        font-size: 14px;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 15px 20px;
        border-top: 1px solid #e5e7eb;
    }

    .pagination-info {
        color: #6b7280;
        font-size: 13px;
    }

    .pagination {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .page-btn {
        min-width: 34px;
        height: 34px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        border-radius: 7px;
        cursor: pointer;
        font-size: 13px;
        color: #374151;
    }

    .page-btn:hover {
        background: #f3f4f6;
    }

    .page-btn.active {
        background: #4f46e5;
        border-color: #4f46e5;
        color: #ffffff;
    }

    .page-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Modal */

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal {
        width: 100%;
        max-width: 760px;
        max-height: 92vh;
        overflow-y: auto;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.20);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #111827;
    }

    .modal-close {
        border: 0;
        background: transparent;
        font-size: 25px;
        line-height: 1;
        color: #6b7280;
        cursor: pointer;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 15px 20px;
        border-top: 1px solid #e5e7eb;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .required {
        color: #dc2626;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        color: #111827;
        background: #ffffff;
        outline: none;
    }

    .form-input:disabled,
    .form-select:disabled {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }

    .form-textarea {
        min-height: 100px;
        resize: vertical;
        font-family: monospace;
        font-size: 12px;
    }

    .help-text {
        color: #9ca3af;
        font-size: 11px;
        line-height: 1.4;
    }

    .field-error {
        color: #dc2626;
        font-size: 11px;
        display: none;
    }

    .alert-box {
        display: none;
        padding: 11px 13px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-size: 13px;
    }

    .alert-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .details-card {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 13px;
        margin-top: 3px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
    }

    .detail-item {
        min-width: 0;
    }

    .detail-label {
        font-size: 10px;
        color: #9ca3af;
        margin-bottom: 3px;
    }

    .detail-value {
        font-size: 12px;
        color: #374151;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    @media (max-width: 900px) {
        .page-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .toolbar-left {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input {
            width: 100%;
        }

        .pagination-wrapper {
            flex-direction: column;
            align-items: stretch;
        }

        .pagination {
            justify-content: center;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: auto;
        }

        .details-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 600px) {
        .details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="alertBox" class="alert-box"></div>

<div class="page-card">

    <div class="page-toolbar">

        <div class="toolbar-left">
            <input
                type="text"
                id="searchInput"
                class="search-input"
                placeholder="Search customer, subscription, idempotency key..."
            >

            <select id="perPageInput" class="per-page-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            onclick="openCreateModal()"
        >
            + Add Usage Event
        </button>

    </div>

    <div class="table-wrapper">

        <table class="data-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Subscription</th>
                    <th>Period</th>
                    <th>Usage</th>
                    <th>Occurred At</th>
                    <th>Idempotency Key</th>
                    <th>Metadata</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="usageEventsTableBody">

                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            Loading usage events...
                        </div>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

    <div class="pagination-wrapper">

        <div
            id="paginationInfo"
            class="pagination-info"
        >
            Showing 0 of 0
        </div>

        <div
            id="pagination"
            class="pagination"
        ></div>

    </div>

</div>


{{-- Add / Edit Modal --}}

<div
    id="usageEventModal"
    class="modal-overlay"
>
    <div class="modal">

        <div class="modal-header">

            <h2
                id="modalTitle"
                class="modal-title"
            >
                Add Usage Event
            </h2>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal()"
            >
                &times;
            </button>

        </div>

        <div class="modal-body">

            <div
                id="modalAlert"
                class="alert-box"
            ></div>

            <form id="usageEventForm">

                <input
                    type="hidden"
                    id="usageEventId"
                >

                <div class="form-grid">

                    {{-- Merchant --}}

                    <div class="form-group">

                        <label class="form-label">
                            Merchant <span class="required">*</span>
                        </label>

                        <select
                            id="merchantIdInput"
                            class="form-select"
                            required
                        >
                            <option value="">
                                Select Merchant
                            </option>
                        </select>

                        <div
                            id="merchantIdError"
                            class="field-error"
                        ></div>

                    </div>


                    {{-- Customer --}}

                    <div class="form-group">

                        <label class="form-label">
                            Customer <span class="required">*</span>
                        </label>

                        <select
                            id="customerIdInput"
                            class="form-select"
                            required
                            disabled
                        >
                            <option value="">
                                Select Customer
                            </option>
                        </select>

                        <div
                            id="customerIdError"
                            class="field-error"
                        ></div>

                    </div>


                    {{-- Subscription --}}

                    <div class="form-group">

                        <label class="form-label">
                            Subscription <span class="required">*</span>
                        </label>

                        <select
                            id="subscriptionIdInput"
                            class="form-select"
                            required
                            disabled
                        >
                            <option value="">
                                Select Subscription
                            </option>
                        </select>

                        <div
                            id="subscriptionIdError"
                            class="field-error"
                        ></div>

                    </div>


                    {{-- Subscription Period --}}

                    <div class="form-group">

                        <label class="form-label">
                            Subscription Period <span class="required">*</span>
                        </label>

                        <select
                            id="subscriptionPeriodIdInput"
                            class="form-select"
                            required
                            disabled
                        >
                            <option value="">
                                Select Period
                            </option>
                        </select>

                        <div
                            id="subscriptionPeriodIdError"
                            class="field-error"
                        ></div>

                    </div>


                    {{-- Period Information --}}

                    <div
                        id="periodDetails"
                        class="form-group full-width"
                        style="display: none;"
                    >

                        <div class="details-card">

                            <div class="details-grid">

                                <div class="detail-item">
                                    <div class="detail-label">
                                        Plan
                                    </div>

                                    <div
                                        id="detailPlan"
                                        class="detail-value"
                                    >
                                        -
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">
                                        Base Price
                                    </div>

                                    <div
                                        id="detailBasePrice"
                                        class="detail-value"
                                    >
                                        -
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">
                                        Included Units
                                    </div>

                                    <div
                                        id="detailIncludedUnits"
                                        class="detail-value"
                                    >
                                        -
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-label">
                                        Overage Rate
                                    </div>

                                    <div
                                        id="detailOverageRate"
                                        class="detail-value"
                                    >
                                        -
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Idempotency Key --}}

                    <div class="form-group full-width">

                        <label class="form-label">
                            Idempotency Key <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="idempotencyKeyInput"
                            class="form-input"
                            maxlength="255"
                            required
                            placeholder="Unique key for this usage event"
                        >

                        <div class="help-text">
                            This key prevents duplicate usage events for the same merchant.
                            It cannot be changed after creation.
                        </div>

                        <div
                            id="idempotencyKeyError"
                            class="field-error"
                        ></div>

                    </div>


                    {{-- Usage Units --}}

                    <div class="form-group">

                        <label class="form-label">
                            Usage Units <span class="required">*</span>
                        </label>

                        <input
                            type="number"
                            id="usageUnitsInput"
                            class="form-input"
                            min="0"
                            step="1"
                            required
                            placeholder="0"
                        >

                        <div
                            id="usageUnitsError"
                            class="field-error"
                        ></div>

                    </div>


                    {{-- Unit Name --}}

                    <div class="form-group">

                        <label class="form-label">
                            Unit Name
                        </label>

                        <input
                            type="text"
                            id="unitNameInput"
                            class="form-input"
                            maxlength="100"
                            placeholder="units"
                        >

                        <div class="help-text">
                            Defaults to the subscription usage unit.
                        </div>

                        <div
                            id="unitNameError"
                            class="field-error"
                        ></div>

                    </div>


                    {{-- Occurred At --}}

                    <div class="form-group">

                        <label class="form-label">
                            Occurred At <span class="required">*</span>
                        </label>

                        <input
                            type="datetime-local"
                            id="occurredAtInput"
                            class="form-input"
                            required
                        >

                        <div
                            id="occurredAtError"
                            class="field-error"
                        ></div>

                    </div>


                    {{-- Metadata --}}

                    <div class="form-group full-width">

                        <label class="form-label">
                            Metadata
                        </label>

                        <textarea
                            id="metadataInput"
                            class="form-textarea"
                            placeholder='{"source":"api","reference":"order-1001"}'
                        ></textarea>

                        <div class="help-text">
                            Optional valid JSON object.
                        </div>

                        <div
                            id="metadataError"
                            class="field-error"
                        ></div>

                    </div>

                </div>

            </form>

        </div>

        <div class="modal-footer">

            <button
                type="button"
                class="btn btn-secondary"
                onclick="closeModal()"
            >
                Cancel
            </button>

            <button
                type="button"
                id="saveButton"
                class="btn btn-primary"
                onclick="saveUsageEvent()"
            >
                Create Usage Event
            </button>

        </div>

    </div>
</div>


<script>

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    const tableBody = document.getElementById(
        'usageEventsTableBody'
    );

    const searchInput = document.getElementById(
        'searchInput'
    );

    const perPageInput = document.getElementById(
        'perPageInput'
    );

    const pagination = document.getElementById(
        'pagination'
    );

    const paginationInfo = document.getElementById(
        'paginationInfo'
    );

    const modal = document.getElementById(
        'usageEventModal'
    );

    const modalTitle = document.getElementById(
        'modalTitle'
    );

    const saveButton = document.getElementById(
        'saveButton'
    );

    const usageEventForm = document.getElementById(
        'usageEventForm'
    );

    const usageEventIdInput = document.getElementById(
        'usageEventId'
    );

    const merchantIdInput = document.getElementById(
        'merchantIdInput'
    );

    const customerIdInput = document.getElementById(
        'customerIdInput'
    );

    const subscriptionIdInput = document.getElementById(
        'subscriptionIdInput'
    );

    const subscriptionPeriodIdInput = document.getElementById(
        'subscriptionPeriodIdInput'
    );

    const idempotencyKeyInput = document.getElementById(
        'idempotencyKeyInput'
    );

    const usageUnitsInput = document.getElementById(
        'usageUnitsInput'
    );

    const unitNameInput = document.getElementById(
        'unitNameInput'
    );

    const occurredAtInput = document.getElementById(
        'occurredAtInput'
    );

    const metadataInput = document.getElementById(
        'metadataInput'
    );

    const periodDetails = document.getElementById(
        'periodDetails'
    );

    let currentPage = 1;

    let merchants = [];
    let customers = [];
    let subscriptions = [];
    let subscriptionPeriods = [];

    let isEditMode = false;


    /*
     * -------------------------------------------------------
     * Common Request Helper
     * -------------------------------------------------------
     */

    async function request(
        url,
        options = {}
    ) {
        const headers = {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {})
        };

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        if (
            options.body &&
            typeof options.body !== 'string'
        ) {
            headers['Content-Type'] =
                'application/json';

            options.body = JSON.stringify(
                options.body
            );
        }

        const response = await fetch(url, {
            ...options,
            credentials: 'same-origin',
            headers
        });

        let result = null;

        try {
            result = await response.json();
        } catch (error) {
            result = null;
        }

        if (!response.ok || result?.success === false) {

            const error = new Error(
                result?.message ||
                'Something went wrong.'
            );

            error.status = response.status;
            error.data = result;

            throw error;
        }

        return result;
    }


    /*
     * -------------------------------------------------------
     * Alerts
     * -------------------------------------------------------
     */

    function showAlert(
        message,
        type = 'success'
    ) {
        const alertBox =
            document.getElementById('alertBox');

        alertBox.className =
            'alert-box ' +
            (
                type === 'success'
                    ? 'alert-success'
                    : 'alert-error'
            );

        alertBox.textContent = message;
        alertBox.style.display = 'block';

        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 4000);
    }


    function showModalAlert(
        message,
        type = 'error'
    ) {
        const alertBox =
            document.getElementById('modalAlert');

        alertBox.className =
            'alert-box ' +
            (
                type === 'success'
                    ? 'alert-success'
                    : 'alert-error'
            );

        alertBox.textContent = message;
        alertBox.style.display = 'block';
    }


    function hideModalAlert() {
        const alertBox =
            document.getElementById('modalAlert');

        alertBox.style.display = 'none';
        alertBox.textContent = '';
    }


    /*
     * -------------------------------------------------------
     * Validation Errors
     * -------------------------------------------------------
     */

    function clearValidationErrors() {

        document
            .querySelectorAll('.field-error')
            .forEach(element => {
                element.style.display = 'none';
                element.textContent = '';
            });
    }


    function showValidationErrors(
        errors
    ) {
        if (!errors) {
            return;
        }

        Object.entries(errors).forEach(
            ([field, messages]) => {

                const errorElement =
                    document.getElementById(
                        field + 'Error'
                    );

                if (!errorElement) {
                    return;
                }

                errorElement.textContent =
                    Array.isArray(messages)
                        ? messages[0]
                        : messages;

                errorElement.style.display =
                    'block';
            }
        );
    }


    /*
     * -------------------------------------------------------
     * Load Usage Events
     * -------------------------------------------------------
     */

    async function loadUsageEvents(
        page = 1
    ) {
        currentPage = page;

        tableBody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="empty-state">
                        Loading usage events...
                    </div>
                </td>
            </tr>
        `;

        try {

            const params =
                new URLSearchParams({
                    page: page,
                    per_page: perPageInput.value,
                    search: searchInput.value.trim()
                });

            const result =
                await request(
                    `/usage-events/data?${params.toString()}`
                );

            const data =
                result?.data || {};

            const items =
                data.items || [];

            renderTable(items);

            renderPagination(
                data.current_page || 1,
                data.last_page || 1,
                data.total || 0,
                data.per_page ||
                    Number(perPageInput.value)
            );

        } catch (error) {

            tableBody.innerHTML = `
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            Failed to load usage events.
                        </div>
                    </td>
                </tr>
            `;

            showAlert(
                error.message ||
                'Failed to load usage events.',
                'error'
            );
        }
    }


    /*
     * -------------------------------------------------------
     * Render Table
     * -------------------------------------------------------
     */

    function renderTable(
        items
    ) {

        if (!items.length) {

            tableBody.innerHTML = `
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            No usage events found.
                        </div>
                    </td>
                </tr>
            `;

            return;
        }

        tableBody.innerHTML =
            items.map(event => {

                const customerName =
                    escapeHtml(
                        event.customer?.name ||
                        'N/A'
                    );

                const customerCode =
                    escapeHtml(
                        event.customer?.code ||
                        ''
                    );

                const subscriptionId =
                    event.subscription_id ??
                    'N/A';

                const periodId =
                    event.subscription_period_id ??
                    'N/A';

                const usageUnits =
                    Number(
                        event.usage_units || 0
                    ).toLocaleString();

                const unitName =
                    escapeHtml(
                        event.unit_name ||
                        'units'
                    );

                const occurredAt =
                    formatDateTime(
                        event.occurred_at
                    );

                const idempotencyKey =
                    escapeHtml(
                        event.idempotency_key ||
                        ''
                    );

                const metadata =
                    event.metadata
                        ? escapeHtml(
                            JSON.stringify(
                                event.metadata
                            )
                        )
                        : '-';

                return `
                    <tr>

                        <td>
                            #${event.id}
                        </td>

                        <td>
                            <div class="customer-name">
                                ${customerName}
                            </div>

                            ${
                                customerCode
                                    ? `
                                        <div class="sub-text">
                                            ${customerCode}
                                        </div>
                                    `
                                    : ''
                            }
                        </td>

                        <td>
                            <span class="period-badge">
                                #${subscriptionId}
                            </span>
                        </td>

                        <td>
                            <span class="period-badge">
                                #${periodId}
                            </span>
                        </td>

                        <td>
                            <div class="usage-value">
                                ${usageUnits}
                            </div>

                            <div style="margin-top:4px;">
                                <span class="unit-badge">
                                    ${unitName}
                                </span>
                            </div>
                        </td>

                        <td>
                            ${occurredAt}
                        </td>

                        <td>
                            <div
                                class="idempotency-key"
                                title="${idempotencyKey}"
                            >
                                ${idempotencyKey}
                            </div>
                        </td>

                        <td>
                            <div
                                class="idempotency-key"
                                title="${metadata}"
                            >
                                ${metadata}
                            </div>
                        </td>

                        <td>
                            <div class="actions">

                                <button
                                    type="button"
                                    class="btn btn-warning btn-small"
                                    onclick="editUsageEvent(${event.id})"
                                >
                                    Edit
                                </button>

                            </div>
                        </td>

                    </tr>
                `;

            }).join('');
    }


    /*
     * -------------------------------------------------------
     * Pagination
     * -------------------------------------------------------
     */

    function renderPagination(
        page,
        lastPage,
        total,
        perPage
    ) {

        if (total === 0) {

            paginationInfo.textContent =
                'Showing 0 of 0';

            pagination.innerHTML = '';

            return;
        }

        const start =
            ((page - 1) * perPage) + 1;

        const end =
            Math.min(
                page * perPage,
                total
            );

        paginationInfo.textContent =
            `Showing ${start}-${end} of ${total}`;

        let html = '';

        html += `
            <button
                class="page-btn"
                ${page <= 1 ? 'disabled' : ''}
                onclick="goToPage(${page - 1})"
            >
                ‹
            </button>
        `;

        const maxVisiblePages = 5;

        let startPage =
            Math.max(
                1,
                page -
                    Math.floor(
                        maxVisiblePages / 2
                    )
            );

        let endPage =
            Math.min(
                lastPage,
                startPage +
                    maxVisiblePages -
                    1
            );

        if (
            endPage - startPage + 1 <
            maxVisiblePages
        ) {
            startPage =
                Math.max(
                    1,
                    endPage -
                        maxVisiblePages +
                        1
                );
        }

        for (
            let i = startPage;
            i <= endPage;
            i++
        ) {

            html += `
                <button
                    class="page-btn ${
                        i === page
                            ? 'active'
                            : ''
                    }"
                    onclick="goToPage(${i})"
                >
                    ${i}
                </button>
            `;
        }

        html += `
            <button
                class="page-btn"
                ${page >= lastPage ? 'disabled' : ''}
                onclick="goToPage(${page + 1})"
            >
                ›
            </button>
        `;

        pagination.innerHTML = html;
    }


    function goToPage(
        page
    ) {
        loadUsageEvents(page);
    }


    /*
     * -------------------------------------------------------
     * Load Merchants
     * -------------------------------------------------------
     */

    async function loadMerchants() {

        try {

            const result =
                await request(
                    '/merchants/data?per_page=100'
                );

            merchants =
                result?.data?.items ||
                result?.data?.data ||
                [];

            merchantIdInput.innerHTML = `
                <option value="">
                    Select Merchant
                </option>
            `;

            merchants.forEach(
                merchant => {

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

        } catch (error) {

            showAlert(
                'Failed to load merchants.',
                'error'
            );
        }
    }


    /*
     * -------------------------------------------------------
     * Merchant Changed
     * -------------------------------------------------------
     */

    merchantIdInput.addEventListener(
        'change',
        async function () {

            const merchantId =
                this.value;

            resetCustomerDropdown();
            resetSubscriptionDropdown();
            resetPeriodDropdown();

            if (!merchantId) {
                return;
            }

            await loadCustomers(
                merchantId
            );
        }
    );


    /*
     * -------------------------------------------------------
     * Load Customers
     *
     * Important:
     * Customer API is protected by Sanctum.
     * We therefore use a web route.
     * -------------------------------------------------------
     */

    async function loadCustomers(
        merchantId,
        selectedCustomerId = null
    ) {

        customerIdInput.disabled = true;

        customerIdInput.innerHTML = `
            <option value="">
                Loading customers...
            </option>
        `;

        try {

            /*
             * This route will be added as part of
             * the Customer Web UI if it does not
             * already exist.
             */
            const result =
                await request(
                    `/customers/data?merchant_id=${encodeURIComponent(merchantId)}&per_page=100`
                );

            customers =
                result?.data?.items ||
                result?.data?.data ||
                [];

            customerIdInput.innerHTML = `
                <option value="">
                    Select Customer
                </option>
            `;

            customers.forEach(
                customer => {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        customer.id;

                    option.textContent =
                        `${customer.name} (${customer.code})`;

                    customerIdInput.appendChild(
                        option
                    );
                }
            );

            customerIdInput.disabled =
                false;

            if (
                selectedCustomerId !== null
            ) {
                customerIdInput.value =
                    String(
                        selectedCustomerId
                    );
            }

        } catch (error) {

            resetCustomerDropdown();

            showModalAlert(
                error.message ||
                'Failed to load customers.'
            );
        }
    }


    /*
     * -------------------------------------------------------
     * Customer Changed
     * -------------------------------------------------------
     */

    customerIdInput.addEventListener(
        'change',
        async function () {

            const customerId =
                this.value;

            resetSubscriptionDropdown();
            resetPeriodDropdown();

            if (!customerId) {
                return;
            }

            const merchantId =
                merchantIdInput.value;

            await loadSubscriptions(
                merchantId,
                customerId
            );
        }
    );


    /*
     * -------------------------------------------------------
     * Load Subscriptions
     * -------------------------------------------------------
     */

    async function loadSubscriptions(
        merchantId,
        customerId,
        selectedSubscriptionId = null
    ) {

        subscriptionIdInput.disabled = true;

        subscriptionIdInput.innerHTML = `
            <option value="">
                Loading subscriptions...
            </option>
        `;

        try {

            const params =
                new URLSearchParams({
                    per_page: 100,
                    merchant_id: merchantId,
                    customer_id: customerId
                });

            const result =
                await request(
                    `/subscriptions/data?${params.toString()}`
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
                subscription => {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        subscription.id;

                    const customerName =
                        subscription.customer?.name ||
                        'Customer';

                    const planName =
                        subscription.plan?.name ||
                        `Plan #${subscription.plan_id}`;

                    option.textContent =
                        `#${subscription.id} - ${customerName} - ${planName}`;

                    subscriptionIdInput.appendChild(
                        option
                    );
                }
            );

            subscriptionIdInput.disabled =
                false;

            if (
                selectedSubscriptionId !== null
            ) {
                subscriptionIdInput.value =
                    String(
                        selectedSubscriptionId
                    );

                await loadSubscriptionPeriods(
                    selectedSubscriptionId
                );
            }

        } catch (error) {

            resetSubscriptionDropdown();

            showModalAlert(
                error.message ||
                'Failed to load subscriptions.'
            );
        }
    }


    /*
     * -------------------------------------------------------
     * Subscription Changed
     * -------------------------------------------------------
     */

    subscriptionIdInput.addEventListener(
        'change',
        async function () {

            const subscriptionId =
                this.value;

            resetPeriodDropdown();

            if (!subscriptionId) {
                return;
            }

            await loadSubscriptionPeriods(
                subscriptionId
            );
        }
    );


    /*
     * -------------------------------------------------------
     * Load Subscription Periods
     * -------------------------------------------------------
     */

    async function loadSubscriptionPeriods(
        subscriptionId,
        selectedPeriodId = null
    ) {

        subscriptionPeriodIdInput.disabled =
            true;

        subscriptionPeriodIdInput.innerHTML = `
            <option value="">
                Loading periods...
            </option>
        `;

        try {

            const params =
                new URLSearchParams({
                    per_page: 100,
                    subscription_id:
                        subscriptionId
                });

            const result =
                await request(
                    `/subscription-periods/data?${params.toString()}`
                );

            subscriptionPeriods =
                result?.data?.items ||
                result?.data?.data ||
                [];

            subscriptionPeriodIdInput.innerHTML = `
                <option value="">
                    Select Period
                </option>
            `;

            subscriptionPeriods.forEach(
                period => {

                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        period.id;

                    const start =
                        formatDateTime(
                            period.starts_at
                        );

                    const end =
                        period.ends_at
                            ? formatDateTime(
                                period.ends_at
                            )
                            : 'Open';

                    option.textContent =
                        `#${period.id} - ${start} → ${end}`;

                    subscriptionPeriodIdInput.appendChild(
                        option
                    );
                }
            );

            subscriptionPeriodIdInput.disabled =
                false;

            if (
                selectedPeriodId !== null
            ) {

                subscriptionPeriodIdInput.value =
                    String(
                        selectedPeriodId
                    );

                updatePeriodDetails(
                    selectedPeriodId,
                    false
                );
            }

        } catch (error) {

            resetPeriodDropdown();

            showModalAlert(
                error.message ||
                'Failed to load subscription periods.'
            );
        }
    }


    /*
     * -------------------------------------------------------
     * Period Changed
     * -------------------------------------------------------
     */

    subscriptionPeriodIdInput.addEventListener(
        'change',
        function () {

            updatePeriodDetails(
                this.value,
                true
            );
        }
    );


    /*
     * -------------------------------------------------------
     * Period Details
     * -------------------------------------------------------
     */

    function updatePeriodDetails(
        periodId,
        syncOccurredAt = false
    ) {

        if (!periodId) {

            periodDetails.style.display =
                'none';

            return;
        }

        const period =
            subscriptionPeriods.find(
                item =>
                    String(item.id) ===
                    String(periodId)
            );

        if (!period) {

            periodDetails.style.display =
                'none';

            return;
        }

        const planName =
            period.plan?.name ||
            `Plan #${period.plan_id}`;

        document.getElementById(
            'detailPlan'
        ).textContent =
            planName;

        document.getElementById(
            'detailBasePrice'
        ).textContent =
            period.base_price ??
            '-';

        document.getElementById(
            'detailIncludedUnits'
        ).textContent =
            Number(
                period.included_units || 0
            ).toLocaleString();

        document.getElementById(
            'detailOverageRate'
        ).textContent =
            period.overage_rate ??
            '0';

        periodDetails.style.display =
            'block';

        /*
         * Keep Occurred At inside the selected period.
         * This is especially important when the user
         * changes from one subscription period to another.
         */
        const periodStart =
            toDateTimeLocal(
                period.starts_at
            );

        const periodEnd =
            period.ends_at
                ? toDateTimeLocal(period.ends_at)
                : '';

        occurredAtInput.min =
            periodStart || '';

        occurredAtInput.max =
            periodEnd || '';

        if (syncOccurredAt && periodStart) {

            const currentOccurredAt =
                occurredAtInput.value;

            const isBeforeStart =
                !currentOccurredAt ||
                currentOccurredAt < periodStart;

            const isAfterEnd =
                periodEnd &&
                currentOccurredAt > periodEnd;

            if (isBeforeStart || isAfterEnd) {
                occurredAtInput.value =
                    periodStart;
            }
        }

        /*
         * Use the plan/period unit if available.
         */
        const selectedSubscription =
            subscriptions.find(
                item =>
                    String(item.id) ===
                    String(
                        subscriptionIdInput.value
                    )
            );

        if (
            !unitNameInput.value &&
            selectedSubscription?.plan?.unit_name
        ) {
            unitNameInput.value =
                selectedSubscription.plan.unit_name;
        }
    }


    /*
     * -------------------------------------------------------
     * Reset Dropdowns
     * -------------------------------------------------------
     */

    function resetCustomerDropdown() {

        customerIdInput.innerHTML = `
            <option value="">
                Select Customer
            </option>
        `;

        customerIdInput.disabled = true;

        customers = [];
    }


    function resetSubscriptionDropdown() {

        subscriptionIdInput.innerHTML = `
            <option value="">
                Select Subscription
            </option>
        `;

        subscriptionIdInput.disabled = true;

        subscriptions = [];
    }


    function resetPeriodDropdown() {

        subscriptionPeriodIdInput.innerHTML = `
            <option value="">
                Select Period
            </option>
        `;

        subscriptionPeriodIdInput.disabled = true;

        subscriptionPeriods = [];

        periodDetails.style.display =
            'none';

        occurredAtInput.min = '';
        occurredAtInput.max = '';
    }


    /*
     * -------------------------------------------------------
     * Open Create Modal
     * -------------------------------------------------------
     */

    async function openCreateModal() {

        isEditMode = false;

        usageEventForm.reset();

        clearValidationErrors();
        hideModalAlert();

        usageEventIdInput.value = '';

        modalTitle.textContent =
            'Add Usage Event';

        saveButton.textContent =
            'Create Usage Event';

        merchantIdInput.disabled =
            false;

        customerIdInput.disabled =
            true;

        subscriptionIdInput.disabled =
            true;

        subscriptionPeriodIdInput.disabled =
            true;

        idempotencyKeyInput.disabled =
            false;

        usageUnitsInput.disabled =
            false;

        unitNameInput.disabled =
            false;

        occurredAtInput.disabled =
            false;

        metadataInput.disabled =
            false;

        resetCustomerDropdown();
        resetSubscriptionDropdown();
        resetPeriodDropdown();

        /*
         * Generate a client-side idempotency key.
         */
        idempotencyKeyInput.value =
            generateIdempotencyKey();

        /*
         * Occurred At will be set automatically when a
         * subscription period is selected.
         */
        occurredAtInput.value = '';
        occurredAtInput.min = '';
        occurredAtInput.max = '';

        modal.classList.add('show');

        await loadMerchants();
    }


    /*
     * -------------------------------------------------------
     * Open Edit Modal
     * -------------------------------------------------------
     */

    async function editUsageEvent(
        id
    ) {

        isEditMode = true;

        usageEventForm.reset();

        clearValidationErrors();
        hideModalAlert();

        modalTitle.textContent =
            'Edit Usage Event';

        saveButton.textContent =
            'Update Usage Event';

        modal.classList.add('show');

        try {

            const result =
                await request(
                    `/usage-events/${id}`
                );

            const event =
                result?.data;

            if (!event) {
                throw new Error(
                    'Usage event not found.'
                );
            }

            usageEventIdInput.value =
                event.id;

            /*
             * Load merchant first.
             */
            await loadMerchants();

            merchantIdInput.value =
                String(
                    event.merchant_id
                );

            merchantIdInput.disabled =
                true;

            /*
             * Customer
             */
            await loadCustomers(
                event.merchant_id,
                event.customer_id
            );

            customerIdInput.disabled =
                true;

            /*
             * Subscription
             */
            await loadSubscriptions(
                event.merchant_id,
                event.customer_id,
                event.subscription_id
            );

            subscriptionIdInput.disabled =
                true;

            /*
             * Period
             */
            await loadSubscriptionPeriods(
                event.subscription_id,
                event.subscription_period_id
            );

            subscriptionPeriodIdInput.disabled =
                true;

            /*
             * Immutable identity field.
             */
            idempotencyKeyInput.value =
                event.idempotency_key || '';

            idempotencyKeyInput.disabled =
                true;

            /*
             * Editable fields.
             */
            usageUnitsInput.value =
                event.usage_units ?? '';

            unitNameInput.value =
                event.unit_name || '';

            occurredAtInput.value =
                toDateTimeLocal(
                    event.occurred_at
                );

            metadataInput.value =
                event.metadata
                    ? JSON.stringify(
                        event.metadata,
                        null,
                        2
                    )
                    : '';

            usageUnitsInput.disabled =
                false;

            unitNameInput.disabled =
                false;

            occurredAtInput.disabled =
                false;

            metadataInput.disabled =
                false;

        } catch (error) {

            closeModal();

            showAlert(
                error.message ||
                'Failed to load usage event.',
                'error'
            );
        }
    }


    /*
     * -------------------------------------------------------
     * Save Usage Event
     * -------------------------------------------------------
     */

    async function saveUsageEvent() {

        clearValidationErrors();
        hideModalAlert();

        const isEdit =
            isEditMode;

        const merchantId =
            merchantIdInput.value;

        const customerId =
            customerIdInput.value;

        const subscriptionId =
            subscriptionIdInput.value;

        const periodId =
            subscriptionPeriodIdInput.value;

        const idempotencyKey =
            idempotencyKeyInput.value.trim();

        const usageUnits =
            usageUnitsInput.value;

        const unitName =
            unitNameInput.value.trim();

        const occurredAt =
            occurredAtInput.value;

        const metadataText =
            metadataInput.value.trim();

        /*
         * Basic client validation.
         */
        if (!isEdit) {

            if (!merchantId) {
                showModalAlert(
                    'Please select a merchant.'
                );

                return;
            }

            if (!customerId) {
                showModalAlert(
                    'Please select a customer.'
                );

                return;
            }

            if (!subscriptionId) {
                showModalAlert(
                    'Please select a subscription.'
                );

                return;
            }

            if (!periodId) {
                showModalAlert(
                    'Please select a subscription period.'
                );

                return;
            }

            if (!idempotencyKey) {
                showModalAlert(
                    'Idempotency key is required.'
                );

                return;
            }
        }

        if (
            usageUnits === '' ||
            Number(usageUnits) < 0
        ) {

            showModalAlert(
                'Usage units must be 0 or greater.'
            );

            return;
        }

        if (!occurredAt) {

            showModalAlert(
                'Occurred At is required.'
            );

            return;
        }

        /*
         * Validate Occurred At against the selected period
         * before sending the request to Laravel.
         */
        if (!isEdit && periodId) {

            const selectedPeriod =
                subscriptionPeriods.find(
                    period =>
                        String(period.id) ===
                        String(periodId)
                );

            if (selectedPeriod) {

                const periodStart =
                    toDateTimeLocal(
                        selectedPeriod.starts_at
                    );

                const periodEnd =
                    selectedPeriod.ends_at
                        ? toDateTimeLocal(
                            selectedPeriod.ends_at
                        )
                        : '';

                if (
                    periodStart &&
                    occurredAt < periodStart
                ) {
                    occurredAtInput.value =
                        periodStart;

                    showModalAlert(
                        'Occurred At was outside the selected period and has been adjusted to the period start.'
                    );

                    return;
                }

                if (
                    periodEnd &&
                    occurredAt > periodEnd
                ) {
                    occurredAtInput.value =
                        periodStart || occurredAt;

                    showModalAlert(
                        'Occurred At was outside the selected period and has been adjusted to the period start.'
                    );

                    return;
                }
            }
        }

        /*
         * Parse metadata.
         */
        let metadata = null;

        if (metadataText !== '') {

            try {

                metadata =
                    JSON.parse(
                        metadataText
                    );

                if (
                    metadata === null ||
                    Array.isArray(metadata) ||
                    typeof metadata !== 'object'
                ) {
                    throw new Error(
                        'Metadata must be a JSON object.'
                    );
                }

            } catch (error) {

                showModalAlert(
                    'Metadata must contain valid JSON object data.'
                );

                return;
            }
        }

        const payload = isEdit
            ? {
                usage_units:
                    Number(usageUnits),

                unit_name:
                    unitName || null,

                occurred_at:
                    occurredAt,

                metadata:
                    metadata
            }
            : {
                merchant_id:
                    Number(merchantId),

                customer_id:
                    Number(customerId),

                subscription_id:
                    Number(subscriptionId),

                subscription_period_id:
                    Number(periodId),

                idempotency_key:
                    idempotencyKey,

                usage_units:
                    Number(usageUnits),

                unit_name:
                    unitName || null,

                occurred_at:
                    occurredAt,

                metadata:
                    metadata
            };

        const url = isEdit
            ? `/usage-events/${usageEventIdInput.value}`
            : '/usage-events';

        const method =
            isEdit
                ? 'PUT'
                : 'POST';

        saveButton.disabled = true;

        saveButton.textContent =
            isEdit
                ? 'Updating...'
                : 'Creating...';

        try {

            const result =
                await request(
                    url,
                    {
                        method,
                        body: payload
                    }
                );

            closeModal();

            showAlert(
                result?.message ||
                (
                    isEdit
                        ? 'Usage event updated successfully.'
                        : 'Usage event created successfully.'
                ),
                'success'
            );

            await loadUsageEvents(
                currentPage
            );

        } catch (error) {

            const validationErrors =
                error?.data?.errors;

            if (validationErrors) {

                showValidationErrors(
                    validationErrors
                );

                showModalAlert(
                    'Please correct the validation errors.'
                );

            } else {

                showModalAlert(
                    error.message ||
                    'Failed to save usage event.'
                );
            }

        } finally {

            saveButton.disabled =
                false;

            saveButton.textContent =
                isEdit
                    ? 'Update Usage Event'
                    : 'Create Usage Event';
        }
    }


    /*
     * -------------------------------------------------------
     * Close Modal
     * -------------------------------------------------------
     */

    function closeModal() {

        modal.classList.remove('show');

        clearValidationErrors();
        hideModalAlert();

        usageEventForm.reset();

        usageEventIdInput.value = '';

        merchantIdInput.disabled =
            false;

        idempotencyKeyInput.disabled =
            false;

        resetCustomerDropdown();
        resetSubscriptionDropdown();
        resetPeriodDropdown();
    }


    /*
     * -------------------------------------------------------
     * Search
     * -------------------------------------------------------
     */

    let searchTimer = null;

    searchInput.addEventListener(
        'input',
        function () {

            clearTimeout(
                searchTimer
            );

            searchTimer =
                setTimeout(
                    () => {
                        loadUsageEvents(1);
                    },
                    350
                );
        }
    );


    /*
     * -------------------------------------------------------
     * Per Page
     * -------------------------------------------------------
     */

    perPageInput.addEventListener(
        'change',
        function () {

            loadUsageEvents(1);
        }
    );


    /*
     * -------------------------------------------------------
     * Close modal on overlay click
     * -------------------------------------------------------
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
     * -------------------------------------------------------
     * Escape Key
     * -------------------------------------------------------
     */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                modal.classList.contains('show')
            ) {
                closeModal();
            }
        }
    );


    /*
     * -------------------------------------------------------
     * Generate Idempotency Key
     * -------------------------------------------------------
     */

    function generateIdempotencyKey() {

        if (
            window.crypto &&
            typeof window.crypto.randomUUID ===
                'function'
        ) {
            return window.crypto.randomUUID();
        }

        return (
            'usage-' +
            Date.now() +
            '-' +
            Math.random()
                .toString(36)
                .substring(2, 12)
        );
    }


    /*
     * -------------------------------------------------------
     * Current Date Time Local
     * -------------------------------------------------------
     */

    function getCurrentDateTimeLocal() {

        const now =
            new Date();

        const year =
            now.getFullYear();

        const month =
            String(
                now.getMonth() + 1
            ).padStart(2, '0');

        const day =
            String(
                now.getDate()
            ).padStart(2, '0');

        const hours =
            String(
                now.getHours()
            ).padStart(2, '0');

        const minutes =
            String(
                now.getMinutes()
            ).padStart(2, '0');

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }


    /*
     * -------------------------------------------------------
     * Date Formatting
     * -------------------------------------------------------
     */

    function formatDateTime(
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
            ).padStart(2, '0');

        const day =
            String(
                date.getDate()
            ).padStart(2, '0');

        const hours =
            String(
                date.getHours()
            ).padStart(2, '0');

        const minutes =
            String(
                date.getMinutes()
            ).padStart(2, '0');

        return (
            `${year}-${month}-${day}` +
            `T${hours}:${minutes}`
        );
    }


    /*
     * -------------------------------------------------------
     * HTML Escape
     * -------------------------------------------------------
     */

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


    /*
     * -------------------------------------------------------
     * Initial Load
     * -------------------------------------------------------
     */

    loadUsageEvents(1);

</script>

@endsection