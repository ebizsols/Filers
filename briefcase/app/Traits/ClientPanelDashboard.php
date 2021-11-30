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

        // Invoices paid
        $this->totalPaidInvoice = Invoice::where(function ($query) {
                $query->where('invoices.status', 'paid')
                    ->orWhere('invoices.status', 'partial');
        })
        ->where('invoices.client_id', user()->id)
        ->where('invoices.send_status', 1)
        ->select(
            'invoices.id'
        )
        ->count();
 

        // Total Pending invoices
        $this->totalUnPaidInvoice = Invoice::where(function ($query) {
                $query->where('invoices.status', 'unpaid')
                    ->orWhere('invoices.status', 'partial');
        })
        ->where('invoices.client_id', user()->id)
        ->where('invoices.send_status', 1)
        ->select(
            'invoices.id'
        )
        ->count();

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
