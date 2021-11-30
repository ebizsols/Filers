<?php

namespace Modules\Quickbooks\Http\Controllers;

use App\Abstracts\Http\Controller;
use Modules\DoubleEntry\Models\Account;
use App\Models\Setting\Category;
use App\Traits\Modules;
use Modules\Quickbooks\Models\Common\Item;
use Modules\Quickbooks\Traits\QuickbooksRemote;
use Modules\Quickbooks\Traits\QuickbooksTransformer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Http\Request;

class Items extends Controller
{
    use Modules, QuickbooksRemote, QuickbooksTransformer;

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

        Cache::set('quickbooks_sync_running_' . company_id(), true, Date::now()->addHours(6));

        $total = $this->getProductsCount();

        if ($total > 0) {
            for ($i = 1; $i <= $total; $i++) {
                $pages[] = [
                    'url' => route('quickbooks.sync.products.sync', $i),
                ];
            }
        }

        Cache::set('quickbooks_sync_total_' . company_id(), $total, Date::now()->addHours(6));
        Cache::set('quickbooks_sync_count_' . company_id(), 0, Date::now()->addHours(6));

        $message = trans('quickbooks::general.total', ['count' => $total]);

        if (empty($pages)) {
            $success = false;
            $error = true;
            $message = trans('magento::general.error.nothing_to_sync');
        }

        $type = 'products';

        $html = view('quickbooks::partial.sync', compact('type', 'total'))->render();

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

        $products = $this->getProducts();

        foreach ($products as $product) {
            $cached[$product->Id] = $product;

            $steps[] = [
                'text' => trans(
                    'quickbooks::general.sync_text',
                    [
                        'type' => trans_choice('quickbooks::general.types.products', 1),
                        'value' => $product->Name,
                    ]
                ),
                'url' => route('quickbooks.sync.products.store'),
                'id' => $product->Id,
            ];
        }

        if (isset($cached)) {
            Cache::set('quickbooks_products_' . company_id(), $cached, Date::now()->addHours(6));
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
        $products = Cache::get('quickbooks_products_' . company_id());

        $product = $products[$request['id']];

        $timestamp = Date::now()->toDateTimeString();

        if ($product->Type == 'Category') {
            $ak_category_data = $this->prepareCategoryData($product);

            $ak_category = Category::where('name', $product->Name)->first();

            try {
                if (empty($ak_category)) {
                    $ak_category = Category::create($ak_category_data);
                }
            } catch (\Exception $e) {
                report($e);
            }
        } else {
            $ak_category = null;

            if (!is_null($product->ParentRef)) {
                $parent = $this->getOne("Item", $product->ParentRef);
                $ak_category_data = $this->prepareCategoryData($parent);

                $ak_category = Category::where('name', $parent->Name)->first();

                if (empty($ak_category)) {
                    $ak_category = Category::create($ak_category_data);
                }
            }

            $ak_item_data = $this->prepareItemData($product, $ak_category);

            $ak_item = Item::where('sku', $product->Id)->first();

            if ($this->moduleIsEnabled('double-entry') && (isset($product->IncomeAccountRef) || isset($product->ExpenseAccountRef))) {
                if (isset($product->IncomeAccountRef) && !is_null($product->IncomeAccountRef)) {
                    $de_income_account_id = Account::where('code', 'QBO-' . $product->IncomeAccountRef)->pluck('id')->first();

                    $ak_item_data['de_income_account_id'] = $de_income_account_id;
                }

                if (isset($product->ExpenseAccountRef) && !is_null($product->ExpenseAccountRef)) {
                    $de_expense_account_id = Account::where('code', 'QBO-' . $product->ExpenseAccountRef)->pluck('id')->first();

                    $ak_item_data['de_expense_account_id'] = $de_expense_account_id;
                }
            }

            try {
                if (empty($ak_item)) {
                    $ak_item = Item::create($ak_item_data);
                } else {
                    $ak_item = $ak_item->update($ak_item_data);
                }
            } catch (\Exception $e) {
                report($e);
            }
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
                'type' => trans_choice('quickbooks::general.types.products', 2)
            ]);

            setting()->set('quickbooks.last_check', $timestamp);
            setting()->save();

            Cache::set('quickbooks_sync_running_' . company_id(), false, Date::now()->addHours(6));
        }

        return response()->json($json);
    }
}
