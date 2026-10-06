@extends('layouts.app')

@section('title', 'Plan Changes')
@section('page_heading', 'Plan Changes')

@section('content')

<style>
    .plan-change-page {
        padding: 24px;
    }

    .page-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        margin-bottom: 24px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .page-subtitle {
        color: #6b7280;
        margin-bottom: 24px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-weight: 600;
        margin-bottom: 7px;
        color: #374151;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        font-size: 14px;
        box-sizing: border-box;
        background: #fff;
    }

    .form-control:focus {
        outline: none;
        border-color: #6366f1;
        box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.12);
    }

    .form-control[readonly] {
        background: #f3f4f6;
    }

    .subscription-info {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-top: 18px;
        display: none;
    }

    .subscription-info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .info-label {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .info-value {
        font-weight: 600;
        color: #111827;
    }

    .button-row {
        margin-top: 22px;
        display: flex;
        gap: 10px;
    }

    .btn {
        border: none;
        border-radius: 7px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary {
        background: #4f46e5;
        color: #fff;
    }

    .btn-primary:hover {
        background: #4338ca;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }

    .btn-secondary:hover {
        background: #d1d5db;
    }

    .message {
        display: none;
        padding: 12px 15px;
        border-radius: 7px;
        margin-bottom: 20px;
        font-size: 14px;
    }

    .message.success {
        display: block;
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .message.error {
        display: block;
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        padding: 12px 10px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        font-size: 14px;
    }

    .data-table th {
        background: #f9fafb;
        font-weight: 700;
        color: #374151;
    }

    .badge {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-upgrade {
        background: #dcfce7;
        color: #166534;
    }

    .badge-downgrade {
        background: #fef3c7;
        color: #92400e;
    }

    .empty-state {
        text-align: center;
        padding: 25px;
        color: #6b7280;
    }

    .required {
        color: #dc2626;
    }

    @media (max-width: 900px) {
        .form-grid,
        .subscription-info-grid {
            grid-template-columns: 1fr;
        }

        .form-group.full-width {
            grid-column: auto;
        }
    }
</style>

<div class="plan-change-page">

    <div class="page-card">

        <div class="page-title">Change Subscription Plan</div>

        <div class="page-subtitle">
            Upgrade or downgrade an active subscription.
        </div>

        <div id="messageBox" class="message"></div>

        <div class="form-grid">

            {{-- Subscription --}}
            <div class="form-group full-width">

                <label class="form-label">
                    Subscription <span class="required">*</span>
                </label>

                <select
                    id="subscriptionId"
                    class="form-control"
                    onchange="onSubscriptionChange()"
                >
                    <option value="">Select Subscription</option>
                </select>

            </div>

            {{-- New Plan --}}
            <div class="form-group">

                <label class="form-label">
                    New Plan <span class="required">*</span>
                </label>

                <select
                    id="toPlanId"
                    class="form-control"
                    onchange="onNewPlanChange()"
                    disabled
                >
                    <option value="">Select New Plan</option>
                </select>

            </div>

            {{-- Change Type --}}
            <div class="form-group">

                <label class="form-label">
                    Change Type
                </label>

                <input
                    type="text"
                    id="changeType"
                    class="form-control"
                    readonly
                    placeholder="Upgrade / Downgrade"
                >

            </div>

            {{-- Effective At --}}
            <div class="form-group">

                <label class="form-label">
                    Effective At <span class="required">*</span>
                </label>

                <input
                    type="datetime-local"
                    id="effectiveAt"
                    class="form-control"
                >

            </div>

            {{-- Reason --}}
            <div class="form-group">

                <label class="form-label">
                    Reason
                </label>

                <input
                    type="text"
                    id="reason"
                    class="form-control"
                    maxlength="255"
                    placeholder="Optional reason"
                >

            </div>

        </div>

        {{-- Subscription Information --}}
        <div id="subscriptionInfo" class="subscription-info">

            <div class="subscription-info-grid">

                <div>
                    <div class="info-label">Customer</div>
                    <div class="info-value" id="customerName">-</div>
                </div>

                <div>
                    <div class="info-label">Current Plan</div>
                    <div class="info-value" id="currentPlanName">-</div>
                </div>

                <div>
                    <div class="info-label">Current Price</div>
                    <div class="info-value" id="currentPlanPrice">-</div>
                </div>

                <div>
                    <div class="info-label">Current Period</div>
                    <div class="info-value" id="currentPeriod">-</div>
                </div>

            </div>

        </div>

        <div class="button-row">

            <button
                type="button"
                class="btn btn-primary"
                onclick="createPlanChange()"
            >
                Change Plan
            </button>

            <button
                type="button"
                class="btn btn-secondary"
                onclick="resetForm()"
            >
                Reset
            </button>

        </div>

    </div>


    {{-- History --}}
    <div class="page-card">

        <div class="page-title" style="font-size: 20px;">
            Plan Change History
        </div>

        <div class="page-subtitle">
            Previous plan changes for the selected subscription.
        </div>

        <div class="table-wrapper">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>From Plan</th>
                        <th>To Plan</th>
                        <th>Change Type</th>
                        <th>Effective At</th>
                        <th>Reason</th>
                    </tr>
                </thead>

                <tbody id="historyTableBody">

                    <tr>
                        <td colspan="6" class="empty-state">
                            Select a subscription to view history.
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>


<script>

    let subscriptions = [];
    let plans = [];
    let selectedSubscription = null;
    let selectedNewPlan = null;


    /*
    |--------------------------------------------------------------------------
    | Initial Load
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', async function () {

        setDefaultEffectiveAt();

        await loadSubscriptions();

        await loadPlans();

    });


    /*
    |--------------------------------------------------------------------------
    | Message
    |--------------------------------------------------------------------------
    */

    function showMessage(message, type = 'success') {

        const box = document.getElementById('messageBox');

        box.className = 'message ' + type;

        box.textContent = message;

        box.style.display = 'block';

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });

    }


    function hideMessage() {

        const box = document.getElementById('messageBox');

        box.style.display = 'none';

        box.textContent = '';

    }


    /*
    |--------------------------------------------------------------------------
    | Load Subscriptions
    |--------------------------------------------------------------------------
    */

    async function loadSubscriptions() {

        try {

            const response = await fetch(
                '/subscriptions/data?per_page=100'
            );

            const result = await response.json();

            if (!response.ok || !result.success) {

                throw new Error(
                    result.message || 'Failed to load subscriptions.'
                );

            }

            subscriptions =
                result.data?.items ??
                result.data ??
                [];

            populateSubscriptionDropdown();

        } catch (error) {

            console.error(error);

            showMessage(
                error.message || 'Failed to load subscriptions.',
                'error'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Load Plans
    |--------------------------------------------------------------------------
    */

    async function loadPlans() {

        try {

            const response = await fetch(
                '/plans/data?per_page=100'
            );

            const result = await response.json();

            if (!response.ok || !result.success) {

                throw new Error(
                    result.message || 'Failed to load plans.'
                );

            }

            plans =
                result.data?.items ??
                result.data ??
                [];

        } catch (error) {

            console.error(error);

            showMessage(
                error.message || 'Failed to load plans.',
                'error'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Populate Subscription Dropdown
    |--------------------------------------------------------------------------
    */

    function populateSubscriptionDropdown() {

        const select =
            document.getElementById('subscriptionId');

        select.innerHTML =
            '<option value="">Select Subscription</option>';

        subscriptions.forEach(function (subscription) {

            const customerName =
                subscription.customer?.name ??
                subscription.customer_name ??
                ('Customer #' + subscription.customer_id);

            const planName =
                subscription.plan?.name ??
                subscription.plan_name ??
                ('Plan #' + subscription.plan_id);

            const option =
                document.createElement('option');

            option.value = subscription.id;

            option.textContent =
                '#' +
                subscription.id +
                ' - ' +
                customerName +
                ' - ' +
                planName;

            select.appendChild(option);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Subscription Changed
    |--------------------------------------------------------------------------
    */

    async function onSubscriptionChange() {

        hideMessage();

        const subscriptionId =
            Number(document.getElementById('subscriptionId').value);

        const planSelect =
            document.getElementById('toPlanId');

        document.getElementById('changeType').value = '';

        planSelect.innerHTML =
            '<option value="">Select New Plan</option>';

        planSelect.disabled = true;

        selectedSubscription = null;

        if (!subscriptionId) {

            document.getElementById(
                'subscriptionInfo'
            ).style.display = 'none';

            clearHistory();

            return;

        }

        selectedSubscription =
            subscriptions.find(function (subscription) {

                return Number(subscription.id) === subscriptionId;

            });

        if (!selectedSubscription) {

            showMessage(
                'Subscription not found.',
                'error'
            );

            return;

        }

        showSubscriptionInformation();

        populateNewPlanDropdown();

        await loadPlanChangeHistory(subscriptionId);

    }


    /*
    |--------------------------------------------------------------------------
    | Show Subscription Information
    |--------------------------------------------------------------------------
    */

    function showSubscriptionInformation() {

        const subscription =
            selectedSubscription;

        const customerName =
            subscription.customer?.name ??
            subscription.customer_name ??
            ('Customer #' + subscription.customer_id);

        const currentPlan =
            getCurrentPlan(subscription);

        const currentPlanName =
            currentPlan?.name ??
            subscription.plan?.name ??
            ('Plan #' + subscription.plan_id);

        const currentPrice =
            currentPlan?.base_price ??
            subscription.plan?.base_price ??
            '-';

        document.getElementById('customerName').textContent =
            customerName;

        document.getElementById('currentPlanName').textContent =
            currentPlanName;

        document.getElementById('currentPlanPrice').textContent =
            currentPrice !== '-'
                ? formatCurrency(currentPrice)
                : '-';

        document.getElementById('currentPeriod').textContent =
            formatDate(subscription.current_period_start) +
            ' - ' +
            formatDate(subscription.current_period_end);

        document.getElementById(
            'subscriptionInfo'
        ).style.display = 'block';

    }


    /*
    |--------------------------------------------------------------------------
    | Get Current Plan
    |--------------------------------------------------------------------------
    */

    function getCurrentPlan(subscription) {

        if (subscription.plan) {

            return subscription.plan;

        }

        return plans.find(function (plan) {

            return Number(plan.id) ===
                Number(subscription.plan_id);

        }) || null;

    }


    /*
    |--------------------------------------------------------------------------
    | Populate New Plan
    |--------------------------------------------------------------------------
    */

    function populateNewPlanDropdown() {

        const planSelect =
            document.getElementById('toPlanId');

        planSelect.innerHTML =
            '<option value="">Select New Plan</option>';

        const currentPlanId =
            Number(selectedSubscription.plan_id);

        const merchantId =
            Number(selectedSubscription.merchant_id);

        plans
            .filter(function (plan) {

                return Number(plan.id) !== currentPlanId &&
                    Number(plan.merchant_id) === merchantId &&
                    plan.status === 'active';

            })
            .forEach(function (plan) {

                const option =
                    document.createElement('option');

                option.value = plan.id;

                option.textContent =
                    plan.name +
                    ' - ' +
                    formatCurrency(plan.base_price);

                planSelect.appendChild(option);

            });

        planSelect.disabled = false;

    }


    /*
    |--------------------------------------------------------------------------
    | New Plan Changed
    |--------------------------------------------------------------------------
    */

    function onNewPlanChange() {

        hideMessage();

        const toPlanId =
            Number(document.getElementById('toPlanId').value);

        const changeTypeInput =
            document.getElementById('changeType');

        changeTypeInput.value = '';

        selectedNewPlan = null;

        if (!toPlanId || !selectedSubscription) {

            return;

        }

        const currentPlan =
            getCurrentPlan(selectedSubscription);

        const newPlan =
            plans.find(function (plan) {

                return Number(plan.id) === toPlanId;

            });

        if (!currentPlan || !newPlan) {

            return;

        }

        selectedNewPlan = newPlan;

        const currentPrice =
            Number(currentPlan.base_price);

        const newPrice =
            Number(newPlan.base_price);

        if (newPrice > currentPrice) {

            changeTypeInput.value = 'upgrade';

        } else if (newPrice < currentPrice) {

            changeTypeInput.value = 'downgrade';

        } else {

            changeTypeInput.value = '';

            showMessage(
                'The new plan must have a different price from the current plan.',
                'error'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Create Plan Change
    |--------------------------------------------------------------------------
    */

    async function createPlanChange() {

        hideMessage();

        if (!selectedSubscription) {

            showMessage(
                'Please select a subscription.',
                'error'
            );

            return;

        }

        const toPlanId =
            Number(document.getElementById('toPlanId').value);

        const effectiveAt =
            document.getElementById('effectiveAt').value;

        const reason =
            document.getElementById('reason').value.trim();

        if (!toPlanId) {

            showMessage(
                'Please select a new plan.',
                'error'
            );

            return;

        }

        if (!effectiveAt) {

            showMessage(
                'Please select an effective date and time.',
                'error'
            );

            return;

        }

        const currentPlan =
            getCurrentPlan(selectedSubscription);

        const newPlan =
            plans.find(function (plan) {

                return Number(plan.id) === toPlanId;

            });

        if (!currentPlan || !newPlan) {

            showMessage(
                'Current plan or new plan could not be found.',
                'error'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Calculate change_type before sending API request.
        |--------------------------------------------------------------------------
        */

        let changeType;

        const currentPrice =
            Number(currentPlan.base_price);

        const newPrice =
            Number(newPlan.base_price);

        if (newPrice > currentPrice) {

            changeType = 'upgrade';

        } else if (newPrice < currentPrice) {

            changeType = 'downgrade';

        } else {

            showMessage(
                'The new plan must have a different price from the current plan.',
                'error'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Effective Date Validation
        |--------------------------------------------------------------------------
        */

        const effectiveDate =
            new Date(effectiveAt);

        const periodStart =
            new Date(selectedSubscription.current_period_start);

        const periodEnd =
            new Date(selectedSubscription.current_period_end);

        if (effectiveDate < periodStart) {

            showMessage(
                'Effective date cannot be before the current subscription period start.',
                'error'
            );

            return;

        }

        if (effectiveDate > periodEnd) {

            showMessage(
                'Effective date cannot be after the current subscription period end.',
                'error'
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | FINAL PAYLOAD
        |--------------------------------------------------------------------------
        |
        | change_type is IMPORTANT.
        |
        */

        const payload = {

            subscription_id:
                Number(selectedSubscription.id),

            from_plan_id:
                Number(selectedSubscription.plan_id),

            to_plan_id:
                Number(toPlanId),

            change_type:
                changeType,

            effective_at:
                formatLaravelDateTime(effectiveDate),

            reason:
                reason || null

        };


        console.log(
            'Plan Change Payload:',
            payload
        );


        try {

            const response = await fetch(
                '/plan-changes',
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },

                    body: JSON.stringify(payload)
                }
            );

            const result =
                await response.json();

            if (!response.ok) {

                let errorMessage =
                    result.message ||
                    'Failed to create plan change.';

                if (result.errors) {

                    const firstError =
                        Object.values(result.errors)
                            .flat()[0];

                    if (firstError) {

                        errorMessage = firstError;

                    }

                }

                throw new Error(errorMessage);

            }

            if (result.success === false) {

                throw new Error(
                    result.message ||
                    'Failed to create plan change.'
                );

            }


            showMessage(
                result.message ||
                'Plan change created successfully.',
                'success'
            );


            /*
            |--------------------------------------------------------------------------
            | Reload data
            |--------------------------------------------------------------------------
            */

            await loadSubscriptions();

            await loadPlans();


            /*
            |--------------------------------------------------------------------------
            | Keep selected subscription
            |--------------------------------------------------------------------------
            */

            document.getElementById(
                'subscriptionId'
            ).value = selectedSubscription.id;


            selectedSubscription =
                subscriptions.find(function (subscription) {

                    return Number(subscription.id) ===
                        Number(selectedSubscription.id);

                }) || selectedSubscription;


            showSubscriptionInformation();

            populateNewPlanDropdown();


            document.getElementById(
                'toPlanId'
            ).value = '';

            document.getElementById(
                'changeType'
            ).value = '';

            document.getElementById(
                'reason'
            ).value = '';


            await loadPlanChangeHistory(
                selectedSubscription.id
            );


        } catch (error) {

            console.error(
                'Plan Change Error:',
                error
            );

            showMessage(
                error.message ||
                'Something went wrong while creating plan change.',
                'error'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Load Plan Change History
    |--------------------------------------------------------------------------
    */

    async function loadPlanChangeHistory(subscriptionId) {

        const tbody =
            document.getElementById('historyTableBody');

        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="empty-state">
                    Loading history...
                </td>
            </tr>
        `;

        try {

            const response = await fetch(
                '/plan-changes/subscription/' +
                subscriptionId +
                '/history',
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );

            const result =
                await response.json();

            if (!response.ok || !result.success) {

                throw new Error(
                    result.message ||
                    'Failed to load plan change history.'
                );

            }

            const history =
                result.data ?? [];

            renderHistory(history);

        } catch (error) {

            console.error(error);

            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-state">
                        Failed to load history.
                    </td>
                </tr>
            `;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Render History
    |--------------------------------------------------------------------------
    */

    function renderHistory(history) {

        const tbody =
            document.getElementById('historyTableBody');

        if (!history.length) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-state">
                        No plan changes found.
                    </td>
                </tr>
            `;

            return;

        }

        tbody.innerHTML = '';

        history.forEach(function (item) {

            const fromPlan =
                item.from_plan?.name ??
                item.from_plan_name ??
                '-';

            const toPlan =
                item.to_plan?.name ??
                item.to_plan_name ??
                '-';

            const changeType =
                item.change_type ??
                '-';

            const reason =
                item.reason ??
                '-';

            const row =
                document.createElement('tr');

            row.innerHTML = `

                <td>${item.id ?? '-'}</td>

                <td>${escapeHtml(fromPlan)}</td>

                <td>${escapeHtml(toPlan)}</td>

                <td>
                    <span class="badge ${
                        changeType === 'upgrade'
                            ? 'badge-upgrade'
                            : 'badge-downgrade'
                    }">
                        ${escapeHtml(changeType)}
                    </span>
                </td>

                <td>
                    ${formatDate(item.effective_at)}
                </td>

                <td>
                    ${escapeHtml(reason)}
                </td>

            `;

            tbody.appendChild(row);

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Reset
    |--------------------------------------------------------------------------
    */

    function resetForm() {

        hideMessage();

        document.getElementById(
            'subscriptionId'
        ).value = '';

        document.getElementById(
            'toPlanId'
        ).innerHTML =
            '<option value="">Select New Plan</option>';

        document.getElementById(
            'toPlanId'
        ).disabled = true;

        document.getElementById(
            'changeType'
        ).value = '';

        document.getElementById(
            'reason'
        ).value = '';

        selectedSubscription = null;

        selectedNewPlan = null;

        document.getElementById(
            'subscriptionInfo'
        ).style.display = 'none';

        clearHistory();

        setDefaultEffectiveAt();

    }


    /*
    |--------------------------------------------------------------------------
    | Clear History
    |--------------------------------------------------------------------------
    */

    function clearHistory() {

        document.getElementById(
            'historyTableBody'
        ).innerHTML = `
            <tr>
                <td colspan="6" class="empty-state">
                    Select a subscription to view history.
                </td>
            </tr>
        `;

    }


    /*
    |--------------------------------------------------------------------------
    | Default Effective Date
    |--------------------------------------------------------------------------
    */

    function setDefaultEffectiveAt() {

        const input =
            document.getElementById('effectiveAt');

        const now =
            new Date();

        const year =
            now.getFullYear();

        const month =
            String(now.getMonth() + 1).padStart(2, '0');

        const day =
            String(now.getDate()).padStart(2, '0');

        const hours =
            String(now.getHours()).padStart(2, '0');

        const minutes =
            String(now.getMinutes()).padStart(2, '0');

        input.value =
            `${year}-${month}-${day}T${hours}:${minutes}`;

    }


    /*
    |--------------------------------------------------------------------------
    | Laravel DateTime Format
    |--------------------------------------------------------------------------
    */

    function formatLaravelDateTime(date) {

        const year =
            date.getFullYear();

        const month =
            String(date.getMonth() + 1).padStart(2, '0');

        const day =
            String(date.getDate()).padStart(2, '0');

        const hours =
            String(date.getHours()).padStart(2, '0');

        const minutes =
            String(date.getMinutes()).padStart(2, '0');

        const seconds =
            String(date.getSeconds()).padStart(2, '0');

        return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;

    }


    /*
    |--------------------------------------------------------------------------
    | Format Date
    |--------------------------------------------------------------------------
    */

    function formatDate(value) {

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


    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    function formatCurrency(value) {

        const amount =
            Number(value);

        if (Number.isNaN(amount)) {

            return value;

        }

        return new Intl.NumberFormat(
            'en-IN',
            {
                style: 'currency',
                currency: 'INR',
                minimumFractionDigits: 2
            }
        ).format(amount);

    }


    /*
    |--------------------------------------------------------------------------
    | CSRF Token
    |--------------------------------------------------------------------------
    */

    function getCsrfToken() {

        const meta =
            document.querySelector(
                'meta[name="csrf-token"]'
            );

        return meta
            ? meta.getAttribute('content')
            : '';

    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }

</script>

@endsection