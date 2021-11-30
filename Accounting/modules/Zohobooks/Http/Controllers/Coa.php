<?php

namespace Modules\Zohobooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use App\Traits\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Modules\DoubleEntry\Jobs\Account\CreateAccount;
use Modules\DoubleEntry\Jobs\Account\UpdateAccount;
use Modules\DoubleEntry\Models\Account;
use Modules\Zohobooks\Traits\ZohoBooksRemote;
use Modules\Zohobooks\Traits\ZohoBooksTransformer;

class Coa extends Controller
{
    use Modules, ZohoBooksRemote, ZohoBooksTransformer;

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

        Cache::set('zohobooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getClient()->chartofaccounts->getTotal();

        if ($total>0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('zohobooks.sync.coa.sync', $i),
                ];
            }
        }

        Cache::set('zohobooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('zohobooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('zohobooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error   = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'coa';

        $html = view('zohobooks::partial.sync', compact('type', 'total'))->render();

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

        $accounts = $this->getClient()->chartofaccounts->getList();
        foreach ($accounts as $account) {
            $cached[$account->account_id] = $this->prepareCoaData($account);

            $steps[] = [
                'text' => trans(
                    'zohobooks::general.sync_text',
                    [
                        'type'  => trans_choice('zohobooks::general.types.coa', 1),
                        'value' => $account->account_name,
                    ]
                ),
                'url'  => route('zohobooks.sync.coa.store'),
                'id'   => $account->account_id,
            ];
        }

        if (isset($cached)) {
            Cache::set('zohobooks_coa_' . company_id(), $cached, Date::now()->addHours(6));
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
        $coa = Cache::get('zohobooks_coa_' . company_id());
        $ak_account_data = $coa[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $de_accounts = Account::all();

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

        $syncCount = Cache::get('zohobooks_sync_count_' . company_id(), 0) + 1;

        Cache::set('zohobooks_sync_count_' . company_id(), $syncCount, Date::now()->addHours(6));

        $json = [
            'errors' => false,
            'success' => true,
            'finished' => false,
            'message' => ''
        ];

        if ($syncCount === Cache::get('zohobooks_sync_total_' . company_id(), 0)) {
            $json['finished'] = true;

            $json['message'] = trans('zohobooks::general.finished', [
                'type' => trans_choice('zohobooks::general.types.coa', 2)
            ]);

            setting()->set('zohobooks.last_check', $timestamp);
            setting()->save();

            Cache::set('zohobooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }
}
