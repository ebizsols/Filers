<?php

namespace App\Models;

use App\Observers\InvoicePaymentReceivedObserver;

/**
 * App\Models\ClientPayment
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $invoice_id
 * @property float $amount
 * @property string|null $gateway
 * @property string|null $transaction_id
 * @property int|null $currency_id
 * @property string|null $plan_id
 * @property string|null $customer_id
 * @property string|null $event_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $paid_on
 * @property string|null $remarks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $offline_method_id
 * @property string|null $bill
 * @property int|null $added_by
 * @property int|null $last_updated_by
 * @property-read mixed $icon
 * @property-read \App\Models\Invoice|null $invoice
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereAddedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereBill($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereGateway($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereLastUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereOfflineMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment wherePaidOn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $order_id
 * @property string|null $payment_gateway_response null = success
 * @property-read \App\Models\Order|null $order
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ClientPayment wherePaymentGatewayResponse($value)
 */
class ClientPayment extends BaseModel
{
    protected $table = 'payments';

    protected $dates = ['paid_on'];

    protected static function boot()
    {
        parent::boot();
        static::observe(InvoicePaymentReceivedObserver::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

}
