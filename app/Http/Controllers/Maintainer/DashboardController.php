<?php

namespace App\Http\Controllers\Maintainer;

use App\Http\Controllers\Controller;
use App\Models\NoticeBoard;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['pageTitle'] = __('Dashboard');
        $maintainerUser = auth()->user()->maintainer;
        $data['totalOpenTickets'] = Ticket::query()->where('property_id', $maintainerUser->property_id)->where('status', TICKET_STATUS_OPEN)->count();
        $data['totalResolvedTickets'] = Ticket::query()->where('property_id', $maintainerUser->property_id)->where('status', TICKET_STATUS_RESOLVED)->count();
        $data['totalCloseTickets'] = Ticket::query()->where('property_id', $maintainerUser->property_id)->where('status', TICKET_STATUS_CLOSE)->count();
        $data['today'] = date('Y-m-d');
        $data['notices'] = NoticeBoard::where('property_id', $maintainerUser->property_id)->where('start_date', '<=', $data['today'])->where('end_date', '>=', $data['today'])->limit(10)->get();
        $data['tickets'] = Ticket::where('property_id', $maintainerUser->property_id)->limit(10)->get();
        return view('maintainer.dashboard')->with($data);
    }
}
