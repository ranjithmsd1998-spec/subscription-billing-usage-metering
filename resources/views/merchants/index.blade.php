@extends('layouts.app')

@section('title', 'Merchants | Subscription Billing')

@section('page_heading', 'Merchants')

@section('content')

<style>
    .merchant-page {
        width: 100%;
    }

    .merchant-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
    }

    .merchant-toolbar-left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
    }

    .search-input {
        width: 320px;
        max-width: 100%;
        padding: 10px 13px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        outline: none;
        background: #ffffff;
        color: #111827;
        font-size: 13px;
    }

    .search-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
    }

    .page-size-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6b7280;
        font-size: 13px;
        white-space: nowrap;
    }

    .page-size-select {
        padding: 9px 30px 9px 10px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: #ffffff;
        color: #374151;
        outline: none;
    }

    .primary-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 15px;
        border: none;
        border-radius: 7px;
        background: #2563eb;
        color: #ffffff;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
    }

    .primary-button:hover {
        background: #1d4ed8;
    }

    .secondary-button {
        padding: 9px 14px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background: #ffffff;
        color: #374151;
        cursor: pointer;
        font-size: 13px;
    }

    .secondary-button:hover {
        background: #f9fafb;
    }

    .danger-button {
        padding: 7px 11px;
        border: 1px solid #fecaca;
        border-radius: 6px;
        background: #ffffff;
        color: #dc2626;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .danger-button:hover {
        background: #fef2f2;
    }

    .edit-button {
        padding: 7px 11px;
        border: 1px solid #bfdbfe;
        border-radius: 6px;
        background: #ffffff;
        color: #2563eb;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
    }

    .edit-button:hover {
        background: #eff6ff;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .loading-row {
        padding: 40px;
        text-align: center;
        color: #6b7280;
        font-size: 13px;
    }

    .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 15px 20px;
        border-top: 1px solid #eef0f4;
    }

    .pagination-info {
        color: #6b7280;
        font-size: 12px;
    }

    .pagination-buttons {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .pagination-button {
        min-width: 34px;
        height: 34px;
        padding: 0 9px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #ffffff;
        color: #374151;
        cursor: pointer;
        font-size: 12px;
    }

    .pagination-button:hover:not(:disabled) {
        background: #f3f4f6;
    }

    .pagination-button.active {
        border-color: #2563eb;
        background: #2563eb;
        color: #ffffff;
    }

    .pagination-button:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }

    /* Modal */

    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.55);
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal {
        width: 100%;
        max-width: 620px;
        max-height: 90vh;
        overflow-y: auto;
        border-radius: 10px;
        background: #ffffff;
        box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
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
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        background: transparent;
        color: #6b7280;
        cursor: pointer;
        font-size: 20px;
    }

    .modal-close:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .modal-body {
        padding: 20px;
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

    .form-group.full {
        grid-column: 1 / -1;
    }

    .form-label {
        color: #374151;
        font-size: 12px;
        font-weight: 600;
    }

    .required {
        color: #dc2626;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 10px 11px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        outline: none;
        background: #ffffff;
        color: #111827;
        font-size: 13px;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.08);
    }

    .form-textarea {
        min-height: 90px;
        resize: vertical;
    }

    .field-error {
        display: none;
        color: #dc2626;
        font-size: 11px;
    }

    .field-error.show {
        display: block;
    }

    .input-error {
        border-color: #ef4444 !important;
    }

    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        padding: 15px 20px;
        border-top: 1px solid #e5e7eb;
    }

    .submit-button {
        min-width: 110px;
    }

    .submit-button:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    /* Toast */

    .toast-container {
        position: fixed;
        top: 85px;
        right: 25px;
        z-index: 2000;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        min-width: 280px;
        max-width: 380px;
        padding: 13px 16px;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        font-size: 13px;
        animation: toast-in 0.2s ease-out;
    }

    .toast.success {
        border: 1px solid #a7f3d0;
        background: #ecfdf5;
        color: #065f46;
    }

    .toast.error {
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    @keyframes toast-in {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 700px) {
        .merchant-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .merchant-toolbar-left {
            flex-direction: column;
            align-items: stretch;
        }

        .search-input {
            width: 100%;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full {
            grid-column: auto;
        }

        .pagination-wrapper {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>


<div class="merchant-page">

    {{-- Page Header --}}
    <div class="page-header">

        <div>
            <h1 class="page-title">
                Merchants
            </h1>

            <p class="page-description">
                Manage merchants and tenant accounts.
            </p>
        </div>

    </div>


    {{-- Toolbar --}}
    <div class="merchant-toolbar">

        <div class="merchant-toolbar-left">

            <input
                type="search"
                id="merchantSearch"
                class="search-input"
                placeholder="Search by name, code or email..."
                autocomplete="off"
            >

            <div class="page-size-wrapper">

                <span>
                    Show
                </span>

                <select
                    id="pageSize"
                    class="page-size-select"
                >
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>

                <span>
                    per page
                </span>

            </div>

        </div>


        <button
            type="button"
            class="primary-button"
            id="addMerchantButton"
        >
            + Add Merchant
        </button>

    </div>


    {{-- Merchant Table --}}
    <div class="card">

        <div class="table-wrapper">

            <table class="data-table">

                <thead>

                    <tr>

                        <th>
                            Name
                        </th>

                        <th>
                            Code
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Currency
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody id="merchantTableBody">

                    <tr>
                        <td
                            colspan="7"
                            class="loading-row"
                        >
                            Loading merchants...
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        <div
            class="pagination-wrapper"
            id="paginationWrapper"
        >

            <div
                class="pagination-info"
                id="paginationInfo"
            >
                Loading...
            </div>

            <div
                class="pagination-buttons"
                id="paginationButtons"
            ></div>

        </div>

    </div>

</div>


{{-- Merchant Modal --}}
<div
    class="modal-overlay"
    id="merchantModal"
>

    <div class="modal">

        <div class="modal-header">

            <h2
                class="modal-title"
                id="modalTitle"
            >
                Add Merchant
            </h2>

            <button
                type="button"
                class="modal-close"
                id="closeModalButton"
            >
                ×
            </button>

        </div>


        <form
            id="merchantForm"
            novalidate
        >

            <div class="modal-body">

                <input
                    type="hidden"
                    id="merchantId"
                >


                <div class="form-grid">

                    {{-- Name --}}
                    <div class="form-group">

                        <label
                            for="name"
                            class="form-label"
                        >
                            Name
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input"
                            maxlength="255"
                        >

                        <div
                            class="field-error"
                            id="nameError"
                        ></div>

                    </div>


                    {{-- Code --}}
                    <div class="form-group">

                        <label
                            for="code"
                            class="form-label"
                        >
                            Code
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="code"
                            name="code"
                            class="form-input"
                            maxlength="255"
                        >

                        <div
                            class="field-error"
                            id="codeError"
                        ></div>

                    </div>


                    {{-- Email --}}
                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label"
                        >
                            Email
                            <span class="required">*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input"
                            maxlength="255"
                        >

                        <div
                            class="field-error"
                            id="emailError"
                        ></div>

                    </div>


                    {{-- Status --}}
                    <div class="form-group">

                        <label
                            for="status"
                            class="form-label"
                        >
                            Status
                            <span class="required">*</span>
                        </label>

                        <select
                            id="status"
                            name="status"
                            class="form-select"
                        >
                            <option value="active">
                                Active
                            </option>

                            <option value="inactive">
                                Inactive
                            </option>
                        </select>

                        <div
                            class="field-error"
                            id="statusError"
                        ></div>

                    </div>


                    {{-- Timezone --}}
                    <div class="form-group">

                        <label
                            for="timezone"
                            class="form-label"
                        >
                            Timezone
                        </label>

                        <input
                            type="text"
                            id="timezone"
                            name="timezone"
                            class="form-input"
                            value="UTC"
                            maxlength="100"
                        >

                        <div
                            class="field-error"
                            id="timezoneError"
                        ></div>

                    </div>


                    {{-- Currency --}}
                    <div class="form-group">

                        <label
                            for="currency"
                            class="form-label"
                        >
                            Currency
                        </label>

                        <input
                            type="text"
                            id="currency"
                            name="currency"
                            class="form-input"
                            value="USD"
                            maxlength="3"
                        >

                        <div
                            class="field-error"
                            id="currencyError"
                        ></div>

                    </div>


                    {{-- Phone --}}
                    <div class="form-group full">

                        <label
                            for="phone"
                            class="form-label"
                        >
                            Phone
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="form-input"
                            maxlength="30"
                        >

                        <div
                            class="field-error"
                            id="phoneError"
                        ></div>

                    </div>


                    {{-- Metadata --}}
                    <div class="form-group full">

                        <label
                            for="metadata"
                            class="form-label"
                        >
                            Metadata
                        </label>

                        <textarea
                            id="metadata"
                            name="metadata"
                            class="form-textarea"
                            placeholder='{"key":"value"}'
                        ></textarea>

                        <div
                            class="field-error"
                            id="metadataError"
                        ></div>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="secondary-button"
                    id="cancelModalButton"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="primary-button submit-button"
                    id="saveMerchantButton"
                >
                    Save Merchant
                </button>

            </div>

        </form>

    </div>

</div>


{{-- Toast --}}
<div
    class="toast-container"
    id="toastContainer"
></div>


<script>
(() => {
    'use strict';

    const state = {
        page: 1,
        perPage: 10,
        search: '',
        editingId: null,
    };


    const elements = {
        tableBody: document.getElementById('merchantTableBody'),
        search: document.getElementById('merchantSearch'),
        pageSize: document.getElementById('pageSize'),
        paginationInfo: document.getElementById('paginationInfo'),
        paginationButtons: document.getElementById('paginationButtons'),

        modal: document.getElementById('merchantModal'),
        modalTitle: document.getElementById('modalTitle'),
        form: document.getElementById('merchantForm'),
        merchantId: document.getElementById('merchantId'),

        addButton: document.getElementById('addMerchantButton'),
        closeButton: document.getElementById('closeModalButton'),
        cancelButton: document.getElementById('cancelModalButton'),
        saveButton: document.getElementById('saveMerchantButton'),

        name: document.getElementById('name'),
        code: document.getElementById('code'),
        email: document.getElementById('email'),
        status: document.getElementById('status'),
        timezone: document.getElementById('timezone'),
        currency: document.getElementById('currency'),
        phone: document.getElementById('phone'),
        metadata: document.getElementById('metadata'),
    };


    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }

        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }


    function getCsrfToken() {
        const token = document
            .querySelector('meta[name="csrf-token"]');

        return token ? token.getAttribute('content') : '';
    }


    async function request(
        url,
        options = {}
    ) {
        const headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(
                options.headers || {}
            ),
        };

        if (
            options.method &&
            options.method !== 'GET'
        ) {
            headers['Content-Type'] = 'application/json';
        }

        const csrfToken = getCsrfToken();

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

        const response = await fetch(
            url,
            {
                ...options,
                headers,
            }
        );

        let result = null;

        try {
            result = await response.json();
        } catch (error) {
            result = {
                success: false,
                message: 'Invalid server response.',
            };
        }

        if (!response.ok) {
            const error = new Error(
                result.message ||
                'Something went wrong.'
            );

            error.response = result;
            error.status = response.status;

            throw error;
        }

        return result;
    }


    function showToast(
        message,
        type = 'success'
    ) {
        const toast = document.createElement('div');

        toast.className = `toast ${type}`;

        toast.textContent = message;

        document
            .getElementById('toastContainer')
            .appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3500);
    }


    function clearErrors() {
        document
            .querySelectorAll('.field-error')
            .forEach((element) => {
                element.textContent = '';
                element.classList.remove('show');
            });

        document
            .querySelectorAll('.form-input, .form-select, .form-textarea')
            .forEach((element) => {
                element.classList.remove('input-error');
            });
    }


    function showValidationErrors(errors) {
        clearErrors();

        Object.entries(errors || {})
            .forEach(([field, messages]) => {
                const errorElement =
                    document.getElementById(`${field}Error`);

                const inputElement =
                    document.getElementById(field);

                if (!errorElement) {
                    return;
                }

                errorElement.textContent =
                    Array.isArray(messages)
                        ? messages[0]
                        : messages;

                errorElement.classList.add('show');

                if (inputElement) {
                    inputElement.classList.add('input-error');
                }
            });
    }


    function resetForm() {
        elements.form.reset();

        state.editingId = null;

        elements.merchantId.value = '';

        elements.status.value = 'active';

        elements.timezone.value = 'UTC';

        elements.currency.value = 'USD';

        clearErrors();
    }


    function openCreateModal() {
        resetForm();

        elements.modalTitle.textContent =
            'Add Merchant';

        elements.saveButton.textContent =
            'Save Merchant';

        elements.modal.classList.add('show');

        elements.name.focus();
    }


    async function openEditModal(id) {
        try {
            resetForm();

            elements.modalTitle.textContent =
                'Edit Merchant';

            elements.saveButton.textContent =
                'Update Merchant';

            elements.modal.classList.add('show');

            const result = await request(
                `/merchants/${id}`
            );

            const merchant = result.data;

            state.editingId = merchant.id;

            elements.merchantId.value =
                merchant.id;

            elements.name.value =
                merchant.name ?? '';

            elements.code.value =
                merchant.code ?? '';

            elements.email.value =
                merchant.email ?? '';

            elements.status.value =
                merchant.status ?? 'active';

            elements.timezone.value =
                merchant.timezone ?? 'UTC';

            elements.currency.value =
                merchant.currency ?? 'USD';

            elements.phone.value =
                merchant.phone ?? '';

            elements.metadata.value =
                merchant.metadata
                    ? JSON.stringify(
                        merchant.metadata,
                        null,
                        2
                    )
                    : '';

        } catch (error) {
            closeModal();

            showToast(
                error.message ||
                'Unable to load merchant.',
                'error'
            );
        }
    }


    function closeModal() {
        elements.modal.classList.remove('show');

        resetForm();
    }


    function getFormData() {
        let metadata = null;

        const metadataText =
            elements.metadata.value.trim();

        if (metadataText !== '') {
            try {
                metadata = JSON.parse(metadataText);
            } catch (error) {
                throw new Error(
                    'Metadata must contain valid JSON.'
                );
            }
        }

        return {
            name: elements.name.value.trim(),
            code: elements.code.value.trim(),
            email: elements.email.value.trim(),
            status: elements.status.value,
            timezone: elements.timezone.value.trim() || null,
            currency: elements.currency.value
                .trim()
                .toUpperCase() || null,
            phone: elements.phone.value.trim() || null,
            metadata,
        };
    }


    async function saveMerchant(event) {
        event.preventDefault();

        clearErrors();

        let data;

        try {
            data = getFormData();
        } catch (error) {
            showToast(
                error.message,
                'error'
            );

            return;
        }

        elements.saveButton.disabled = true;

        elements.saveButton.textContent =
            state.editingId
                ? 'Updating...'
                : 'Saving...';

        try {
            let url = '/merchants';

            let method = 'POST';

            if (state.editingId) {
                url = `/merchants/${state.editingId}`;

                method = 'PUT';
            }

            const result = await request(
                url,
                {
                    method,
                    body: JSON.stringify(data),
                }
            );

            showToast(
                result.message ||
                'Merchant saved successfully.'
            );

            closeModal();

            await loadMerchants();

        } catch (error) {
            if (
                error.status === 422 &&
                error.response?.errors
            ) {
                showValidationErrors(
                    error.response.errors
                );
            } else {
                showToast(
                    error.message ||
                    'Unable to save merchant.',
                    'error'
                );
            }

        } finally {
            elements.saveButton.disabled = false;

            elements.saveButton.textContent =
                state.editingId
                    ? 'Update Merchant'
                    : 'Save Merchant';
        }
    }


    async function deleteMerchant(id) {
        const confirmed = window.confirm(
            'Are you sure you want to delete this merchant?'
        );

        if (!confirmed) {
            return;
        }

        try {
            const result = await request(
                `/merchants/${id}`,
                {
                    method: 'DELETE',
                }
            );

            showToast(
                result.message ||
                'Merchant deleted successfully.'
            );

            const currentPage =
                state.page;

            await loadMerchants();

            /*
             * If the current page became empty after
             * deleting the final record, move back one page.
             */
            const rows =
                elements.tableBody.querySelectorAll(
                    'tr[data-merchant-id]'
                );

            if (
                rows.length === 0 &&
                currentPage > 1
            ) {
                state.page = currentPage - 1;

                await loadMerchants();
            }

        } catch (error) {
            showToast(
                error.message ||
                'Unable to delete merchant.',
                'error'
            );
        }
    }


    function renderMerchants(
        merchants
    ) {
        if (!merchants || merchants.length === 0) {
            elements.tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="7"
                        class="loading-row"
                    >
                        No merchants found.
                    </td>
                </tr>
            `;

            return;
        }

        elements.tableBody.innerHTML =
            merchants.map((merchant) => {

                const status =
                    String(
                        merchant.status ?? ''
                    ).toLowerCase();

                const statusClass =
                    status === 'active'
                        ? 'badge-success'
                        : 'badge-neutral';

                return `
                    <tr
                        data-merchant-id="${escapeHtml(
                            merchant.id
                        )}"
                    >

                        <td>
                            <div class="customer-name">
                                ${escapeHtml(
                                    merchant.name
                                )}
                            </div>
                        </td>

                        <td>
                            <span class="number">
                                ${escapeHtml(
                                    merchant.code
                                )}
                            </span>
                        </td>

                        <td>
                            ${escapeHtml(
                                merchant.email
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                merchant.currency || '-'
                            )}
                        </td>

                        <td>
                            <span
                                class="badge ${statusClass}"
                            >
                                ${escapeHtml(
                                    status
                                        ? status
                                            .charAt(0)
                                            .toUpperCase()
                                            + status.slice(1)
                                        : 'Unknown'
                                )}
                            </span>
                        </td>

                        <td>
                            ${
                                merchant.created_at
                                    ? new Date(
                                        merchant.created_at
                                    ).toLocaleDateString(
                                        'en-IN',
                                        {
                                            day: '2-digit',
                                            month: 'short',
                                            year: 'numeric'
                                        }
                                    )
                                    : '-'
                            }
                        </td>

                        <td>

                            <div class="action-group">

                                <button
                                    type="button"
                                    class="edit-button"
                                    data-action="edit"
                                    data-id="${escapeHtml(
                                        merchant.id
                                    )}"
                                >
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="danger-button"
                                    data-action="delete"
                                    data-id="${escapeHtml(
                                        merchant.id
                                    )}"
                                >
                                    Delete
                                </button>

                            </div>

                        </td>

                    </tr>
                `;
            }).join('');
    }


    function renderPagination(data) {
        const currentPage =
            Number(data.current_page || 1);

        const lastPage =
            Number(data.last_page || 1);

        const from =
            Number(data.from || 0);

        const to =
            Number(data.to || 0);

        const total =
            Number(data.total || 0);

        if (total === 0) {
            elements.paginationInfo.textContent =
                'No records found.';

            elements.paginationButtons.innerHTML =
                '';

            return;
        }

        elements.paginationInfo.textContent =
            `Showing ${from}-${to} of ${total} merchants`;

        const buttons = [];

        buttons.push(`
            <button
                type="button"
                class="pagination-button"
                data-page="${currentPage - 1}"
                ${currentPage <= 1 ? 'disabled' : ''}
            >
                Previous
            </button>
        `);

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

        for (
            let page = startPage;
            page <= endPage;
            page++
        ) {
            buttons.push(`
                <button
                    type="button"
                    class="pagination-button ${
                        page === currentPage
                            ? 'active'
                            : ''
                    }"
                    data-page="${page}"
                >
                    ${page}
                </button>
            `);
        }

        buttons.push(`
            <button
                type="button"
                class="pagination-button"
                data-page="${currentPage + 1}"
                ${currentPage >= lastPage ? 'disabled' : ''}
            >
                Next
            </button>
        `);

        elements.paginationButtons.innerHTML =
            buttons.join('');
    }


    async function loadMerchants() {
        elements.tableBody.innerHTML = `
            <tr>
                <td
                    colspan="7"
                    class="loading-row"
                >
                    Loading merchants...
                </td>
            </tr>
        `;

        const params =
            new URLSearchParams({
                page: String(state.page),
                per_page: String(state.perPage),
                search: state.search,
            });

        try {
            const result = await request(
                `/merchants/data?${params.toString()}`
            );

            const data = result.data;

            /*
             * Supports both paginator-shaped responses
             * and plain collection responses.
             */
            const merchants =
                Array.isArray(data)
                    ? data
                    : (
                        data?.data || []
                    );

            renderMerchants(
                merchants
            );

            if (Array.isArray(data)) {
                renderPagination({
                    current_page: 1,
                    last_page: 1,
                    per_page: state.perPage,
                    total: merchants.length,
                    from: merchants.length
                        ? 1
                        : 0,
                    to: merchants.length,
                });

                return;
            }

            renderPagination(data);

        } catch (error) {

            elements.tableBody.innerHTML = `
                <tr>
                    <td
                        colspan="7"
                        class="loading-row"
                    >
                        Unable to load merchants.
                    </td>
                </tr>
            `;

            showToast(
                error.message ||
                'Unable to load merchants.',
                'error'
            );
        }
    }


    /* =========================
       EVENTS
    ========================= */

    elements.addButton.addEventListener(
        'click',
        openCreateModal
    );


    elements.closeButton.addEventListener(
        'click',
        closeModal
    );


    elements.cancelButton.addEventListener(
        'click',
        closeModal
    );


    elements.modal.addEventListener(
        'click',
        (event) => {
            if (
                event.target === elements.modal
            ) {
                closeModal();
            }
        }
    );


    elements.form.addEventListener(
        'submit',
        saveMerchant
    );


    elements.pageSize.addEventListener(
        'change',
        () => {
            state.perPage =
                Number(
                    elements.pageSize.value
                );

            state.page = 1;

            loadMerchants();
        }
    );


    let searchTimer = null;

    elements.search.addEventListener(
        'input',
        () => {
            clearTimeout(
                searchTimer
            );

            searchTimer = setTimeout(
                () => {
                    state.search =
                        elements.search.value.trim();

                    state.page = 1;

                    loadMerchants();
                },
                300
            );
        }
    );


    elements.paginationButtons.addEventListener(
        'click',
        (event) => {
            const button =
                event.target.closest(
                    '[data-page]'
                );

            if (!button) {
                return;
            }

            if (
                button.disabled
            ) {
                return;
            }

            const page =
                Number(
                    button.dataset.page
                );

            if (
                page < 1 ||
                page > 999999
            ) {
                return;
            }

            state.page = page;

            loadMerchants();
        }
    );


    elements.tableBody.addEventListener(
        'click',
        (event) => {
            const button =
                event.target.closest(
                    '[data-action]'
                );

            if (!button) {
                return;
            }

            const action =
                button.dataset.action;

            const id =
                Number(
                    button.dataset.id
                );

            if (!id) {
                return;
            }

            if (action === 'edit') {
                openEditModal(id);
            }

            if (action === 'delete') {
                deleteMerchant(id);
            }
        }
    );


    document.addEventListener(
        'keydown',
        (event) => {
            if (
                event.key === 'Escape' &&
                elements.modal.classList.contains(
                    'show'
                )
            ) {
                closeModal();
            }
        }
    );


    /*
     * Initial load.
     */
    loadMerchants();

})();
</script>

@endsection