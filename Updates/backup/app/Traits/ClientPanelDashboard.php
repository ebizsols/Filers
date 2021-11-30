<?php

namespace App\Traits;

use App\Models\ContractSign;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Illuminate\Support\Facades\DB;

/**
 *
 */
trait ClientPanelDashboard
{

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function clientPanelDashboard()
    {

        $this->counts = DB::table('users')
            ->select(
                DB::raw('(select count(projects.id) from `projects` where client_id = ' . $this->user->id . ') as totalProjects'),
                DB::raw('(select count(tickets.id) from `tickets` where (status="open" or status="pending") and user_id = ' . $this->user->id . ') as totalUnResolvedTickets')
            )
            ->first();

        // Amount paid
        $payments = Payment::join('currencies', 'currencies.id', '=', 'payments.currency_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('projects', 'projects.id', '=', 'payments.project_id')
            ->select('payments.amount', 'currencies.id as currency_id', 'currencies.exchange_rate', 'projects.client_id', 'payments.invoice_id', 'payments.project_id')
            ->where('payments.status', 'complete');
        $payments = $payments->where(function ($query) {
            $query->where('projects.client_id', user()->id)
                ->orWhere('invoices.client_id', user()->id);
        });
        $payments = $payments->orderBy('paid_on', 'ASC')
            ->get();

        $paymentTotal = 0;

        foreach ($payments as $chart) {

            if ($chart->currency->currency_code != $this->global->currency->currency_code && $chart->currency->exchange_rate != 0) {
                if ($chart->currency->is_cryptocurrency == 'yes') {
                    $usdTotal = ($chart->amount * $chart->currency->usd_price);
                    $paymentTotal = $paymentTotal + floor($usdTotal / $chart->exchange_rate);

                } else {
                    $paymentTotal = $paymentTotal + floor($chart->amount / $chart->exchange_rate);
                }
            } else {
                $paymentTotal = $paymentTotal + round($chart->amount, 2);
            }
        }

        $this->totalPaidAmount = $paymentTotal;

        // Total Pending amount
        $invoices = Invoice::join('currencies', 'currencies.id', '=', 'invoices.currency_id')
            ->where(function ($query) {
                $query->where('invoices.status', 'unpaid')
                    ->orWhere('invoices.status', 'partial');
            })
            ->where('invoices.client_id', user()->id)
            ->where('invoices.send_status', 1)
            ->select(
                'invoices.*',
                'currencies.currency_code',
                'currencies.is_cryptocurrency',
                'currencies.usd_price',
                'currencies.exchange_rate'
            )
            ->get();

        $totalPendingAmount = 0;

        foreach ($invoices as $invoice) {
            if ($invoice->currency->currency_code != $this->global->currency->currency_code && $invoice->currency->exchange_rate != 0) {

                if ($invoice->currency->is_cryptocurrency == 'yes') {
                    $usdTotal = ($invoice->due_amount * $invoice->currency->usd_price);
                    $totalPendingAmount += floor($usdTotal / $invoice->currency->exchange_rate);

                } else {
                    $totalPendingAmount += floor($invoice->due_amount / $invoice->currency->exchange_rate);
                }
            } else {
                $totalPendingAmount += round($invoice->due_amount, 2);
            }

        }

        $this->totalPendingAmount = $totalPendingAmount;

        $this->totalContractsSigned = ContractSign::whereHas('contract', function ($query) {
            $query->where('client_id', user()->id);
        })
            ->count();

        $this->pendingMilestone = ProjectMilestone::with('project', 'currency')
            ->whereHas('project', function ($query) {
                $query->where('client_id', user()->id);
            })
            ->get();

        $this->statusWiseProject = $this->projectStatusChartData();

        return view('dashboard.client.index', $this->data);
    }

    public function projectStatusChartData()
    {
        $labels = ['in progress', 'on hold', 'not started', 'canceled', 'finished'];
        $data['labels'] = [__('app.inProgress'), __('app.onHold'), __('app.notStarted'), __('app.canceled'), __('app.finished')];
        $data['colors'] = ['#1d82f5', '#FCBD01', '#FCBD01', '#D30000', '#2CB100'];
        $data['values'] = [];

        foreach ($labels as $label) {
            $data['values'][] = Project::where('client_id', user()->id)->where('status', $label)->count();
        }

        return $data;
    }

}
