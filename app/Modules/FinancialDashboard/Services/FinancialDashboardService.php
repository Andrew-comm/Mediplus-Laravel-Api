<?php

namespace App\Modules\FinancialDashboard\Services;

use App\Modules\Payments\Models\Payment;
use App\Modules\Sales\Models\Sale;
use App\Modules\Sales\Models\SaleItem;

class FinancialDashboardService
{
    /**
     * Get all financial dashboard information.
     */
    public function getDashboardData(): array
    {
        return [
            'summary' => $this->getSummary(),

            'sales_status' => $this->getSalesStatus(),

            'recent_sales' => $this->getRecentSales(),

            'recent_payments' => $this->getRecentPayments(),

            'payment_methods' => $this->getPaymentMethods(),

            'top_medicines' => $this->getTopMedicines(),

            'daily_sales' => $this->getDailySales(),
        ];
    }


    /**
     * Main financial summary.
     */
    private function getSummary(): array
    {
        /*
         * Total value of all sales.
         */
        $totalRevenue = (float) Sale::sum('total_amount');


        /*
         * Actual money received from payments.
         */
        $paymentsReceived = (float) Payment::sum('amount');


        /*
         * Outstanding amount:
         *
         * Total Sales - Payments Received
         */
        $outstandingBalance =
            max(
                0,
                $totalRevenue - $paymentsReceived
            );


        $totalSales = Sale::count();


        $averageSale = $totalSales > 0
            ? $totalRevenue / $totalSales
            : 0;


        /*
         * Today's sales.
         */
        $todaySales = Sale::query()
            ->whereDate(
                'created_at',
                today()
            )
            ->sum('total_amount');


        /*
         * Gross profit.
         */
        $grossProfit = SaleItem::query()
            ->join(
                'medicines',
                'sale_items.medicine_id',
                '=',
                'medicines.id'
            )
            ->selectRaw(
                '
                SUM(
                    (
                        sale_items.price
                        -
                        medicines.buying_price
                    )
                    *
                    sale_items.quantity
                ) AS profit
                '
            )
            ->value('profit');


        $grossProfit = $grossProfit ?? 0;


        return [

            'today_sales' =>
                round(
                    (float) $todaySales,
                    2
                ),

            'total_revenue' =>
                round(
                    $totalRevenue,
                    2
                ),

            'payments_received' =>
                round(
                    $paymentsReceived,
                    2
                ),

            'outstanding_balance' =>
                round(
                    $outstandingBalance,
                    2
                ),

            'gross_profit' =>
                round(
                    (float) $grossProfit,
                    2
                ),

            'average_sale' =>
                round(
                    $averageSale,
                    2
                ),

            'total_sales' =>
                $totalSales,
        ];
    }


    /**
     * Sales payment status counts.
     */
    private function getSalesStatus(): array
    {
        return [

            'paid' =>
                Sale::where(
                    'payment_status',
                    'paid'
                )->count(),

            'partial' =>
                Sale::where(
                    'payment_status',
                    'partial'
                )->count(),

            'pending' =>
                Sale::where(
                    'payment_status',
                    'pending'
                )->count(),

        ];
    }


    /**
     * Get sales filtered by payment status.
     *
     * This method is used when the user clicks:
     *
     * Paid
     * Partial
     * Pending
     */
    public function getSalesByStatus(
        string $status,
        int $perPage = 20
    ) {
        $sales = Sale::with([
            'customer',
            'payments',
        ])
            ->where(
                'payment_status',
                $status
            )
            ->latest()
            ->paginate($perPage);


        /*
         * Convert the paginator items into
         * dashboard-friendly data.
         *
         * We deliberately use items()
         * instead of getCollection()
         * to avoid the Intelephense issue.
         */
        $data = collect(
            $sales->items()
        )
            ->map(function ($sale) {

                /*
                 * Calculate actual amount paid
                 * from the payments belonging
                 * to this sale.
                 */
                $paidAmount = $sale->payments->sum(
                    'amount'
                );


                /*
                 * Outstanding amount.
                 */
                $balance = max(
                    0,
                    (float) $sale->total_amount
                    -
                    (float) $paidAmount
                );


                return [

                    'id' =>
                        $sale->id,

                    'invoice_number' =>
                        $sale->invoice_number,

                    'customer' =>
                        $sale->customer?->name,

                    'total_amount' =>
                        round(
                            (float) $sale->total_amount,
                            2
                        ),

                    'paid_amount' =>
                        round(
                            (float) $paidAmount,
                            2
                        ),

                    'balance' =>
                        round(
                            (float) $balance,
                            2
                        ),

                    'payment_status' =>
                        $sale->payment_status,

                    'created_at' =>
                        $sale->created_at,

                ];

            })
            ->values();


        /*
         * Return both the data and pagination.
         */
        return [

            'data' =>
                $data,

            'status' =>
                $status,

            'count' =>
                $sales->total(),

            'pagination' => [

                'current_page' =>
                    $sales->currentPage(),

                'last_page' =>
                    $sales->lastPage(),

                'per_page' =>
                    $sales->perPage(),

                'total' =>
                    $sales->total(),

            ],
        ];
    }


