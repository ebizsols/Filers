<?php

namespace App\Http\Controllers;

use App\DataTables\ProductsDataTable;
use App\Helper\Reply;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubCategory;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ProductController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.products';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('products', $this->user->modules));
            return $next($request);
        });
    }

    /**
     * @param ProductsDataTable $dataTable
     * @return mixed|void
     */
    public function index(ProductsDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_product');

        abort_403(!in_array($viewPermission, ['all', 'added']));


        $productDetails = [];

        if (request()->hasCookie('productDetails')) {
            $productDetails = json_decode(request()->cookie('productDetails'), true);
        }

        $this->productDetails = $productDetails;

        $this->totalProducts = Product::count();
        $this->categories = ProductCategory::all();
        $this->subCategories = ProductSubCategory::all();

        return $dataTable->render('products.index', $this->data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->addPermission = user()->permission('add_product');
        abort_403(!in_array($this->addPermission, ['all', 'added']));
        $this->taxes = Tax::all();
        $this->categories = ProductCategory::all();
        $this->subCategories = ProductSubCategory::all();
        $this->pageTitle = __('app.add') . ' ' . __('app.menu.products');

        if (request()->ajax()) {
            $html = view('products.ajax.create', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'products.ajax.create';
        return view('products.create', $this->data);
    }

    /**
     *
     * @param StoreProductRequest $request
     * @return void
     */
    public function store(StoreProductRequest $request)
    {
        $this->addPermission = user()->permission('add_product');
        abort_403(!in_array($this->addPermission, ['all', 'added']));

        $products = new Product();
        $products->name = $request->name;
        $products->price = $request->price;
        $products->taxes = $request->tax ? json_encode($request->tax) : null;
        $products->description = ($request->description !== '<p><br></p>') ? $request->description : '';
        $products->hsn_sac_code = $request->hsn_sac_code;
        $products->allow_purchase = ($request->purchase_allow == 'no') ? true : false;
        $products->category_id = ($request->category_id) ? $request->category_id : null;
        $products->sub_category_id = ($request->sub_category_id) ? $request->sub_category_id : null;
        $products->save();

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('products.index');
        }

        return Reply::successWithData(__('messages.productAdded'), ['redirectUrl' => $redirectUrl]);

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->product = Product::find($id);
        $this->viewPermission = user()->permission('view_product');
        abort_403(!($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->product->added_by == user()->id)));

        $this->taxes = Tax::get();
        $this->pageTitle = $this->product->name;

        $this->taxValue = '';
        $taxes = [];

        foreach ($this->taxes as $tax) {
            if ($this->product && isset($this->product->taxes) && array_search($tax->id, json_decode($this->product->taxes)) !== false) {
                $taxes[] = $tax->tax_name . ' : ' . $tax->rate_percent . '%';
            }
        }

        $this->taxValue = implode(', ', $taxes);

        $this->category = $this->product ? ProductCategory::find($this->product->category_id) : '';

        $this->subCategory = $this->category ? ProductSubCategory::where('category_id', $this->category->id)->first() : '';

        if (request()->ajax()) {
            $html = view('products.ajax.show', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'products.ajax.show';

        return view('products.create', $this->data);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->product = Product::find($id);
        $this->editPermission = user()->permission('edit_product');
        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->product->added_by == user()->id)));

        $this->taxes = Tax::all();
        $this->categories = ProductCategory::all();
        $this->subCategories = !is_null($this->product->sub_category_id) ? ProductSubCategory::where('category_id', $this->product->category_id)->get() : [];
        $this->pageTitle = __('app.update') . ' ' . __('app.menu.products');

        if (request()->ajax()) {
            $html = view('products.ajax.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'products.ajax.edit';

        return view('products.create', $this->data);

    }

    /**
     * @param UpdateProductRequest $request
     * @param int $id
     * @return array|void
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $products = Product::find($id);
        $this->editPermission = user()->permission('edit_product');

        abort_403(!($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->product->added_by == user()->id)));

        $products->name = $request->name;
        $products->price = $request->price;
        $products->taxes = $request->tax ? json_encode($request->tax) : null;
        $products->hsn_sac_code = $request->hsn_sac_code;
        $products->description = ($request->description !== '<p><br></p>') ? $request->description : '';
        $products->allow_purchase = ($request->purchase_allow == 'no') ? true : false;
        $products->category_id = ($request->category_id) ? $request->category_id : null;
        $products->sub_category_id = ($request->sub_category_id) ? $request->sub_category_id : null;

        $products->save();

        return Reply::successWithData(__('messages.productUpdated'), ['redirectUrl' => route('products.index')]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $products = Product::find($id);
        $this->deletePermission = user()->permission('delete_product');
        abort_403(!($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $products->added_by == user()->id)));

        Product::destroy($id);
        return Reply::successWithData(__('messages.productDeleted'), ['redirectUrl' => route('products.index')]);

    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
        case 'delete':
            $this->deleteRecords($request);
                return Reply::success(__('messages.deleteSuccess'));
        case 'change-purchase':
            $this->allowPurchase($request);
                return Reply::success(__('messages.statusUpdatedSuccessfully'));
        default:
                return Reply::error(__('messages.selectAction'));
        }
    }

    protected function deleteRecords($request)
    {
        abort_403(user()->permission('delete_product') != 'all');

        Product::whereIn('id', explode(',', $request->row_ids))->forceDelete();
    }

    protected function allowPurchase($request)
    {
        abort_403(user()->permission('edit_product') != 'all');

        Product::whereIn('id', explode(',', $request->row_ids))->update(['allow_purchase' => $request->status]);
    }

    public function addCartItem(Request $request)
    {
        $newItem = $request->productID;
        $productDetails = [];

        if ($request->hasCookie('productDetails')) {
            $productDetails = json_decode($request->cookie('productDetails'), true);
        }

        if ($productDetails) {
            if (is_array($productDetails)) {
                $productDetails[] = $newItem;
            }
            else {
                array_push($productDetails, $newItem);
            }
        }
        else {
            $productDetails[] = $newItem;
        }

        return response(Reply::successWithData(__('messages.productAdded'), ['status' => 'success', 'productItems' => $productDetails]))->cookie('productDetails', json_encode($productDetails));
    }

    public function removeCartItem(Request $request, $id)
    {
        $productDetails = [];

        if ($request->hasCookie('productDetails')) {
            $productDetails = json_decode($request->cookie('productDetails'), true);

            foreach (array_keys($productDetails, $id) as $key) {
                unset($productDetails[$key]);
            }
        }

        return response(Reply::successWithData(__('messages.deleteSuccess'), ['status' => 'success', 'productItems' => $productDetails]))->cookie('productDetails', json_encode($productDetails));
    }

    public function cart(Request $request)
    {
        abort_403(!in_array('client', user_roles()));

        $this->lastOrder = Order::lastOrderNumber() + 1;
        $this->invoiceSetting = invoice_setting();
        $this->taxes = Tax::all();
        $productData = [];

        if ($request->hasCookie('productDetails')) {
            $productDetails = json_decode($request->cookie('productDetails'), true);

            $this->quantityArray = array_count_values($productDetails);
            $this->prodc = $productDetails;
            $this->productKeys = array_unique($this->prodc);
            $this->products = Product::where('allow_purchase', 1)->whereIn('id', $this->productKeys)->get();
        }

        if (request()->ajax()) {
            $html = view('products.ajax.cart', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'products.ajax.cart';
        return view('products.create', $this->data);

    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function placeOrder(Request $request)
    {
        $items = $request->item_name;
        $cost_per_item = $request->cost_per_item;
        $quantity = $request->quantity;
        $amount = $request->amount;

        if (!empty($items)) {
            foreach ($items as $itm) {
                if (is_null($itm)) {
                    return Reply::error(__('messages.itemBlank'));
                }
            }
        }
        else {
            return Reply::error(__('messages.addItem'));
        }

        foreach ($quantity as $qty) {
            if (!is_numeric($qty) && (intval($qty) < 1)) {
                return Reply::error(__('messages.quantityNumber'));
            }
        }

        foreach ($cost_per_item as $rate) {
            if (!is_numeric($rate)) {
                return Reply::error(__('messages.unitPriceNumber'));
            }
        }

        foreach ($amount as $amt) {
            if (!is_numeric($amt)) {
                return Reply::error(__('messages.amountNumber'));
            }
        }

        $order = new Order();
        $order->client_id = user()->id;
        $order->order_date = Carbon::now()->format('Y-m-d');
        $order->sub_total = round($request->sub_total, 2);
        $order->total = round($request->total, 2);
        $order->currency_id = $this->global->currency_id;
        $order->note = $request->note;
        $order->show_shipping_address = ($request->has('shipping_address') ? 'yes' : 'no');
        $order->save();

        if ($order->show_shipping_address == 'yes') {
            /** @phpstan-ignore-next-line */
            $client = $order->clientdetails;
            $client->shipping_address = $request->shipping_address;
            $client->save();
        }

        // Log search
        $this->logSearchEntry($order->id, $order->id, 'orders.show', 'order');

        return response(Reply::redirect(route('orders.show', $order->id), __('messages.orderCreated')))->withCookie(Cookie::forget('productDetails'));
    }

}
