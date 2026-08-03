<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicineRequest extends FormRequest
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

            'name' => [
                'required',
                'string',
                'max:255'
            ],


            'category' => [
                'required',
                'string',
                'max:255'
            ],


            'batch_number' => [
                'required',
                'string',
                'max:255',
                'unique:medicines,batch_number,' . $this->medicine->id
            ],


            'expiry_date' => [
                'required',
                'date'
            ],


            'buying_price' => [
                'required',
                'numeric',
                'min:0'
            ],


            'selling_price' => [
                'required',
                'numeric',
                'min:0'
            ],


            'quantity' => [
                'required',
                'integer',
                'min:0'
            ],


           'supplier_id' => [
            'required',
            'exists:suppliers,id'
        ]

        ];
    }
}