    /**
     * Latest sales.
     */
    private function getRecentSales()
    {
        return Sale::with([
            'customer',
            'payments',
        ])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($sale) {

                $paidAmount =
                    $sale->payments->sum(
                        'amount'
                    );


                $balance = max(
                    0,
                    (float) $sale->total_amount
                    -
                    (float) $paidAmount
                );


                return [

                    'id' =>
                        $sale->id,

                    'invoice_number' =>
                        $sale->invoice_number,

                    'customer' =>
                        $sale->customer?->name,

                    'total_amount' =>
                        round(
                            (float) $sale->total_amount,
                            2
                        ),

                    'paid_amount' =>
                        round(
                            (float) $paidAmount,
                            2
                        ),

                    'balance' =>
                        round(
                            (float) $balance,
                            2
                        ),

                    'payment_status' =>
                        $sale->payment_status,

                    'created_at' =>
                        $sale->created_at,

                ];

            })
            ->values();
    }


    /**
     * Latest payments.
     */
    private function getRecentPayments()
    {
        return Payment::with([
            'sale.customer',
        ])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($payment) {

                return [

                    'id' =>
                        $payment->id,

                    'invoice_number' =>
                        $payment->sale?->invoice_number,

                    'customer' =>
                        $payment->sale?->customer?->name,

                    'amount' =>
                        round(
                            (float) $payment->amount,
                            2
                        ),

                    'payment_method' =>
                        $payment->payment_method,

                    'reference_number' =>
                        $payment->reference_number,

                    'payment_date' =>
                        $payment->payment_date,

                ];

            })
            ->values();
    }


    /**
     * Revenue collected by payment method.
     */
    private function getPaymentMethods()
    {
        return Payment::query()
            ->selectRaw(
                'payment_method, SUM(amount) as total'
            )
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get()
            ->map(function ($payment) {

                return [

                    'payment_method' =>
                        $payment->payment_method,

                    'total' =>
                        round(
                            (float) $payment->total,
                            2
                        ),

                ];

            })
            ->values();
    }


    /**
     * Best-selling medicines.
     */
    private function getTopMedicines()
    {
        return SaleItem::query()
            ->join(
                'medicines',
                'sale_items.medicine_id',
                '=',
                'medicines.id'
            )
            ->selectRaw(
                '
                medicines.id,
                medicines.name,
                SUM(sale_items.quantity) AS units_sold,
                SUM(sale_items.subtotal) AS revenue
                '
            )
            ->groupBy(
                'medicines.id',
                'medicines.name'
            )
            ->orderByDesc('units_sold')
            ->limit(10)
            ->get()
            ->map(function ($medicine) {

                return [

                    'id' =>
                        $medicine->id,

                    'name' =>
                        $medicine->name,

                    'units_sold' =>
                        (int) $medicine->units_sold,

                    'revenue' =>
                        round(
                            (float) $medicine->revenue,
                            2
                        ),

                ];

            })
            ->values();
    }


    /**
     * Sales for the last 7 days.
     */
    private function getDailySales()
    {
        return Sale::query()
            ->where(
                'created_at',
                '>=',
                now()
                    ->subDays(6)
                    ->startOfDay()
            )
            ->selectRaw(
                '
                DATE(created_at) as date,
                SUM(total_amount) as total
                '
            )
            ->groupByRaw(
                'DATE(created_at)'
            )
            ->orderBy('date')
            ->get()
            ->map(function ($sale) {

                return [

                    'date' =>
                        $sale->date,

                    'total' =>
                        round(
                            (float) $sale->total,
                            2
                        ),

                ];

            })
            ->values();
    }
}
