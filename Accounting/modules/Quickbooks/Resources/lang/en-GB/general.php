<?php

return [

    'name'          => 'QuickBooks',
    'description'   => 'Migrate your data from QuickBooks to Akaunting.',
    'double_entry'  => 'By default, Akaunting ships with single-entry accounting features. You can get the <a href=":url">Double-Entry</a> app in order to have Chart of Accounts, Journal Entry, General Ledger, Balance Sheet, and Trial Balance features.',

    'form' => [
        'client_id'         => 'Client ID',
        'client_secret'     => 'Client Secret',
        'environment'       => 'Environment',
        'development'       => 'Development',
        'production'        => 'Production',

        'sync' => [
            'title'             => 'Sync',
            'auth'              => 'Auth',
            'coa'               => 'Chart of Accounts',
            'taxes'             => 'Sales Taxes',
            'products'          => 'Products & Services',
            'customers'          => 'Customers',
            'vendors'           => 'Vendors',
            'invoices'          => 'Invoices',
            'bills'             => 'Bills',
            'revenues'          => 'Revenues',
        ],
    ],

    'auth_failed'       => 'Authentication failed due to the following reason: <br/>:error',
    'auth_success'      => 'Successfully connected to QuickBooks',
    'finished'          => 'Sync completed for :type.',

    'types' => [
        'all'               => 'All',
        'coa'               => 'Chart of Account|Chart of Accounts',
        'taxes'             => 'Sales Tax|Sales Taxes',
        'products'          => 'Product & Service|Products & Services',
        'customers'         => 'Customer|Customers',
        'vendors'           => 'Vendor|Vendors',
        'invoices'          => 'Invoice|Invoices',
        'bills'             => 'Bill|Bills',
        'revenues'          => 'Revenue|Revenues',
    ],

    'sync_text' => 'Sync this :type: :value',
    'total'     => 'Total :type count: :count',
];
