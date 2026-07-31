<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [

            'cards' => [

                'totalMedicines' => $this['totalMedicines'],

                'lowStock' => $this['lowStock'],

                'expiredMedicines' => $this['expiredMedicines'],

                'expiringSoon' => $this['expiringSoon'],

            ],

            'recentMedicines' => $this['recentMedicines'],

        ];
    }
}
