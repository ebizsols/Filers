<?php

namespace Modules\Quickbooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use App\Traits\Modules;
use Modules\DoubleEntry\Jobs\Account\CreateAccount;
use Modules\DoubleEntry\Jobs\Account\UpdateAccount;
use Modules\DoubleEntry\Models\Account;
use Modules\Quickbooks\Traits\QuickbooksRemote;
use Modules\Quickbooks\Traits\QuickbooksTransformer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;

class Coa extends Controller
{
    use Modules, QuickbooksRemote, QuickbooksTransformer;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-double-entry-chart-of-accounts')->only('create', 'store', 'duplicate', 'import');
        $this->middleware('permission:read-double-entry-chart-of-accounts')->only('index', 'show', 'edit', 'export');
        $this->middleware('permission:update-double-entry-chart-of-accounts')->only('update', 'enable', 'disable');
        $this->middleware('permission:delete-double-entry-chart-of-accounts')->only('destroy');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return JsonResponse
     */
    public function count()
    {
        $success = true;
        $error   = false;
        $message = null;
        $pages   = [];

        Cache::set('quickbooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getAccountsCount();

        if ($total>0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('quickbooks.sync.coa.sync', $i),
                ];
            }
        }

        Cache::set('quickbooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('quickbooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('quickbooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error   = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'coa';

        $html = view('quickbooks::partial.sync', compact('type', 'total'))->render();

        return response()->json([
            'success' => $success,
            'error'   => $error,
            'message' => $message,
            'count'   => $total,
            'pages'   => $pages,
            'html'    => $html,
        ]);
    }

    public function sync()
    {
        $steps = [];

        $accounts = $this->getAccounts();
        foreach ($accounts as $account) {
            $cached[$account->Id] = $account;

            $steps[] = [
                'text' => trans(
                    'quickbooks::general.sync_text',
                    [
                        'type'  => trans_choice('quickbooks::general.types.coa', 1),
                        'value' => $account->Name,
                    ]
                ),
                'url'  => route('quickbooks.sync.coa.store'),
                'id'   => $account->Id,
            ];
        }

        if (isset($cached)) {
            Cache::set('quickbooks_coa_' . company_id(), $cached, Date::now()->addHours(6));
        }

        return response()->json([
            'errors'  => false,
            'success' => true,
            'steps'   => $steps,
        ]);
    }

    /**
     * Enable the specified resource.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function store(Request $request)
    {
        $coa = Cache::get('quickbooks_coa_' . company_id());
        $coa_quickbooks = $coa[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $de_accounts = Account::all();

        $ak_account_data = $this->prepareCoaData($coa_quickbooks);

        $ak_account = $de_accounts->first(function ($account) use ($ak_account_data) {
            return $account->type_id == $ak_account_data['type_id'] && trans($account->name) == $ak_account_data['name'];
        });

        try {
            if (empty($ak_account)) {
                $ak_account = $this->dispatch(new CreateAccount($ak_account_data));
            } else {
                $ak_account = $this->dispatch(new UpdateAccount($ak_account, $ak_account_data));
            }
        } catch (\Exception $e) {
            report($e);
        }

        $syncCount = Cache::get('quickbooks_sync_count_' . company_id(), 0) + 1;

        Cache::set('quickbooks_sync_count_' . company_id(), $syncCount, Date::now()->addHours(6));

        $json = [
            'errors' => false,
            'success' => true,
            'finished' => false,
            'message' => ''
        ];

        if ($syncCount === Cache::get('quickbooks_sync_total_' . company_id(), 0)) {
            $json['finished'] = true;

            $json['message'] = trans('quickbooks::general.finished', [
                'type' => trans_choice('quickbooks::general.types.coa', 2)
            ]);

            setting()->set('quickbooks.last_check', $timestamp);
            setting()->save();

            Cache::set('quickbooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }
}
