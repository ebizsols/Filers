<?php

return [

    'name'          => 'ZohoBooks',
    'description'   => 'Migrate your data from ZohoBooks to Akaunting.',
    'double_entry'  => 'By default, Akaunting ships with single-entry accounting features. You can get the <a href=":url">Double-Entry</a> app in order to have Chart of Accounts, Journal Entry, General Ledger, Balance Sheet, and Trial Balance features.',

    'form' => [
        'client_id'         => 'Client ID',
        'client_secret'     => 'Client Secret',
        'organization_id'       => 'Organization id',

        'sync' => [
            'title'             => 'Sync',
            'auth'              => 'Auth',
            'coa'               => 'Chart of Accounts',
            'taxes'             => 'Sales Taxes',
            'products'          => 'Products & Services',
            'customers'          => 'Customers & Vendors',
            'invoices'          => 'Invoices',
            'bills'             => 'Bills',
        ],
    ],

    'auth_failed'       => 'Authentication failed due to the following reason: <br/>:error',
    'auth_success'      => 'Successfully connected to ZohoBooks',
    'finished'          => 'Sync completed for :type.',

    'types' => [
        'all'               => 'All',
        'coa'               => 'Chart of Account|Chart of Accounts',
        'taxes'             => 'Sales Tax|Sales Taxes',
        'products'          => 'Product & Service|Products & Services',
        'customers'         => 'Customer & Vendor|Customers & Vendors',
        'invoices'          => 'Invoice|Invoices',
        'bills'             => 'Bill|Bills',
    ],

    'sync_text' => 'Sync this :type: :value',
    'total'     => 'Total :type count: :count',
];
