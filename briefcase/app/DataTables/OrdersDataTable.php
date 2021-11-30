<?php

namespace App\DataTables;

use App\DataTables\BaseDataTable;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class OrdersDataTable extends BaseDataTable
{
    private $deleteOrderPermission;
    private $editOrderPermission;
    private $viewOrderPermission;

    public function __construct()
    {
        parent::__construct();
        $this->viewOrderPermission = user()->permission('view_order');
        $this->deleteOrderPermission = user()->permission('delete_order');
        $this->editOrderPermission = user()->permission('edit_order');
    }

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $action = '<div class="task_view">

                <div class="dropdown">
                    <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                        id="dropdownMenuLink-' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-options-vertical icons"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-' . $row->id . '" tabindex="0">';

                $action .= ' <a href="' . route('orders.show', [$row->id]) . '" class="dropdown-item"><i class="fa fa-eye mr-2"></i>' . __('app.view') . '</a>';

                if ($this->editOrderPermission == 'all' || ($this->editOrderPermission == 'added' && $row->added_by == user()->id) && $row->status != 'paid') {
                    $action .= '<a class="dropdown-item openRightModal" href="' . route('orders.edit', $row->id) . '" >
                        <i class="fa fa-edit mr-2"></i>
                        ' . trans('app.edit') . '
                    </a>';
                }

                if ($row->status != 'paid' &&
                    ($this->deleteOrderPermission == 'all'
                    || ($this->deleteOrderPermission == 'owned' && $row->client_id == user()->id)
                )) {
                    $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-toggle="tooltip"  data-order-id="' . $row->id . '">
                        <i class="fa fa-trash mr-2"></i>
                        ' . trans('app.delete') . '
                    </a>';
                }

                $action .= '</div>
                </div>
            </div>';

                return $action;
            })
            ->editColumn('order_number', function ($row) {

                return '<div class="media align-items-center">
                        <div class="media-body">
                    <h5 class="mb-0 f-13 text-darkest-grey"><a href="' . route('orders.show', [$row->id]) . '">' . $row->order_number . '</a></h5>
                    </div>
                  </div>';

            })
            ->addColumn('client_name', function ($row) {
                return $row->client->name;
            })
            ->editColumn('name', function ($row) {

                $client = $row->client;

                return view('components.client', [
                    'user' => $client
                ]);
            })
            ->editColumn('status', function ($row) {
                $status = '';

                if ($row->status == 'unpaid') {
                        $status .= ' <i class="fa fa-circle mr-1 text-red f-10"></i>' .  __('app.' . $row->status);
                }
                elseif ($row->status == 'paid') {
                    $status .= ' <i class="fa fa-circle mr-1 text-dark-green f-10"></i>' .  __('app.' . $row->status);
                }

                return $status;
            })
            ->editColumn('total', function ($row) {
                $currencySymbol = $row->currency->currency_symbol;

                return currency_formatter($row->total, $currencySymbol);
            })
            ->editColumn(
                'order_date',
                function ($row) {
                    return Carbon::parse($row->order_date)->timezone($this->global->timezone)->format($this->global->date_format);
                }
            )
            ->rawColumns(['action', 'status', 'total', 'name', 'order_number'])
            ->removeColumn('currency_symbol')
            ->removeColumn('currency_code');
    }

    public function ajax()
    {
        return $this->dataTable($this->query())
            ->make(true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $request = $this->request();

        $model = Order::with([
            'currency:id,currency_symbol,currency_code', 'client', 'payment'
            ])
            ->with('client', 'client.session', 'client.clientDetails', 'payment')
            ->select('orders.id', 'orders.client_id', 'orders.currency_id', 'orders.total', 'orders.status', 'orders.order_date', 'orders.show_shipping_address', 'orders.added_by');

        if ($request->startDate !== null && $request->startDate != 'null' && $request->startDate != '') {
            $startDate = Carbon::createFromFormat($this->global->date_format, $request->startDate)->toDateString();
            $model = $model->where(DB::raw('DATE(orders.`order_date`)'), '>=', $startDate);
        }

        if ($request->endDate !== null && $request->endDate != 'null' && $request->endDate != '') {
            $endDate = Carbon::createFromFormat($this->global->date_format, $request->endDate)->toDateString();
            $model = $model->where(DB::raw('DATE(orders.`order_date`)'), '<=', $endDate);
        }

        if ($request->status != 'all' && !is_null($request->status)) {
            $model = $model->where('orders.status', '=', $request->status);
        }

        if ($request->clientID != 'all' && !is_null($request->clientID)) {
            $model = $model->where('orders.client_id', '=', $request->clientID);
        }

        if ($request->searchText != '') {
            $model->where(function ($query) {
                $query->where('orders.id', 'like', '%' . request('searchText') . '%')
                    ->orWhere('orders.total', 'like', '%' . request('searchText') . '%');
            });
        }

        if ($this->viewOrderPermission == 'added') {
            $model->where('orders.added_by', user()->id);
        }

        if (in_array('client', user_roles())) {
            $model->where('orders.client_id', user()->id);
        }

        return $model;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('orders-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(5)
            ->destroy(true)
            ->responsive(true)
            ->serverSide(true)
            /* ->stateSave(true) */
            ->processing(true)
            ->language(__('app.datatable'))
            ->parameters([
                'initComplete' => 'function () {
                    window.LaravelDataTables["orders-table"].buttons().container()
                    .appendTo( "#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ])
            ->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> ' . trans('app.exportExcel')]));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            __('app.id') => ['data' => 'id', 'name' => 'id', 'visible' => false],
            __('app.order') . '#' => ['data' => 'order_number', 'name' => 'order_number', 'exportable' => false],
            __('app.client_name') => ['data' => 'client_name', 'name' => 'project.client.name', 'visible' => false],
            __('app.client') => ['data' => 'name', 'name' => 'project.client.name', 'visible' => !in_array('client', user_roles()), 'exportable' => false],
            __('modules.invoices.total') => ['data' => 'total', 'name' => 'total', 'class' => 'text-right'],
            __('modules.orders.orderDate') => ['data' => 'order_date', 'name' => 'order_date'],
            __('app.status') => ['data' => 'status', 'name' => 'status', 'width' => '10%'],
            Column::computed('action', __('app.action'))
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->width(150)
                ->addClass('text-right pr-20')
        ];

    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Orders_' . date('YmdHis');
    }

    public function pdf()
    {
        set_time_limit(0);

        if ('snappy' == config('datatables-buttons.pdf_generator', 'snappy')) {
            return $this->snappyPdf();
        }

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('datatables::print', ['data' => $this->getDataForPrint()]);

        return $pdf->download($this->getFilename() . '.pdf');
    }

}
