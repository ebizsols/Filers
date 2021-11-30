<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Models\TicketAgentGroups;
use App\Models\TicketChannel;
use App\Models\TicketGroup;
use App\Models\TicketReplyTemplate;
use App\Models\TicketType;
use App\Models\User;

class TicketSettingController extends AccountBaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.ticketAgents';
        $this->activeSettingMenu = 'ticket_settings';
        $this->middleware(function ($request, $next) {
            abort_403(!(user()->permission('manage_ticket_setting') == 'all'));
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->agents = TicketAgentGroups::with('user')->get();
        $this->employees = User::doesntHave('agent')
            ->join('role_user', 'role_user.user_id', '=', 'users.id')
            ->join('roles', 'roles.id', '=', 'role_user.role_id')
            ->select('users.id', 'users.name', 'users.email', 'users.created_at')
            ->where('roles.name', 'employee')
            ->get();
        $this->groups = TicketGroup::all();
        $this->ticketTypes = TicketType::all();
        $this->templates = TicketReplyTemplate::all();
        $this->channels = TicketChannel::all();


        $this->view = 'ticket-settings.ajax.agent';

        $tab = request('tab');

        switch ($tab) {
        case 'type':
            $this->view = 'ticket-settings.ajax.type';
                break;
        case 'channel':
            $this->view = 'ticket-settings.ajax.channel';
                break;
        case 'reply-template':
            $this->view = 'ticket-settings.ajax.reply-template';
                break;
        default:
            $this->view = 'ticket-settings.ajax.agent';
                break;
        }

        ($tab == '') ? $this->activeTab = 'agent' : $this->activeTab = $tab;

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('ticket-settings.index', $this->data);

    }

}
