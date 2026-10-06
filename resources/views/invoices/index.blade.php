@extends('layouts.app')

@section('title', 'Invoices')
@section('page_heading', 'Invoices')

@section('content')

<style>
    .invoice-page {
        padding: 24px;
    }

    .invoice-toolbar {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 18px;
        margin-bottom: 18px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
    }

    .toolbar-row {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .form-control,
    .form-select {
        height: 40px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 14px;
        background: #fff;
    }

    .search-input {
        min-width: 260px;
        flex: 1;
    }

    .btn {
        height: 40px;
        border: 0;
        border-radius: 8px;
        padding: 0 16px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
    }

    .btn:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .btn-primary {
        background: #2563eb;
        color: #fff;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-secondary {
        background: #6b7280;
        color: #fff;
    }

    .btn-success {
        background: #16a34a;
        color: #fff;
    }

    .btn-success:hover {
        background: #15803d;
    }

    .btn-warning {
        background: #d97706;
        color: #fff;
    }

    .btn-danger {
        background: #dc2626;
        color: #fff;
    }

    .btn-light {
        background: #f3f4f6;
        color: #374151;
    }

    .invoice-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
        overflow: hidden;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .invoice-table {
        width: 100%;
        border-collapse: collapse;
    }

    .invoice-table th {
        background: #f9fafb;
        color: #374151;
        font-size: 13px;
        font-weight: 600;
        text-align: left;
        padding: 13px 14px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .invoice-table td {
        padding: 13px 14px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        color: #374151;
        vertical-align: middle;
    }

    .invoice-table tr:hover td {
        background: #fafafa;
    }

    .invoice-number {
        font-weight: 600;
        color: #2563eb;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-draft {
        background: #f3f4f6;
        color: #4b5563;
    }

    .status-issued {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-paid {
        background: #dcfce7;
        color: #15803d;
    }

    .status-void {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-completed {
        background: #dcfce7;
        color: #15803d;
    }

    .status-failed {
        background: #fee2e2;
        color: #b91c1c;
    }

    .status-refunded {
        background: #ede9fe;
        color: #6d28d9;
    }

    .action-group {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .action-btn {
        border: 0;
        border-radius: 6px;
        padding: 6px 9px;
        font-size: 12px;
        cursor: pointer;
    }

    .action-btn:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .action-view {
        background: #e0e7ff;
        color: #3730a3;
    }

    .action-issue {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .action-pay {
        background: #dcfce7;
        color: #15803d;
    }

    .action-void {
        background: #fee2e2;
        color: #b91c1c;
    }

    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #6b7280;
    }

    .loading-state {
        padding: 35px;
        text-align: center;
        color: #6b7280;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 18px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .pagination-info {
        color: #6b7280;
        font-size: 13px;
    }

    .pagination {
        display: flex;
        gap: 5px;
    }

    .page-btn {
        min-width: 34px;
        height: 34px;
        border: 1px solid #d1d5db;
        background: #fff;
        border-radius: 6px;
        cursor: pointer;
    }

    .page-btn.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    .page-btn:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .55);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        padding: 20px;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal {
        width: 100%;
        max-width: 720px;
        max-height: 90vh;
        overflow-y: auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, .2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .modal-header h3 {
        margin: 0;
        font-size: 18px;
    }

    .modal-close {
        border: 0;
        background: transparent;
        font-size: 24px;
        cursor: pointer;
        color: #6b7280;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
    }

    .form-control-full,
    .form-select-full {
        width: 100%;
        height: 42px;
        box-sizing: border-box;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 14px;
    }

    .form-control-full:focus,
    .form-select-full:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
    }

    .error-text {
        color: #dc2626;
        font-size: 12px;
        margin-top: 5px;
    }

    .period-info {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
        margin-top: 12px;
        display: none;
    }

    .period-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .info-item label {
        display: block;
        color: #64748b;
        font-size: 11px;
        margin-bottom: 2px;
    }

    .info-item span {
        font-weight: 600;
        color: #334155;
        font-size: 13px;
    }

    .invoice-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .summary-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        padding: 12px;
    }

    .summary-box label {
        display: block;
        font-size: 11px;
        color: #64748b;
        margin-bottom: 4px;
    }

    .summary-box strong {
        font-size: 15px;
        color: #1e293b;
    }

    .detail-section {
        margin-top: 20px;
    }

    .detail-section h4 {
        margin: 0 0 10px;
        font-size: 14px;
        color: #334155;
    }

    .detail-table {
        width: 100%;
        border-collapse: collapse;
    }

    .detail-table th,
    .detail-table td {
        padding: 9px 10px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 13px;
        text-align: left;
    }

    .detail-table th {
        background: #f8fafc;
        font-weight: 600;
    }

    .totals {
        margin-left: auto;
        max-width: 320px;
        margin-top: 15px;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 7px 0;
        font-size: 13px;
    }

    .total-row.grand-total {
        border-top: 2px solid #1e293b;
        margin-top: 5px;
        padding-top: 10px;
        font-size: 17px;
        font-weight: 700;
    }

    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 20000;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .toast {
        min-width: 280px;
        max-width: 420px;
        padding: 13px 16px;
        border-radius: 8px;
        color: #fff;
        box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
        font-size: 14px;
    }

    .toast.success {
        background: #16a34a;
    }

    .toast.error {
        background: #dc2626;
    }

    @media (max-width: 900px) {
        .invoice-summary {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .invoice-page {
            padding: 12px;
        }

        .invoice-summary {
            grid-template-columns: 1fr;
        }

        .period-info-grid {
            grid-template-columns: 1fr;
        }

        .search-input {
            min-width: 100%;
        }
    }
</style>

<div class="invoice-page">

    <div class="invoice-toolbar">

        <div class="toolbar-row">

            <input
                type="text"
                id="searchInput"
                class="form-control search-input"
                placeholder="Search invoice, customer, code..."
            >

            <select id="merchantFilter" class="form-select">
                <option value="">Select Merchant</option>
            </select>

            <select id="customerFilter" class="form-select">
                <option value="">All Customers</option>
            </select>

            <select id="statusFilter" class="form-select">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="issued">Issued</option>
                <option value="paid">Paid</option>
                <option value="void">Void</option>
            </select>

            <select id="perPage" class="form-select">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>

            <button
                type="button"
                class="btn btn-light"
                onclick="resetFilters()"
            >
                Reset
            </button>

            <button
                type="button"
                class="btn btn-primary"
                onclick="openGenerateModal()"
            >
                + Generate Invoice
            </button>

        </div>

    </div>

    <div class="invoice-card">

        <div class="table-wrapper">

            <table class="invoice-table">

                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Subscription</th>
                        <th>Billing Period</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody id="invoiceTableBody">

                    <tr>
                        <td colspan="7" class="loading-state">
                            Loading invoices...
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

        <div class="pagination-wrapper">

            <div id="paginationInfo" class="pagination-info">
                Showing 0 invoices
            </div>

            <div id="pagination" class="pagination"></div>

        </div>

    </div>

</div>


{{-- Generate Invoice Modal --}}
<div id="generateModal" class="modal-backdrop">

    <div class="modal">

        <div class="modal-header">

            <h3>Generate Invoice</h3>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('generateModal')"
            >
                &times;
            </button>

        </div>

        <form id="generateForm">

            <div class="modal-body">

                <div class="form-group">

                    <label class="form-label">
                        Subscription Period *
                    </label>

                    <select
                        id="subscriptionPeriodId"
                        name="subscription_period_id"
                        class="form-select-full"
                        required
                    >
                        <option value="">
                            Select Subscription Period
                        </option>
                    </select>

                    <div
                        id="periodError"
                        class="error-text"
                    ></div>

                </div>

                <div
                    id="periodInfo"
                    class="period-info"
                >

                    <div class="period-info-grid">

                        <div class="info-item">
                            <label>Period ID</label>
                            <span id="infoPeriodId">-</span>
                        </div>

                        <div class="info-item">
                            <label>Subscription</label>
                            <span id="infoSubscription">-</span>
                        </div>

                        <div class="info-item">
                            <label>Customer</label>
                            <span id="infoCustomer">-</span>
                        </div>

                        <div class="info-item">
                            <label>Plan</label>
                            <span id="infoPlan">-</span>
                        </div>

                        <div class="info-item">
                            <label>Start</label>
                            <span id="infoStart">-</span>
                        </div>

                        <div class="info-item">
                            <label>End</label>
                            <span id="infoEnd">-</span>
                        </div>

                        <div class="info-item">
                            <label>Base Price</label>
                            <span id="infoBasePrice">-</span>
                        </div>

                        <div class="info-item">
                            <label>Included Units</label>
                            <span id="infoIncludedUnits">-</span>
                        </div>

                    </div>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Tax Rate (%)
                    </label>

                    <input
                        type="number"
                        id="taxRate"
                        name="tax_rate"
                        class="form-control-full"
                        value="0"
                        min="0"
                        max="100"
                        step="0.01"
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Due At
                    </label>

                    <input
                        type="datetime-local"
                        id="dueAt"
                        name="due_at"
                        class="form-control-full"
                    >

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    onclick="closeModal('generateModal')"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                    id="generateButton"
                >
                    Generate Invoice
                </button>

            </div>

        </form>

    </div>

</div>


{{-- Invoice Details Modal --}}
<div id="detailsModal" class="modal-backdrop">

    <div class="modal">

        <div class="modal-header">

            <h3 id="detailsTitle">
                Invoice Details
            </h3>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('detailsModal')"
            >
                &times;
            </button>

        </div>

        <div class="modal-body">

            <div class="invoice-summary">

                <div class="summary-box">
                    <label>Invoice Number</label>
                    <strong id="detailInvoiceNumber">-</strong>
                </div>

                <div class="summary-box">
                    <label>Status</label>
                    <strong id="detailStatus">-</strong>
                </div>

                <div class="summary-box">
                    <label>Customer</label>
                    <strong id="detailCustomer">-</strong>
                </div>

                <div class="summary-box">
                    <label>Currency</label>
                    <strong id="detailCurrency">-</strong>
                </div>

            </div>

            <div class="detail-section">

                <h4>Billing Information</h4>

                <table class="detail-table">

                    <tbody>

                        <tr>
                            <th>Subscription</th>
                            <td id="detailSubscription">-</td>
                        </tr>

                        <tr>
                            <th>Billing Period</th>
                            <td id="detailBillingPeriod">-</td>
                        </tr>

                        <tr>
                            <th>Issued At</th>
                            <td id="detailIssuedAt">-</td>
                        </tr>

                        <tr>
                            <th>Due At</th>
                            <td id="detailDueAt">-</td>
                        </tr>

                        <tr>
                            <th>Paid At</th>
                            <td id="detailPaidAt">-</td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="detail-section">

                <h4>Invoice Items</h4>

                <div class="table-wrapper">

                    <table class="detail-table">

                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                            </tr>
                        </thead>

                        <tbody id="detailItems">
                        </tbody>

                    </table>

                </div>

            </div>

            <div class="detail-section">

                <h4>Payment History</h4>

                <div class="table-wrapper">

                    <table class="detail-table">

                        <thead>

                            <tr>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Paid At</th>
                            </tr>

                        </thead>

                        <tbody id="detailPayments">

                            <tr>
                                <td colspan="5">
                                    Loading payments...
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

            <div class="totals">

                <div class="total-row">
                    <span>Base Amount</span>
                    <strong id="detailBaseAmount">0.00</strong>
                </div>

                <div class="total-row">
                    <span>Overage</span>
                    <strong id="detailOverageAmount">0.00</strong>
                </div>

                <div class="total-row">
                    <span>Proration</span>
                    <strong id="detailProrationAmount">0.00</strong>
                </div>

                <div class="total-row">
                    <span>Subtotal</span>
                    <strong id="detailSubtotal">0.00</strong>
                </div>

                <div class="total-row">
                    <span>Tax</span>
                    <strong id="detailTaxAmount">0.00</strong>
                </div>

                <div class="total-row grand-total">
                    <span>Total</span>
                    <strong id="detailTotal">0.00</strong>
                </div>

            </div>

        </div>

        <div class="modal-footer">

            <button
                type="button"
                class="btn btn-light"
                onclick="closeModal('detailsModal')"
            >
                Close
            </button>

        </div>

    </div>

</div>


{{-- Payment Modal --}}
<div id="paymentModal" class="modal-backdrop">

    <div class="modal">

        <div class="modal-header">

            <h3>Make Payment</h3>

            <button
                type="button"
                class="modal-close"
                onclick="closeModal('paymentModal')"
            >
                &times;
            </button>

        </div>

        <form id="paymentForm">

            <div class="modal-body">

                <div class="invoice-summary">

                    <div class="summary-box">
                        <label>Invoice</label>
                        <strong id="paymentInvoiceNumber">-</strong>
                    </div>

                    <div class="summary-box">
                        <label>Total</label>
                        <strong id="paymentInvoiceTotal">-</strong>
                    </div>

                    <div class="summary-box">
                        <label>Paid</label>
                        <strong id="paymentAlreadyPaid">-</strong>
                    </div>

                    <div class="summary-box">
                        <label>Remaining</label>
                        <strong id="paymentRemaining">-</strong>
                    </div>

                </div>

                <input
                    type="hidden"
                    id="paymentMerchantId"
                >

                <input
                    type="hidden"
                    id="paymentCustomerId"
                >

                <input
                    type="hidden"
                    id="paymentInvoiceId"
                >

                <input
                    type="hidden"
                    id="paymentCurrency"
                >

                <div class="form-group">

                    <label class="form-label">
                        Amount *
                    </label>

                    <input
                        type="number"
                        id="paymentAmount"
                        class="form-control-full"
                        min="0.01"
                        step="0.01"
                        required
                    >

                    <div
                        id="paymentAmountError"
                        class="error-text"
                    ></div>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Payment Reference *
                    </label>

                    <input
                        type="text"
                        id="paymentReference"
                        class="form-control-full"
                        maxlength="255"
                        placeholder="e.g. TXN-20261005-001"
                        required
                    >

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Payment Method *
                    </label>

                    <select
                        id="paymentMethod"
                        class="form-select-full"
                        required
                    >
                        <option value="">
                            Select Payment Method
                        </option>

                        <option value="cash">
                            Cash
                        </option>

                        <option value="bank_transfer">
                            Bank Transfer
                        </option>

                        <option value="upi">
                            UPI
                        </option>

                        <option value="card">
                            Card
                        </option>

                        <option value="other">
                            Other
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label class="form-label">
                        Status *
                    </label>

                    <select
                        id="paymentStatus"
                        class="form-select-full"
                        required
                    >
                        <option value="completed">
                            Completed
                        </option>

                        <option value="pending">
                            Pending
                        </option>
                    </select>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    onclick="closeModal('paymentModal')"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-success"
                    id="paymentSubmitButton"
                >
                    Create Payment
                </button>

            </div>

        </form>

    </div>

</div>


<div id="toastContainer" class="toast-container"></div>


<script>

const invoiceState = {
    page: 1,
    perPage: 10,
    periods: [],
    merchants: [],
    customers: [],
    paymentRemaining: 0
};


function escapeHtml(value) {

    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}


async function request(url, options = {}) {

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    const response = await fetch(url, {
        credentials: 'same-origin',
        ...options,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(csrfToken
                ? {
                    'X-CSRF-TOKEN': csrfToken
                }
                : {}),
            ...(options.headers || {})
        }
    });

    let result = null;

    try {
        result = await response.json();
    } catch (error) {
        result = null;
    }

    if (
        !response.ok ||
        result?.success === false
    ) {

        let message =
            result?.message ||
            'Something went wrong.';

        if (
            result?.errors &&
            typeof result.errors === 'object'
        ) {

            const firstKey =
                Object.keys(result.errors)[0];

            if (firstKey) {

                const firstError =
                    result.errors[firstKey];

                if (Array.isArray(firstError)) {
                    message = firstError[0];
                } else if (firstError) {
                    message = firstError;
                }
            }
        }

        const error =
            new Error(message);

        error.status =
            response.status;

        error.data =
            result;

        throw error;
    }

    return result;
}


function showToast(
    message,
    type = 'success'
) {

    const container =
        document.getElementById(
            'toastContainer'
        );

    const toast =
        document.createElement('div');

    toast.className =
        `toast ${type}`;

    toast.textContent =
        message;

    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3500);
}


function openModal(id) {

    document
        .getElementById(id)
        .classList.add('show');
}


function closeModal(id) {

    document
        .getElementById(id)
        .classList.remove('show');
}


function formatMoney(
    amount,
    currency = ''
) {

    const value =
        Number(amount || 0)
            .toFixed(2);

    return `${
        currency
            ? currency + ' '
            : ''
    }${value}`;
}


function formatDate(value) {

    if (!value) {
        return '-';
    }

    const date =
        new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleDateString(
        'en-IN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );
}


function formatDateTime(value) {

    if (!value) {
        return '-';
    }

    const date =
        new Date(value);

    if (Number.isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString(
        'en-IN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }
    );
}


function getCustomerName(invoice) {

    return invoice.customer?.name ||
        invoice.customer_name ||
        `Customer #${
            invoice.customer_id || '-'
        }`;
}


function getSubscriptionId(invoice) {

    return invoice.subscription?.id ||
        invoice.subscription_id ||
        '-';
}


function statusBadge(status) {

    const normalized =
        String(status || '')
            .toLowerCase();

    return `
        <span class="status-badge status-${escapeHtml(normalized)}">
            ${escapeHtml(normalized || '-')}
        </span>
    `;
}


async function loadMerchants() {

    try {

        const result =
            await request(
                '/merchants/data?per_page=100'
            );

        const merchants =
            result?.data?.items ||
            result?.data?.data ||
            result?.data ||
            [];

        invoiceState.merchants =
            merchants;

        const select =
            document.getElementById(
                'merchantFilter'
            );

        select.innerHTML =
            '<option value="">Select Merchant</option>';

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

                select.appendChild(
                    option
                );
            }
        );

    } catch (error) {

        console.error(error);

        showToast(
            'Unable to load merchants.',
            'error'
        );
    }
}


async function loadCustomers() {

    try {

        const result =
            await request(
                '/customers/data?per_page=100'
            );

        const customers =
            result?.data?.items ||
            result?.data?.data ||
            result?.data ||
            [];

        invoiceState.customers =
            customers;

        renderCustomerFilter(
            customers
        );

    } catch (error) {

        console.error(error);

        showToast(
            'Unable to load customers.',
            'error'
        );
    }
}


function renderCustomerFilter(
    customers
) {

    const select =
        document.getElementById(
            'customerFilter'
        );

    select.innerHTML =
        '<option value="">All Customers</option>';

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

            select.appendChild(
                option
            );
        }
    );
}


