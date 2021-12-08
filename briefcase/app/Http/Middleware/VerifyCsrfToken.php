<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/verify_ipn',
        '/verify_webhook',
        '/*-webhook',
        '/lead-form/leadStore',
        '/lead-form/ticket-store',
        '/paystack_webhook',
        '/mollie_webhook',
        '/payfast_webhook',
    ];
}
