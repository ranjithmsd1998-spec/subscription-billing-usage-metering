<?php

namespace App\Http\Controllers;

use App\Services\DailyUsageAggregateServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class DailyUsageAggregateWebController extends Controller
{
    public function __construct(
        private readonly DailyUsageAggregateServiceInterface $dailyUsageAggregateService
    ) {
    }

    public function index(): View
    {
        return view('daily-usage-aggregates.index');
    }

    public function list(Request $request): JsonResponse
    {
        $subscriptionPeriodId = $request->input(
            'subscription_period_id'
        );

        if (!$subscriptionPeriodId) {
            return response()->json([
                'success' => true,
                'message' => 'Please select a subscription period.',
                'data' => [
                    'items' => [],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                ],
            ]);
        }

        $perPage = (int) $request->input(
            'per_page',
            10
        );

        if (!in_array(
            $perPage,
            [10, 25, 50, 100],
            true
        )) {
            $perPage = 10;
        }

        $page = max(
            (int) $request->input('page', 1),
            1
        );

        $aggregates = $this->dailyUsageAggregateService
            ->getBySubscriptionPeriod(
                (int) $subscriptionPeriodId
            );

        $collection = collect($aggregates);

        /*
         * Search
         */
        $search = trim(
            (string) $request->input('search', '')
        );

        if ($search !== '') {

            $search = mb_strtolower($search);

            $collection = $collection
                ->filter(function ($aggregate) use ($search) {

                    $customerName = mb_strtolower(
                        (string) (
                            $aggregate->customer?->name
                            ?? ''
                        )
                    );

                    $customerCode = mb_strtolower(
                        (string) (
                            $aggregate->customer?->code
                            ?? ''
                        )
                    );

                    $usageDate = mb_strtolower(
                        (string) (
                            $aggregate->usage_date
                            ?? ''
                        )
                    );

                    $subscriptionId = (string) (
                        $aggregate->subscription_id
                        ?? ''
                    );

                    $periodId = (string) (
                        $aggregate->subscription_period_id
                        ?? ''
                    );

                    return str_contains(
                        $customerName,
                        $search
                    )
                    || str_contains(
                        $customerCode,
                        $search
                    )
                    || str_contains(
                        $usageDate,
                        $search
                    )
                    || str_contains(
                        $subscriptionId,
                        $search
                    )
                    || str_contains(
                        $periodId,
                        $search
                    );
                })
                ->values();
        }

        /*
         * Usage date filter
         */
        $usageDate = trim(
            (string) $request->input(
                'usage_date',
                ''
            )
        );

        if ($usageDate !== '') {

            $collection = $collection
                ->filter(function ($aggregate) use ($usageDate) {

                    return (string) $aggregate->usage_date
                        === $usageDate;
                })
                ->values();
        }

        /*
         * Sort latest usage date first.
         */
        $collection = $collection
            ->sortByDesc(
                'usage_date'
            )
            ->values();

        /*
         * Manual pagination
         */
        $total = $collection->count();

        $items = $collection
            ->slice(
                ($page - 1) * $perPage,
                $perPage
            )
            ->values();

        $paginator = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Daily usage aggregates retrieved successfully.',
            'data' => [
                'items' => $paginator->items(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $aggregate = $this->dailyUsageAggregateService
            ->findById($id);

        return response()->json([
            'success' => true,
            'message' => 'Daily usage aggregate retrieved successfully.',
            'data' => $aggregate,
        ]);
    }
}