async function loadPeriods() {

    try {

        const result =
            await request(
                '/subscription-periods/data?per_page=100'
            );

        const periods =
            result?.data?.items ||
            result?.data?.data ||
            result?.data ||
            [];

        invoiceState.periods =
            periods;

        const select =
            document.getElementById(
                'subscriptionPeriodId'
            );

        select.innerHTML =
            '<option value="">Select Subscription Period</option>';

        periods.forEach(
            period => {

                const subscription =
                    period.subscription ||
                    {};

                const customer =
                    subscription.customer ||
                    period.customer ||
                    {};

                const plan =
                    period.plan ||
                    subscription.plan ||
                    {};

                const option =
                    document.createElement(
                        'option'
                    );

                option.value =
                    period.id;

                option.textContent =
                    `Period #${period.id} | ` +
                    `Subscription #${
                        subscription.id ||
                        period.subscription_id ||
                        '-'
                    } | ` +
                    `${
                        customer.name ||
                        'Customer'
                    } | ` +
                    `${
                        plan.name ||
                        'Plan'
                    } | ` +
                    `${
                        formatDate(
                            period.starts_at
                        )
                    } - ${
                        formatDate(
                            period.ends_at
                        )
                    }`;

                select.appendChild(
                    option
                );
            }
        );

    } catch (error) {

        console.error(error);

        showToast(
            'Unable to load subscription periods.',
            'error'
        );
    }
}


