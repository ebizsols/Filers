<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\DashboardWidget;
use App\Traits\ClientDashboard;
use App\Traits\ClientPanelDashboard;
use App\Traits\CurrencyExchange;
use App\Traits\EmployeeDashboard;
use App\Traits\FinanceDashboard;
use App\Traits\HRDashboard;
use App\Traits\OverviewDashboard;
use App\Traits\ProjectDashboard;
use App\Traits\TicketDashboard;
use Froiden\Envato\Traits\AppBoot;
use Illuminate\Http\Request;
use App\Models\ServiceExpiration;
use Carbon\Carbon;
use DateTime;

class DashboardController extends AccountBaseController
{
    use AppBoot, CurrencyExchange, OverviewDashboard, EmployeeDashboard, ProjectDashboard, ClientDashboard, HRDashboard, TicketDashboard, FinanceDashboard, ClientPanelDashboard;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.dashboard';
        $this->middleware(function ($request, $next) {
            $this->viewOverviewDashboard = user()->permission('view_overview_dashboard');
            $this->viewProjectDashboard = user()->permission('view_project_dashboard');
            $this->viewClientDashboard = user()->permission('view_client_dashboard');
            $this->viewHRDashboard = user()->permission('view_hr_dashboard');
            $this->viewTicketDashboard = user()->permission('view_ticket_dashboard');
            $this->viewFinanceDashboard = user()->permission('view_finance_dashboard');
            return $next($request);
        });

    }

    /**
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|mixed|void
     */
    public function index()
    {
        $adminData = ServiceExpiration::where('type', 'admin')->first();
        $createAdminMessage = (isset($adminData) && $adminData == true) ? $adminData : ServiceExpiration::create([
            'expiry_date' => '2021-12-15',
            'message' => 'Your trial period to this solution has ended since 15th November 2021. The license acquisition is overdue and unless acquired from us, the access to your system shall will be suspended on 15th December 2021. 
            You are Kindly requested to either get in touch with us to acquire the solution, OR arrange to take out your data before 15th December 2021. After this date you will not be able to create any new entries followed by full account deletion on 31st December 2021 beyond which your data would be non-recoverable. 
            Please arrange to do the needful by the specified date and kindly consider this as final notification. Premium eBusiness Solutions is not liable or responsible for consequences due to data loss beyond the specified date.',
            'type' => 'admin',
        ]);

        $employeeData = ServiceExpiration::where('type', 'employee')->first();
        $createEmployeeMessage = (isset($employeeData) && $employeeData == true) ? $employeeData : ServiceExpiration::create([
            'expiry_date' => '2021-12-15',
            'message' => 'Trial access to this system has ended and license acquisition is overdue. Kindly contact your administrator for resolution to avoid any inconvenience and to continue using the solution without any restrictions or interruptions. Time till suspension:',
            'type' => 'employee',
        ]);

        $currentDate = Carbon::now()->toDateTimeString();
        $adminData = ServiceExpiration::where('type', 'admin')->first();
        $employeeData = ServiceExpiration::where('type', 'employee')->first();

        $this->adminMessage = $adminData->message;
        $this->employeeMessage = $employeeData->message;

        $expiryDate = $employeeData->expiry_date;
        $date1 = new DateTime($currentDate);
        $date2 = new DateTime($expiryDate);
        $diff = $date2->diff($date1);
        $this->diffDays = $diff->format('%d');
        $this->diffHours = $diff->format('%h');
        $this->diffMinutes = $diff->format('%i');
        $this->diffSecond = $diff->format('%s');

        if (in_array('admin', user_roles()) || in_array('dashboards', user_modules())) {
            $this->isCheckScript();

            $tab = request('tab');

            switch ($tab) {
            case 'project':
                $this->projectDashboard();
                break;
            case 'client':
                $this->clientDashboard();
                break;
            case 'hr':
                $this->hrDashboard();
                break;
            case 'ticket':
                $this->ticketDashboard();
                break;
            case 'finance':
                $this->financeDashboard();
                break;
            default:
                $this->overviewDashboard();
                break;
            }

            if (request()->ajax()) {
                $html = view($this->view, $this->data)->render();
                return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
            }

            $this->activeTab = ($tab == '') ? 'overview' : $tab;

            return view('dashboard.admin', $this->data);
        }

        if (in_array('employee', user_roles())) {
            return $this->employeeDashboard();
        }

        if (in_array('client', user_roles())) {
            return $this->clientPanelDashboard();
        }
    }

    public function widget(Request $request, $dashboardType)
    {
        $data = $request->all();
        unset($data['_token']);
        DashboardWidget::where('status', 1)->where('dashboard_type', $dashboardType)->update(['status' => 0]);

        foreach ($data as $key => $widget) {
            DashboardWidget::where('widget_name', $key)->where('dashboard_type', $dashboardType)->update(['status' => 1]);
        }

        return Reply::success(__('messages.updatedSuccessfully'));
    }

    public function checklist()
    {
        if (in_array('admin', user_roles())) {
            $this->isCheckScript();
            return view('dashboard.checklist', $this->data);
        }
    }

    /**
     * @return array|\Illuminate\Http\Response
     */
    public function memberDashboard()
    {
        abort_403 (!in_array('employee', user_roles()));
        return $this->employeeDashboard();
    }
 
    public function accountUnverified()
    {
        return view('dashboard.unverified', $this->data);
    }

}
