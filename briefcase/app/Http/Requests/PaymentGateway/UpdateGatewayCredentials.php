<?php

namespace App\Http\Requests\PaymentGateway;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGatewayCredentials extends FormRequest
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

        $rules = [];

        if ($this->payment_method == 'paypal' && $this->paypal_status == 'active') {
            $rules = ['paypal_mode' => 'required|in:sandbox,live'];

            if($this->paypal_mode == 'sandbox') {
                $rules['sandbox_paypal_client_id'] = 'required';
                $rules['sandbox_paypal_secret'] = 'required';
            }
            else {
                $rules['paypal_client_id'] = 'required';
                $rules['paypal_secret'] = 'required';
            }
        }

        if ($this->payment_method == 'stripe' && $this->stripe_status == 'active') {
            $rules = ['stripe_mode' => 'required|in:test,live'];

            if($this->stripe_mode == 'test') {
                $rules['test_stripe_client_id'] = 'required';
                $rules['test_stripe_secret'] = 'required';
            }
            else {
                $rules['live_stripe_client_id'] = 'required';
                $rules['live_stripe_secret'] = 'required';
            }
        }

        if ($this->payment_method == 'razorpay' && $this->razorpay_status == 'active') {
            $rules = ['razorpay_mode' => 'required|in:test,live'];

            if($this->razorpay_mode == 'test') {
                $rules['test_razorpay_key'] = 'required';
                $rules['test_razorpay_secret'] = 'required';
            }
            else {
                $rules['live_razorpay_key'] = 'required';
                $rules['live_razorpay_secret'] = 'required';
            }
        }

        return $rules;
    }

}
