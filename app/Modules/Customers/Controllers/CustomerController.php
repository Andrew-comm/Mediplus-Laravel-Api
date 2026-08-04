<?php

namespace App\Modules\Customers\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Customers\Models\Customer;
use App\Modules\Customers\Requests\StoreCustomerRequest;
use App\Modules\Customers\Requests\UpdateCustomerRequest;
use App\Modules\Customers\Resources\CustomerResource;


class CustomerController extends Controller
{


    public function index()
    {

        return CustomerResource::collection(

            Customer::latest()->get()

        );

    }





    public function store(
        StoreCustomerRequest $request
    )
    {


        $customer = Customer::create(

            $request->validated()

        );


        return new CustomerResource(

            $customer

        );

    }





    public function show(Customer $customer)
    {

        return new CustomerResource(

            $customer

        );

    }





    public function update(
        UpdateCustomerRequest $request,
        Customer $customer
    )
    {


        $customer->update(

            $request->validated()

        );


        return new CustomerResource(

            $customer

        );

    }





    public function destroy(Customer $customer)
    {

        $customer->delete();


        return response()->json([

            'message'=>
            'Customer deleted successfully'

        ]);

    }


}