function updatePeriodInfo() {

    const id =
        Number(
            document.getElementById(
                'subscriptionPeriodId'
            ).value
        );

    const info =
        document.getElementById(
            'periodInfo'
        );

    if (!id) {

        info.style.display =
            'none';

        return;
    }

    const period =
        invoiceState.periods.find(
            item =>
                Number(item.id) === id
        );

    if (!period) {

        info.style.display =
            'none';

        return;
    }

    const subscription =
        period.subscription ||
        {};

    const customer =
        subscription.customer ||
        period.customer ||
        {};

    const plan =
        period.plan ||
        subscription.plan ||
        {};

    document.getElementById(
        'infoPeriodId'
    ).textContent =
        period.id || '-';

    document.getElementById(
        'infoSubscription'
    ).textContent =
        subscription.id ||
        period.subscription_id ||
        '-';

    document.getElementById(
        'infoCustomer'
    ).textContent =
        customer.name ||
        customer.code ||
        '-';

    document.getElementById(
        'infoPlan'
    ).textContent =
        plan.name ||
        `Plan #${period.plan_id || '-'}`;

    document.getElementById(
        'infoStart'
    ).textContent =
        formatDate(
            period.starts_at
        );

    document.getElementById(
        'infoEnd'
    ).textContent =
        formatDate(
            period.ends_at
        );

    document.getElementById(
        'infoBasePrice'
    ).textContent =
        formatMoney(
            period.base_price,
            period.currency || ''
        );

    document.getElementById(
        'infoIncludedUnits'
    ).textContent =
        period.included_units ?? 0;

    info.style.display =
        'block';
}


