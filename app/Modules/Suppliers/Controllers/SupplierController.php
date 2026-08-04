<?php


namespace App\Modules\Suppliers\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Suppliers\Resources\SupplierResource;
use App\Modules\Suppliers\Models\Supplier;

use Illuminate\Http\Request;

class SupplierController extends Controller
{

    public function index()
    {

        return SupplierResource::collection(
            Supplier::all()
        );

    }


    public function store(Request $request)
    {

        $supplier = Supplier::create(
            $request->validate([

                'name'=>'required',

                'email'=>'nullable|email',

                'phone'=>'nullable',

                'address'=>'nullable'

            ])
        );


        return new SupplierResource($supplier);

    }


    public function show(Supplier $supplier)
    {

        return new SupplierResource($supplier);

    }


    public function update(Request $request,Supplier $supplier)
    {

        $supplier->update(

            $request->validate([

                'name'=>'required',

                'email'=>'nullable|email',

                'phone'=>'nullable',

                'address'=>'nullable'

            ])

        );


        return new SupplierResource($supplier);

    }


    public function destroy(Supplier $supplier)
    {

        $supplier->delete();


        return response()->json([

            'message'=>'Supplier deleted'

        ]);

    }

}
