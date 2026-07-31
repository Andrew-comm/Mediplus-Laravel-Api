<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Models\Medicine;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function index()
    {
        return new DashboardResource([

            'totalMedicines' => Medicine::count(),

            'lowStock' => Medicine::where(
                'quantity',
                '<',
                20
            )->count(),

            'expiredMedicines' => Medicine::where(
                'expiry_date',
                '<',
                now()
            )->count(),

            'expiringSoon' => Medicine::whereBetween(
                'expiry_date',
                [
                    now(),
                    now()->addDays(30)
                ]
            )->count(),

            'recentMedicines' => Medicine::latest()
                ->take(5)
                ->get()

        ]);


    }


}