async function loadInvoices(
    page = 1
) {

    const tbody =
        document.getElementById(
            'invoiceTableBody'
        );

    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="loading-state">
                Loading invoices...
            </td>
        </tr>
    `;

    const params =
        new URLSearchParams();

    const merchantId =
        document.getElementById(
            'merchantFilter'
        ).value;

    const customerId =
        document.getElementById(
            'customerFilter'
        ).value;

    const status =
        document.getElementById(
            'statusFilter'
        ).value;

    const search =
        document.getElementById(
            'searchInput'
        ).value.trim();

    invoiceState.page =
        page;

    invoiceState.perPage =
        Number(
            document.getElementById(
                'perPage'
            ).value
        );

    if (merchantId) {
        params.set(
            'merchant_id',
            merchantId
        );
    }

    if (customerId) {
        params.set(
            'customer_id',
            customerId
        );
    }

    if (status) {
        params.set(
            'status',
            status
        );
    }

    if (search) {
        params.set(
            'search',
            search
        );
    }

    params.set(
        'page',
        invoiceState.page
    );

    params.set(
        'per_page',
        invoiceState.perPage
    );

    try {

        if (!merchantId && !customerId) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="empty-state">
                        Select a merchant or customer to view invoices.
                    </td>
                </tr>
            `;

            updatePagination(
                0,
                1,
                1,
                0
            );

            return;
        }

        const result =
            await request(
                `/invoices/data?${params.toString()}`
            );

        const data =
            result?.data || {};

        const items =
            data.items || [];

        renderInvoices(
            items
        );

        updatePagination(
            Number(
                data.current_page || 1
            ),
            Number(
                data.last_page || 1
            ),
            Number(
                data.per_page ||
                invoiceState.perPage
            ),
            Number(
                data.total || 0
            )
        );

    } catch (error) {

        console.error(error);

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    ${escapeHtml(error.message)}
                </td>
            </tr>
        `;

        showToast(
            error.message,
            'error'
        );
    }
}


function renderInvoices(
    invoices
) {

    const tbody =
        document.getElementById(
            'invoiceTableBody'
        );

    if (!invoices.length) {

        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="empty-state">
                    No invoices found.
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML =
        invoices.map(
            invoice => {

                const currency =
                    invoice.currency ||
                    '';

                const customer =
                    getCustomerName(
                        invoice
                    );

                const subscriptionId =
                    getSubscriptionId(
                        invoice
                    );

                const start =
                    formatDate(
                        invoice.billing_period_start
                    );

                const end =
                    formatDate(
                        invoice.billing_period_end
                    );

                const status =
                    String(
                        invoice.status || ''
                    ).toLowerCase();

                let actions = `
                    <button
                        type="button"
                        class="action-btn action-view"
                        onclick="viewInvoice(${invoice.id})"
                    >
                        View
                    </button>
                `;

                if (status === 'draft') {

                    actions += `
                        <button
                            type="button"
                            class="action-btn action-issue"
                            onclick="issueInvoice(${invoice.id})"
                        >
                            Issue
                        </button>

                        <button
                            type="button"
                            class="action-btn action-void"
                            onclick="voidInvoice(${invoice.id})"
                        >
                            Void
                        </button>
                    `;
                }

                if (status === 'issued') {

                    actions += `
                        <button
                            type="button"
                            class="action-btn action-pay"
                            onclick="openPaymentModal(${invoice.id})"
                        >
                            Make Payment
                        </button>

                        <button
                            type="button"
                            class="action-btn action-void"
                            onclick="voidInvoice(${invoice.id})"
                        >
                            Void
                        </button>
                    `;
                }

                return `
                    <tr>

                        <td>
                            <span class="invoice-number">
                                ${escapeHtml(
                                    invoice.invoice_number ||
                                    `#${invoice.id}`
                                )}
                            </span>
                        </td>

                        <td>
                            ${escapeHtml(
                                customer
                            )}
                        </td>

                        <td>
                            #${escapeHtml(
                                subscriptionId
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                start
                            )}
                            -
                            ${escapeHtml(
                                end
                            )}
                        </td>

                        <td>
                            <strong>
                                ${escapeHtml(
                                    formatMoney(
                                        invoice.total,
                                        currency
                                    )
                                )}
                            </strong>
                        </td>

                        <td>
                            ${statusBadge(
                                status
                            )}
                        </td>

                        <td>
                            <div class="action-group">
                                ${actions}
                            </div>
                        </td>

                    </tr>
                `;

            }
        ).join('');
}


function updatePagination(
    currentPage,
    lastPage,
    perPage,
    total
) {

    const info =
        document.getElementById(
            'paginationInfo'
        );

    const pagination =
        document.getElementById(
            'pagination'
        );

    if (total === 0) {

        info.textContent =
            'Showing 0 invoices';

        pagination.innerHTML =
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

    info.textContent =
        `Showing ${from}-${to} of ${total} invoices`;

    let html = '';

    html += `
        <button
            class="page-btn"
            ${
                currentPage <= 1
                    ? 'disabled'
                    : ''
            }
            onclick="loadInvoices(${
                currentPage - 1
            })"
        >
            ‹
        </button>
    `;

    const start =
        Math.max(
            1,
            currentPage - 2
        );

    const end =
        Math.min(
            lastPage,
            currentPage + 2
        );

    for (
        let page = start;
        page <= end;
        page++
    ) {

        html += `
            <button
                class="page-btn ${
                    page === currentPage
                        ? 'active'
                        : ''
                }"
                onclick="loadInvoices(${page})"
            >
                ${page}
            </button>
        `;
    }

    html += `
        <button
            class="page-btn"
            ${
                currentPage >= lastPage
                    ? 'disabled'
                    : ''
            }
            onclick="loadInvoices(${
                currentPage + 1
            })"
        >
            ›
        </button>
    `;

    pagination.innerHTML =
        html;
}


function resetFilters() {

    document.getElementById(
        'searchInput'
    ).value = '';

    document.getElementById(
        'merchantFilter'
    ).value = '';

    document.getElementById(
        'customerFilter'
    ).value = '';

    document.getElementById(
        'statusFilter'
    ).value = '';

    document.getElementById(
        'perPage'
    ).value = '10';

    loadInvoices(1);
}


async function viewInvoice(id) {

    try {

        const result =
            await request(
                `/invoices/${id}`
            );

        const invoice =
            result?.data;

        if (!invoice) {

            throw new Error(
                'Invoice data was not returned.'
            );
        }

        renderInvoiceDetails(
            invoice
        );

        await renderInvoicePayments(
            invoice.id
        );

        openModal(
            'detailsModal'
        );

    } catch (error) {

        console.error(error);

        showToast(
            error.message,
            'error'
        );
    }
}


function renderInvoiceDetails(
    invoice
) {

    const currency =
        invoice.currency ||
        '';

    document.getElementById(
        'detailsTitle'
    ).textContent =
        invoice.invoice_number ||
        `Invoice #${invoice.id}`;

    document.getElementById(
        'detailInvoiceNumber'
    ).textContent =
        invoice.invoice_number ||
        `#${invoice.id}`;

    document.getElementById(
        'detailStatus'
    ).innerHTML =
        statusBadge(
            invoice.status
        );

    document.getElementById(
        'detailCustomer'
    ).textContent =
        getCustomerName(
            invoice
        );

    document.getElementById(
        'detailCurrency'
    ).textContent =
        currency ||
        '-';

    document.getElementById(
        'detailSubscription'
    ).textContent =
        `#${getSubscriptionId(
            invoice
        )}`;

    document.getElementById(
        'detailBillingPeriod'
    ).textContent =
        `${
            formatDate(
                invoice.billing_period_start
            )
        } - ${
            formatDate(
                invoice.billing_period_end
            )
        }`;

    document.getElementById(
        'detailIssuedAt'
    ).textContent =
        formatDateTime(
            invoice.issued_at
        );

    document.getElementById(
        'detailDueAt'
    ).textContent =
        formatDateTime(
            invoice.due_at
        );

    document.getElementById(
        'detailPaidAt'
    ).textContent =
        formatDateTime(
            invoice.paid_at
        );

    document.getElementById(
        'detailBaseAmount'
    ).textContent =
        formatMoney(
            invoice.base_amount,
            currency
        );

    document.getElementById(
        'detailOverageAmount'
    ).textContent =
        formatMoney(
            invoice.overage_amount,
            currency
        );

    document.getElementById(
        'detailProrationAmount'
    ).textContent =
        formatMoney(
            invoice.proration_amount,
            currency
        );

    document.getElementById(
        'detailSubtotal'
    ).textContent =
        formatMoney(
            invoice.subtotal,
            currency
        );

    document.getElementById(
        'detailTaxAmount'
    ).textContent =
        formatMoney(
            invoice.tax_amount,
            currency
        );

    document.getElementById(
        'detailTotal'
    ).textContent =
        formatMoney(
            invoice.total,
            currency
        );

    const items =
        invoice.items || [];

    const itemsBody =
        document.getElementById(
            'detailItems'
        );

    if (!items.length) {

        itemsBody.innerHTML = `
            <tr>
                <td colspan="5">
                    No invoice items found.
                </td>
            </tr>
        `;

    } else {

        itemsBody.innerHTML =
            items.map(
                item => {

                    return `
                        <tr>

                            <td>
                                ${escapeHtml(
                                    item.item_type ||
                                    '-'
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    item.description ||
                                    '-'
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    item.quantity ??
                                    0
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    formatMoney(
                                        item.unit_price,
                                        currency
                                    )
                                )}
                            </td>

                            <td>
                                <strong>
                                    ${escapeHtml(
                                        formatMoney(
                                            item.amount,
                                            currency
                                        )
                                    )}
                                </strong>
                            </td>

                        </tr>
                    `;

                }
            ).join('');
    }
}


