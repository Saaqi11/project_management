<?php

namespace App\Services;

use App\Models\Owner;

class OwnerService
{
    public function getAllData($request)
    {

        $owners = Owner::query()
            ->join('users', 'owners.user_id', '=', 'users.id')
            ->select('users.*')
            ->orderBy('owners.id', 'desc');

        return datatables($owners)
            ->addColumn('name', function ($owner) {
                return $owner->first_name . ' ' . $owner->last_name;
            })
            ->addColumn('email', function ($owner) {
                return $owner->email;
            })
            ->addColumn('contact_number', function ($owner) {
                return $owner->contact_number;
            })
            ->addColumn('status', function ($package) {
                if ($package->status == ACTIVE) {
                    return '<div class="status-btn status-btn-green font-13 radius-4">Active</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Deactivate</div>';
                }
            })
            ->rawColumns(['name', 'status', 'trail', 'action'])
            ->make(true);
    }
}
