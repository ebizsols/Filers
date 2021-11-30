<?php

namespace App\Http\Requests\Payments;

use App\Http\Requests\CoreRequest;

class StorePayment extends CoreRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'amount' => 'required|numeric|min:1',
            'paid_on' => 'required'
        ];

        if ($this->transaction_id) {
            $rules = [
                'transaction_id' => 'unique:payments'
            ];
        }


        return $rules;
    }

    public function messages()
    {
        return $messges = [
            'invoice_id.required' => 'Select the invoice you want to add payment for.',
        ];
    }

}
