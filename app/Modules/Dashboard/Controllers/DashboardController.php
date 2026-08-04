<?php

namespace App\Modules\Dashboard\Controllers;
use App\Http\Controllers\Controller;
use App\Modules\Dashboard\Resources\DashboardResource;
use App\Modules\Medicines\Models\Medicine;
use App\Modules\Suppliers\Models\Supplier;


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


            'suppliers' => Supplier::count(),


            'recentMedicines' => Medicine::with('supplierData')
                ->latest()
                ->take(5)
                ->get()

        ]);

    }

}