async function loadInvoicePayments(
    invoiceId
) {

    const result =
        await request(
            `/payments/data?invoice_id=${invoiceId}`
        );

    return Array.isArray(
        result?.data
    )
        ? result.data
        : [];
}


async function renderInvoicePayments(
    invoiceId
) {

    const paymentsBody =
        document.getElementById(
            'detailPayments'
        );

    paymentsBody.innerHTML = `
        <tr>
            <td colspan="5">
                Loading payments...
            </td>
        </tr>
    `;

    try {

        const payments =
            await loadInvoicePayments(
                invoiceId
            );

        if (!payments.length) {

            paymentsBody.innerHTML = `
                <tr>
                    <td colspan="5">
                        No payments found.
                    </td>
                </tr>
            `;

            return;
        }

        paymentsBody.innerHTML =
            payments.map(
                payment => {

                    const status =
                        String(
                            payment.status || ''
                        ).toLowerCase();

                    return `
                        <tr>

                            <td>
                                ${escapeHtml(
                                    payment.payment_reference ||
                                    '-'
                                )}
                            </td>

                            <td>
                                <strong>
                                    ${escapeHtml(
                                        formatMoney(
                                            payment.amount,
                                            payment.currency ||
                                            ''
                                        )
                                    )}
                                </strong>
                            </td>

                            <td>
                                ${escapeHtml(
                                    payment.payment_method ||
                                    '-'
                                )}
                            </td>

                            <td>
                                ${statusBadge(
                                    status
                                )}
                            </td>

                            <td>
                                ${escapeHtml(
                                    formatDateTime(
                                        payment.paid_at
                                    )
                                )}
                            </td>

                        </tr>
                    `;

                }
            ).join('');

    } catch (error) {

        console.error(error);

        paymentsBody.innerHTML = `
            <tr>
                <td colspan="5">
                    Unable to load payment history.
                </td>
            </tr>
        `;
    }
}


