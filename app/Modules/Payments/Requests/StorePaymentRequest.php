<?php

namespace App\Modules\Payments\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
       return [

        'sale_id'=>

        'required|exists:sales,id',

        'amount'=>

        'required|numeric|min:0.01',

        'payment_method'=>

        'required|in:cash,mpesa,bank',

        'payment_date'=>

        'required|date',

        'reference_number'=>

        'nullable|string|max:255',

        'notes'=>

        'nullable|string'

        ];
    }
}
