<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PaymentGatewayCredentials
 *
 * @property int $id
 * @property string|null $paypal_client_id
 * @property string|null $paypal_secret
 * @property string $paypal_status
 * @property string|null $stripe_client_id
 * @property string|null $stripe_secret
 * @property string|null $stripe_webhook_secret
 * @property string $stripe_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $razorpay_key
 * @property string|null $razorpay_secret
 * @property string $razorpay_status
 * @property string $paypal_mode
 * @property string|null $sandbox_paypal_client_id
 * @property string|null $sandbox_paypal_secret
 * @property-read mixed $icon
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials wherePaypalClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials wherePaypalMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials wherePaypalSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials wherePaypalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereRazorpayKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereRazorpaySecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereRazorpayStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereSandboxPaypalClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereSandboxPaypalSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereStripeClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereStripeSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereStripeStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereStripeWebhookSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentGatewayCredentials whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaymentGatewayCredentials extends BaseModel
{
    //
}
