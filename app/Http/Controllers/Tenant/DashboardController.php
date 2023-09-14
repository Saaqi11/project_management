<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\NoticeBoard;
use App\Models\Property;
use App\Models\PropertyUnit;
use App\Models\Ticket;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['pageTitle'] = __('Dashboard');
        $tenantUser = auth()->user()->tenant;
        $data['property'] = Property::findOrFail($tenantUser->property_id);
        $data['unit'] = PropertyUnit::findOrFail($tenantUser->unit_id);
        $data['tenant'] = $tenantUser;
        $data['invoices'] = Invoice::where('tenant_id', $tenantUser->id)->get();
        $data['totalTickets'] = Ticket::query()->where('unit_id', $tenantUser->unit_id)->count();
        $data['today'] = date('Y-m-d');
        $data['notices'] = NoticeBoard::with('userNotices')
            ->where(function ($q) use ($tenantUser) {
                $q->where('unit_id', $tenantUser->unit_id)
                    ->orWhere('unit_all', ACTIVE);
            })
            ->where('start_date', '<=', $data['today'])
            ->where('end_date', '>=', $data['today'])
            ->limit(10)
            ->get();
        return view('tenant.dashboard')->with($data);
    }
}
