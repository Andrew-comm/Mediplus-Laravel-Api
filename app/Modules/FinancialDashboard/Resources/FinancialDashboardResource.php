<?php

namespace App\Modules\FinancialDashboard\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinancialDashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'summary' => $this['summary'],

            'sales_status' =>
                $this['sales_status'],

            'recent_sales' =>
                $this['recent_sales'],

            'recent_payments' =>
                $this['recent_payments'],

            'payment_methods' =>
                $this['payment_methods'],

            'top_medicines' =>
                $this['top_medicines'],

            'daily_sales' =>
                $this['daily_sales'],

        ];
    }
}
