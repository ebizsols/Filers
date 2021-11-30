<?php

Route::admin('zohobooks', function () {
    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', 'Settings@edit')->name('edit');
        Route::post('/', 'Settings@update')->name('update');
    });

    Route::group(['prefix' => 'sync', 'as' => 'sync.'], function () {
        Route::group([
            'middleware' => [
                'Modules\Zohobooks\Http\Middleware\ZohoBooksEnabled',
            ],
        ], function () {
            Route::get('coa', 'Sync@coa')->name('coa');
            Route::get('taxes', 'Sync@taxes')->name('taxes');
            Route::get('products', 'Sync@products')->name('products');
            Route::get('customers', 'Sync@customers')->name('customers');
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
    'namespace' => 'Modules\Zohobooks\Http\Controllers',
    'prefix' => 'zohobooks/auth',
    'as' => 'zohobooks.auth.'
], function () {
    Route::get('/', 'Auth@OAuth')->name('start');
});
