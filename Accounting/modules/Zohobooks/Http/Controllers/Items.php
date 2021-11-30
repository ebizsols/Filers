<?php

namespace Modules\Zohobooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use App\Traits\Modules;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Modules\DoubleEntry\Models\Account;
use Modules\Zohobooks\Models\Common\Item;
use Modules\Zohobooks\Traits\ZohoBooksRemote;
use Modules\Zohobooks\Traits\ZohoBooksTransformer;

class Items extends Controller
{
    use Modules, ZohoBooksRemote, ZohoBooksTransformer;

    /**
     * Instantiate a new controller instance.
     */
    public function __construct()
    {
        // Add CRUD permission check
        $this->middleware('permission:create-common-items')->only('create', 'store', 'duplicate', 'import');
        $this->middleware('permission:read-common-items')->only('index', 'show', 'edit', 'export');
        $this->middleware('permission:update-common-items')->only('update', 'enable', 'disable');
        $this->middleware('permission:delete-common-items')->only('destroy');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return JsonResponse
     */
    public function count()
    {
        $success = true;
        $error = false;
        $message = null;
        $pages = [];

        Cache::set('zohobooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getClient()->items->getTotal();

        if ($total > 0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('zohobooks.sync.products.sync', $i),
                ];
            }
        }

        Cache::set('zohobooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('zohobooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('zohobooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'products';

        $html = view('zohobooks::partial.sync', compact('type', 'total'))->render();

        return response()->json([
            'success' => $success,
            'error' => $error,
            'message' => $message,
            'count' => $total,
            'pages' => $pages,
            'html' => $html,
        ]);
    }

    public function sync()
    {
        $steps = [];

        $items = $this->getClient()->items->getList();

        foreach ($items as $item) {

            $cached[$item->item_id] = $this->prepareItemData($item);

            $steps[] = [
                'text' => trans(
                    'zohobooks::general.sync_text',
                    [
                        'type' => trans_choice('zohobooks::general.types.products', 1),
                        'value' => $item->item_name,
                    ]
                ),
                'url' => route('zohobooks.sync.products.store'),
                'id' => $item->item_id,
            ];
        }
        if (isset($cached)) {
            Cache::set('zohobooks_products_' . company_id(), $cached, Date::now()->addHours(6));
        }

        return response()->json([
            'errors' => false,
            'success' => true,
            'steps' => $steps,
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
        $products = Cache::get('zohobooks_products_' . company_id());

        $product = $products[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        $ak_item = Item::where('sku', $product['sku'])->first();

        if ($this->moduleIsEnabled('double-entry') && $product['purchase_account_id'] != "") {
            $de_expense_account_id = Account::where('code', 'ZOHOBOOK-' . $product['purchase_account_id'])->pluck('id')->first();

            $product['de_expense_account_id'] = $de_expense_account_id;
        }

        try {
            if (empty($ak_item)) {
                $ak_item = Item::create($product);
            } else {
                $ak_item = $ak_item->update($product);
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
                'type' => trans_choice('zohobooks::general.types.products', 2)
            ]);

            setting()->set('zohobooks.last_check', $timestamp);
            setting()->save();

            Cache::set('zohobooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }
}
