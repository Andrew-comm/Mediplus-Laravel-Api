<?php

namespace App\Modules\FinancialDashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\FinancialDashboard\Resources\FinancialDashboardResource;
use App\Modules\FinancialDashboard\Services\FinancialDashboardService;

class FinancialDashboardController extends Controller
{
    public function __construct(
        protected FinancialDashboardService $financialDashboardService
    ) {
    }


    /**
     * Main financial dashboard.
     */
    public function index()
    {
        $data = $this->financialDashboardService
            ->getDashboardData();

        return new FinancialDashboardResource($data);
    }


    /**
     * Get sales by payment status.
     *
     * /api/financial-dashboard/sales/paid
     * /api/financial-dashboard/sales/partial
     * /api/financial-dashboard/sales/pending
     */
    public function salesByStatus(string $status)
    {
        /*
         * Only these three statuses
         * are allowed.
         */
        if (!in_array($status, [
            'paid',
            'partial',
            'pending',
        ])) {

            return response()->json([
                'message' =>
                    'Invalid payment status.',
            ], 422);
        }


        /*
         * Let the service handle
         * database querying and calculations.
         */
        $result =
            $this->financialDashboardService
                ->getSalesByStatus(
                    $status,
                    20
                );


        return response()->json(
            $result
        );
    }
}