function openGenerateModal() {

    document.getElementById(
        'generateForm'
    ).reset();

    document.getElementById(
        'taxRate'
    ).value = '0';

    document.getElementById(
        'periodInfo'
    ).style.display =
        'none';

    document.getElementById(
        'periodError'
    ).textContent =
        '';

    openModal(
        'generateModal'
    );

    loadPeriods();
}


document.getElementById(
    'subscriptionPeriodId'
).addEventListener(
    'change',
    updatePeriodInfo
);


document.getElementById(
    'generateForm'
).addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();

        const periodId =
            document.getElementById(
                'subscriptionPeriodId'
            ).value;

        const taxRate =
            document.getElementById(
                'taxRate'
            ).value;

        const dueAt =
            document.getElementById(
                'dueAt'
            ).value;

        if (!periodId) {

            document.getElementById(
                'periodError'
            ).textContent =
                'Please select a subscription period.';

            return;
        }

        const button =
            document.getElementById(
                'generateButton'
            );

        button.disabled =
            true;

        button.textContent =
            'Generating...';

        try {

            const payload = {
                subscription_period_id:
                    Number(periodId),

                tax_rate:
                    Number(
                        taxRate || 0
                    )
            };

            if (dueAt) {
                payload.due_at =
                    dueAt;
            }

            const result =
                await request(
                    '/invoices/generate',
                    {
                        method: 'POST',
                        body: JSON.stringify(
                            payload
                        )
                    }
                );

            showToast(
                result?.message ||
                'Invoice generated successfully.'
            );

            closeModal(
                'generateModal'
            );

            loadInvoices(1);

        } catch (error) {

            console.error(error);

            showToast(
                error.message,
                'error'
            );

            document.getElementById(
                'periodError'
            ).textContent =
                error.message;

        } finally {

            button.disabled =
                false;

            button.textContent =
                'Generate Invoice';
        }
    }
);


