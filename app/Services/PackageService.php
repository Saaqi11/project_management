<?php

namespace App\Services;

use App\Models\OwnerPackage;
use App\Models\Package;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class PackageService
{
    use ResponseTrait;
    public function getAllData($request)
    {
        $packages = Package::query();

        return datatables($packages)
            ->addColumn('name', function ($package) {
                return $package->name;
            })->addColumn('monthly_price', function ($package) {
                return currencyPrice($package->monthly_price);
            })->addColumn('yearly_price', function ($package) {
                return currencyPrice($package->yearly_price);
            })->addColumn('status', function ($package) {
                if ($package->status == ACTIVE) {
                    return '<div class="status-btn status-btn-green font-13 radius-4">Active</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Deactivate</div>';
                }
            })->addColumn('trail', function ($package) {
                if ($package->is_trail == ACTIVE) {
                    return '<div class="status-btn status-btn-blue font-13 radius-4">Yes</div>';
                } else {
                    return '<div class="status-btn status-btn-red font-13 radius-4">No</div>';
                }
            })->addColumn('action', function ($package) {
                return '<div class="tbl-action-btns d-inline-flex">
                    <button type="button" class="p-1 tbl-action-btn edit" data-id="' . $package->id . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>
                    <button onclick="deleteItem(\'' . route('admin.packages.destroy', $package->id) . '\', \'allDataTable\')" class="p-1 tbl-action-btn"   title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>
                </div>';
            })
            ->rawColumns(['name', 'status', 'trail', 'action'])
            ->make(true);
    }

    public function getActiveAll()
    {
        return Package::where('status', ACTIVE)->where('is_trail', '!=', ACTIVE)->get();
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $id = $request->get('id', '');
            if ($id != '') {
                $package = Package::findOrFail($request->id);
            } else {
                $package = new Package();
            }

            // Slug exists
            $slug = getSlug($request->name);
            $slugExist = Package::where('slug', $slug)->whereNot('id', $request->id)->exists();
            if ($slugExist) {
                throw new Exception('Name Already Exist');
            }

            $package->name = $request->name;
            $package->slug = $slug;
            $package->max_property = $request->max_property;
            $package->max_unit = $request->max_unit ?? 0;
            $package->max_tenant = $request->max_tenant;
            $package->max_maintainer = $request->max_maintainer;
            $package->max_invoice = $request->max_invoice;
            $package->max_auto_invoice = $request->max_auto_invoice;
            $package->notice_support = $request->notice_support;
            $package->ticket_support = $request->ticket_support;
            $package->status = $request->status;
            $package->is_trail = $request->is_trail;
            $package->is_default = $request->is_default;
            $package->monthly_price = $request->monthly_price;
            $package->yearly_price = $request->yearly_price;
            $package->save();

            // update if status changed
            // if (is_null(Package::where(['status' => ACTIVE])->first())) {
            //     Package::first()->update(['status' => ACTIVE]);
            // }

            // // update if trail changed
            // if (is_null(Package::where(['is_trail' => ACTIVE, 'status' => ACTIVE])->first())) {
            //     Package::where(['status' => ACTIVE])->first()->update(['is_trail' => ACTIVE]);
            // }

            DB::commit();
            $message = $request->id ? __(UPDATED_SUCCESSFULLY) : __(CREATED_SUCCESSFULLY);
            return $this->success([], $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function getInfo($id)
    {
        return Package::findOrFail($id);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            if (Package::where('status', ACTIVE)->count() > 1) {
                Package::findOrFail($id)->delete();

                // update if trail changed
                if (is_null(Package::where(['is_trail' => ACTIVE, 'status' => ACTIVE])->first())) {
                    Package::where(['status' => ACTIVE])->first()->update(['is_trail' => ACTIVE]);
                }

                DB::commit();
                $message = __(DELETED_SUCCESSFULLY);
                return $this->success([], $message);
            } else {
                $message = __("Trail package can not be deleted");
                return $this->error([], $message);
            }
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function getUserPackagesData($request)
    {
        $ownerPackages = OwnerPackage::query()
            ->join('users', 'owner_packages.user_id', '=', 'users.id')
            ->join('packages', 'owner_packages.package_id', '=', 'packages.id')
            ->join('subscription_orders', 'owner_packages.order_id', '=', 'subscription_orders.id')
            ->join('gateways', 'subscription_orders.gateway_id', '=', 'gateways.id')
            ->select('owner_packages.*', 'users.first_name', 'users.last_name', 'packages.name as packageName', 'subscription_orders.payment_status', 'gateways.title as gatewaysName')
            ->orderBy('owner_packages.id', 'desc');

        return datatables($ownerPackages)
            ->addColumn('user_name', function ($ownerPackage) {
                return  $ownerPackage->first_name . ' ' . $ownerPackage->last_name;
            })->addColumn('package_name', function ($ownerPackage) {
                return  $ownerPackage->packageName;
            })->addColumn('gateway', function ($ownerPackage) {
                return  $ownerPackage->gatewaysName;
            })->addColumn('payment_status', function ($ownerPackage) {
                if ($ownerPackage->payment_status == ORDER_PAYMENT_STATUS_PAID) {
                    return '<div class="status-btn status-btn-green font-13 radius-4">Paid</div>';
                } elseif ($ownerPackage->payment_status == ORDER_PAYMENT_STATUS_PENDING) {
                    return '<div class="status-btn status-btn-red font-13 radius-4">Pending</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Cancelled</div>';
                }
            })->addColumn('start_date', function ($ownerPackage) {
                return  date('Y-m-d', strtotime($ownerPackage->start_date));
            })->addColumn('end_date', function ($ownerPackage) {
                return  date('Y-m-d', strtotime($ownerPackage->end_date));
            })->addColumn('status', function ($ownerPackage) {
                if ($ownerPackage->status == ACTIVE) {
                    return '<div class="status-btn status-btn-blue font-13 radius-4">Active</div>';
                } else {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">Deactivate</div>';
                }
            })->addColumn('action', function ($ownerPackage) {
                return '<div class="tbl-action-btns d-inline-flex">
                    <button type="button" class="p-1 tbl-action-btn edit" data-id="' . $ownerPackage->id . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>
                </div>';
            })
            ->rawColumns(['user_name', 'package_name', 'payment_status', 'start_date', 'end_date', 'status', 'action'])
            ->make(true);
    }
}
