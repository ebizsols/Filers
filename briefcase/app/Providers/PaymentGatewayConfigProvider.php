<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class PaymentGatewayConfigProvider extends ServiceProvider
{

    public function register()
    {
        try {
            $paymentGatewayCredentials = DB::table('payment_gateway_credentials')->first();

            if ($paymentGatewayCredentials) {

                if (!in_array(config('app.env'), ['demo', 'development'])) {

                    // Paystack
                    $key       = ($paymentGatewayCredentials->paystack_key) ? $paymentGatewayCredentials->paystack_key : env('PAYSTACK_PUBLIC_KEY');
                    $apiSecret = ($paymentGatewayCredentials->paystack_secret) ? $paymentGatewayCredentials->paystack_secret : env('PAYSTACK_SECRET_KEY');
                    $email = ($paymentGatewayCredentials->paystack_merchant_email) ? $paymentGatewayCredentials->paystack_merchant_email : env('MERCHANT_EMAIL');
                    $url = ($paymentGatewayCredentials->paystack_payment_url) ? $paymentGatewayCredentials->paystack_payment_url : env('PAYSTACK_PAYMENT_URL');

                    Config::set('paystack.publicKey', $key);
                    Config::set('paystack.secretKey', $apiSecret);
                    Config::set('paystack.paymentUrl', $url);
                    Config::set('paystack.merchantEmail', $email);


                    // Mollie
                    $mollie_api_key = ($paymentGatewayCredentials->mollie_api_key) ? $paymentGatewayCredentials->mollie_api_key : env('MOLLIE_KEY');
                    Config::set('mollie.key', $mollie_api_key);

                    // Payfast
                    $payfast_merchant_id = ($paymentGatewayCredentials->payfast_merchant_id) ? $paymentGatewayCredentials->payfast_merchant_id : env('PF_MERCHANT_ID');
                    $payfast_merchant_key = ($paymentGatewayCredentials->payfast_merchant_key) ? $paymentGatewayCredentials->payfast_merchant_key : env('PF_MERCHANT_KEY');
                    $payfast_passphrase = ($paymentGatewayCredentials->payfast_passphrase) ? $paymentGatewayCredentials->payfast_passphrase : env('PAYFAST_PASSPHRASE');

                    $payfast_mode = ($paymentGatewayCredentials->payfast_mode == 'sandbox');

                    Config::set('payfast.merchant.merchant_id', $payfast_merchant_id);
                    Config::set('payfast.merchant.merchant_key', $payfast_merchant_key);
                    Config::set('payfast.passphrase', $payfast_passphrase);
                    Config::set('payfast.testing', $payfast_mode);

                    // Authorize.net
                    $authorize_api_login_id = ($paymentGatewayCredentials->authorize_api_login_id) ? $paymentGatewayCredentials->authorize_api_login_id : env('AUTHORIZE_PAYMENT_API_LOGIN_ID');
                    $authorize_transaction_key = ($paymentGatewayCredentials->authorize_transaction_key) ? $paymentGatewayCredentials->authorize_transaction_key : env('AUTHORIZE_PAYMENT_TRANSACTION_KEY');

                    $authorize_environment = ($paymentGatewayCredentials->authorize_environment == 'sandbox');

                    Config::set('services.authorize.login', $authorize_api_login_id);
                    Config::set('services.authorize.transaction', $authorize_transaction_key);
                    Config::set('services.authorize.sandbox', $authorize_environment);

                    // square
                    $square_application_id = ($paymentGatewayCredentials->square_application_id) ? $paymentGatewayCredentials->square_application_id : env('SQUARE_APPLICATION_ID');
                    $square_access_token = ($paymentGatewayCredentials->square_access_token) ? $paymentGatewayCredentials->square_access_token : env('SQUARE_ACCESS_TOKEN');
                    $square_location_id = ($paymentGatewayCredentials->square_location_id) ? $paymentGatewayCredentials->square_location_id : env('SQUARE_LOCATION_ID');

                    $square_environment = $paymentGatewayCredentials->square_environment;

                    Config::set('services.square.application_id', $square_application_id);
                    Config::set('services.square.access_token', $square_access_token);
                    Config::set('services.square.location_id', $square_location_id);
                    Config::set('services.square.environment', $square_environment);

                }

            }
        } catch (\Exception $e) {

            Log::info($e);
        }

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

}