async function openPaymentModal(
    invoiceId
) {

    try {

        const result =
            await request(
                `/invoices/${invoiceId}`
            );

        const invoice =
            result?.data;

        if (!invoice) {

            throw new Error(
                'Invoice data was not returned.'
            );
        }

        if (
            String(
                invoice.status
            ).toLowerCase() !== 'issued'
        ) {

            showToast(
                'Only issued invoices can receive payments.',
                'error'
            );

            return;
        }

        const currency =
            invoice.currency ||
            '';

        const total =
            Number(
                invoice.total || 0
            );

        const payments =
            await loadInvoicePayments(
                invoiceId
            );

        const completedAmount =
            payments
                .filter(
                    payment =>
                        String(
                            payment.status
                        ).toLowerCase() ===
                        'completed'
                )
                .reduce(
                    (
                        sum,
                        payment
                    ) =>
                        sum +
                        Number(
                            payment.amount ||
                            0
                        ),
                    0
                );

        const remaining =
            Math.max(
                0,
                total -
                completedAmount
            );

        if (remaining <= 0) {

            showToast(
                'This invoice is already fully paid.',
                'error'
            );

            return;
        }

        invoiceState.paymentRemaining =
            remaining;

        document.getElementById(
            'paymentInvoiceId'
        ).value =
            invoice.id;

        document.getElementById(
            'paymentMerchantId'
        ).value =
            invoice.merchant_id ||
            invoice.merchant?.id ||
            '';

        document.getElementById(
            'paymentCustomerId'
        ).value =
            invoice.customer_id ||
            invoice.customer?.id ||
            '';

        document.getElementById(
            'paymentCurrency'
        ).value =
            currency;

        document.getElementById(
            'paymentInvoiceNumber'
        ).textContent =
            invoice.invoice_number ||
            `#${invoice.id}`;

        document.getElementById(
            'paymentInvoiceTotal'
        ).textContent =
            formatMoney(
                total,
                currency
            );

        document.getElementById(
            'paymentAlreadyPaid'
        ).textContent =
            formatMoney(
                completedAmount,
                currency
            );

        document.getElementById(
            'paymentRemaining'
        ).textContent =
            formatMoney(
                remaining,
                currency
            );

        document.getElementById(
            'paymentAmount'
        ).value =
            remaining.toFixed(2);

        document.getElementById(
            'paymentAmount'
        ).max =
            remaining.toFixed(2);

        document.getElementById(
            'paymentReference'
        ).value =
            '';

        document.getElementById(
            'paymentMethod'
        ).value =
            '';

        document.getElementById(
            'paymentStatus'
        ).value =
            'completed';

        document.getElementById(
            'paymentAmountError'
        ).textContent =
            '';

        openModal(
            'paymentModal'
        );

    } catch (error) {

        console.error(error);

        showToast(
            error.message,
            'error'
        );
    }
}


