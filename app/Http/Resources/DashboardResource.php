<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class DashboardResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [

            'cards' => [

                'totalMedicines' => $this['totalMedicines'],

                'lowStock' => $this['lowStock'],

                'expiredMedicines' => $this['expiredMedicines'],

                'expiringSoon' => $this['expiringSoon'],

                'suppliers' => $this['suppliers'],

            ],


            'recentMedicines' => MedicineResource::collection(
                $this['recentMedicines']
            ),

        ];

    }
}
