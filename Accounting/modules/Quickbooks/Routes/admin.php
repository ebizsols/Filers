<?php

Route::admin('quickbooks', function () {
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', 'Settings@edit')->name('edit');
        Route::post('/', 'Settings@update')->name('update');
    });

    Route::group(['prefix' => 'sync', 'as' => 'sync.'], function () {
        Route::group([
            'middleware' => [
                'Modules\Quickbooks\Http\Middleware\QuickbooksEnabled',
                'Modules\Quickbooks\Http\Middleware\QuickbooksRefreshToken',
            ],
        ], function () {
            Route::get('coa', 'Sync@coa')->name('coa');
            Route::get('taxes', 'Sync@taxes')->name('taxes');
            Route::get('products', 'Sync@products')->name('products');
            Route::get('customers', 'Sync@customers')->name('customers');
            Route::get('vendors', 'Sync@vendors')->name('vendors');
            Route::get('invoices', 'Sync@invoices')->name('invoices');
            Route::get('bills', 'Sync@bills')->name('bills');

            Route::get('coa/count', 'Coa@count')->name('coa.count');
            Route::get('coa/sync/{page}', 'Coa@sync')->name('coa.sync');
            Route::post('coa', 'Coa@store')->name('coa.store');

            Route::get('taxes/count', 'Taxes@count')->name('taxes.count');
            Route::get('taxes/sync/{page}', 'Taxes@sync')->name('taxes.sync');
            Route::post('taxes', 'Taxes@store')->name('taxes.store');

            Route::get('products/count', 'Items@count')->name('products.count');
            Route::get('products/sync/{page}', 'Items@sync')->name('products.sync');
            Route::post('products', 'Items@store')->name('products.store');

            Route::get('customers/count', 'Customers@count')->name('customers.count');
            Route::get('customers/sync/{page}', 'Customers@sync')->name('customers.sync');
            Route::post('customers', 'Customers@store')->name('customers.store');

            Route::get('vendors/count', 'Vendors@count')->name('vendors.count');
            Route::get('vendors/sync/{page}', 'Vendors@sync')->name('vendors.sync');
            Route::post('vendors', 'Vendors@store')->name('vendors.store');

            Route::get('invoices/count', 'Invoices@count')->name('invoices.count');
            Route::get('invoices/sync/{page}', 'Invoices@sync')->name('invoices.sync');
            Route::post('invoices', 'Invoices@store')->name('invoices.store');

            Route::get('bills/count', 'Bills@count')->name('bills.count');
            Route::get('bills/sync/{page}', 'Bills@sync')->name('bills.sync');
            Route::post('bills', 'Bills@store')->name('bills.store');
        });
    });
});

Route::group([
    'middleware' => 'web',
    'namespace' => 'Modules\Quickbooks\Http\Controllers',
    'prefix' => 'quickbooks/auth',
    'as' => 'quickbooks.auth.'
], function () {
    Route::get('/', 'Auth@OAuth')->name('start');
});