document.getElementById(
    'paymentForm'
).addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();

        const invoiceId =
            Number(
                document.getElementById(
                    'paymentInvoiceId'
                ).value
            );

        const merchantId =
            Number(
                document.getElementById(
                    'paymentMerchantId'
                ).value
            );

        const customerId =
            Number(
                document.getElementById(
                    'paymentCustomerId'
                ).value
            );

        const currency =
            document.getElementById(
                'paymentCurrency'
            ).value;

        const amount =
            Number(
                document.getElementById(
                    'paymentAmount'
                ).value
            );

        const paymentReference =
            document.getElementById(
                'paymentReference'
            ).value.trim();

        const paymentMethod =
            document.getElementById(
                'paymentMethod'
            ).value;

        const status =
            document.getElementById(
                'paymentStatus'
            ).value;

        const amountError =
            document.getElementById(
                'paymentAmountError'
            );

        amountError.textContent =
            '';

        const remaining =
            Number(
                invoiceState.paymentRemaining ||
                0
            );

        if (!amount || amount <= 0) {

            amountError.textContent =
                'Payment amount must be greater than zero.';

            return;
        }

        if (
            amount >
            remaining + 0.001
        ) {

            amountError.textContent =
                'Payment amount cannot exceed the remaining invoice amount.';

            return;
        }

        if (!paymentReference) {

            showToast(
                'Payment reference is required.',
                'error'
            );

            return;
        }

        if (!paymentMethod) {

            showToast(
                'Please select a payment method.',
                'error'
            );

            return;
        }

        const button =
            document.getElementById(
                'paymentSubmitButton'
            );

        button.disabled =
            true;

        button.textContent =
            'Processing...';

        try {

            const payload = {
                merchant_id:
                    merchantId,

                customer_id:
                    customerId,

                invoice_id:
                    invoiceId,

                payment_reference:
                    paymentReference,

                amount:
                    amount,

                currency:
                    currency,

                payment_method:
                    paymentMethod,

                status:
                    status
            };

            const result =
                await request(
                    '/payments',
                    {
                        method: 'POST',
                        body: JSON.stringify(
                            payload
                        )
                    }
                );

            showToast(
                result?.message ||
                'Payment created successfully.'
            );

            closeModal(
                'paymentModal'
            );

            await loadInvoices(
                invoiceState.page
            );

        } catch (error) {

            console.error(error);

            showToast(
                error.message,
                'error'
            );

        } finally {

            button.disabled =
                false;

            button.textContent =
                'Create Payment';
        }
    }
);


async function issueInvoice(
    id
) {

    if (
        !confirm(
            'Are you sure you want to issue this invoice?'
        )
    ) {
        return;
    }

    try {

        const result =
            await request(
                `/invoices/${id}/issue`,
                {
                    method: 'POST',
                    body: JSON.stringify({})
                }
            );

        showToast(
            result?.message ||
            'Invoice issued successfully.'
        );

        loadInvoices(
            invoiceState.page
        );

    } catch (error) {

        showToast(
            error.message,
            'error'
        );
    }
}


async function voidInvoice(
    id
) {

    if (
        !confirm(
            'Are you sure you want to void this invoice?'
        )
    ) {
        return;
    }

    try {

        const result =
            await request(
                `/invoices/${id}/void`,
                {
                    method: 'POST',
                    body: JSON.stringify({})
                }
            );

        showToast(
            result?.message ||
            'Invoice voided successfully.'
        );

        loadInvoices(
            invoiceState.page
        );

    } catch (error) {

        showToast(
            error.message,
            'error'
        );
    }
}


let searchTimer =
    null;


document.getElementById(
    'searchInput'
).addEventListener(
    'input',
    function() {

        clearTimeout(
            searchTimer
        );

        searchTimer =
            setTimeout(
                () => loadInvoices(1),
                400
            );
    }
);


document.getElementById(
    'merchantFilter'
).addEventListener(
    'change',
    function() {

        document.getElementById(
            'customerFilter'
        ).value =
            '';

        loadInvoices(1);
    }
);


document.getElementById(
    'customerFilter'
).addEventListener(
    'change',
    function() {

        if (this.value) {

            document.getElementById(
                'merchantFilter'
            ).value =
                '';
        }

        loadInvoices(1);
    }
);


document.getElementById(
    'statusFilter'
).addEventListener(
    'change',
    function() {
        loadInvoices(1);
    }
);


document.getElementById(
    'perPage'
).addEventListener(
    'change',
    function() {
        loadInvoices(1);
    }
);


document.querySelectorAll(
    '.modal-backdrop'
).forEach(
    modal => {

        modal.addEventListener(
            'click',
            function(event) {

                if (
                    event.target ===
                    modal
                ) {
                    modal.classList.remove(
                        'show'
                    );
                }

            }
        );

    }
);


document.addEventListener(
    'DOMContentLoaded',
    async function() {

        await loadMerchants();

        await loadCustomers();

        await loadPeriods();

        loadInvoices(1);
    }
);

</script>

@endsection