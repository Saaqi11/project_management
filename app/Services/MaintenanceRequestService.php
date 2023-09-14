<?php

namespace App\Services;

use App\Models\FileManager;
use App\Models\MaintenanceRequest;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MaintenanceRequestService
{
    use ResponseTrait;

    public function getAllData()
    {
        $maintenance = MaintenanceRequest::query()
            ->join('properties', 'maintenance_requests.property_id', '=', 'properties.id')
            ->leftJoin('property_units', 'maintenance_requests.unit_id', '=', 'property_units.id')
            ->join('maintenance_issues', 'maintenance_requests.issue_id', '=', 'maintenance_issues.id')
            ->where('maintenance_requests.owner_user_id', auth()->id())
            ->select('maintenance_requests.*', 'properties.name as property_name', 'maintenance_issues.name as issue_name', 'property_units.unit_name');
        return datatables($maintenance)
            ->addIndexColumn()
            ->addColumn('details', function ($maintenance) {
                return Str::limit($maintenance->details, 50, '...');
            })
            ->addColumn('status', function ($maintenance) {
                if ($maintenance->status == MAINTENANCE_REQUEST_STATUS_COMPLETE) {
                    return '<div class="status-btn status-btn-green font-13 radius-4">Completed</div>';
                } elseif ($maintenance->status == MAINTENANCE_REQUEST_STATUS_INPROGRESS) {
                    return '<div class="status-btn status-btn-orange font-13 radius-4">In Progress</div>';
                } else {
                    return '<div class="status-btn status-btn-red font-13 radius-4">Pending</div>';
                }
            })
            ->addColumn('action', function ($maintenance) {
                $id = $maintenance->id;
                return '<div class="tbl-action-btns d-inline-flex">
                            <button type="button" class="p-1 tbl-action-btn view" data-bs-toggle="modal" data-id="' . $id . '" data-bs-target="#viewModal" title="View"><span class="iconify" data-icon="carbon:view-filled"></span></button>
                            <button type="button" class="p-1 tbl-action-btn edit" data-id="' . $id . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>
                            <button onclick="deleteItem(\'' . route('owner.maintenance-request.delete', $id) . '\', \'allDatatable\')" class="p-1 tbl-action-btn"   title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>
                        </div>';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function getByPropertyId($id)
    {
        $maintenance = MaintenanceRequest::query()
            ->join('properties', 'maintenance_requests.property_id', '=', 'properties.id')
            ->leftJoin('property_units', 'maintenance_requests.unit_id', '=', 'property_units.id')
            ->join('maintenance_issues', 'maintenance_requests.issue_id', '=', 'maintenance_issues.id')
            ->select('maintenance_requests.*', 'properties.name as property_name', 'maintenance_issues.name as issue_name', 'property_units.unit_name')
            ->where('maintenance_requests.property_id', $id)
            ->get();
        return $maintenance;
    }


    public function store($request)
    {
        DB::beginTransaction();
        try {
            $id = $request->get('id', '');
            if ($id != '') {
                $maintenance = MaintenanceRequest::where('owner_user_id', auth()->id())->findOrFail($request->id);
            } else {
                $maintenance = new MaintenanceRequest();
            }

            // maintenance
            $maintenance->property_id = $request->property_id;
            $maintenance->owner_user_id = auth()->id();
            $maintenance->unit_id = $request->unit_id;
            $maintenance->issue_id = $request->issue_id;
            $maintenance->details = $request->details;
            $maintenance->status = $request->status;
            $maintenance->save();

            /*File Manager Call upload*/
            if ($request->hasFile('attach')) {
                $exitFile = FileManager::where('origin_type', 'App\Models\MaintenanceRequest')->where('id', $maintenance->attach_id)->first();
                if ($exitFile) {
                    $exitFile->removeFile();
                    $upload = $exitFile->updateUpload($exitFile->id, 'MaintenanceRequest', $request->attach);
                } else {
                    $newFile = new FileManager();
                    $upload = $newFile->upload('MaintenanceRequest', $request->attach);
                }

                if ($upload['status']) {
                    $maintenance->attach_id = $upload['file']->id;
                    $maintenance->save();
                    $upload['file']->origin_type = "App\Models\MaintenanceRequest";
                    $upload['file']->save();
                } else {
                    throw new Exception($upload['message']);
                }
            }
            /*End*/
            DB::commit();
            $message = $request->id ? UPDATED_SUCCESSFULLY : CREATED_SUCCESSFULLY;
            return $this->success([], $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function getInfo($id)
    {

        $maintenance = MaintenanceRequest::query()
            ->join('properties', 'maintenance_requests.property_id', '=', 'properties.id')
            ->leftJoin('property_units', 'maintenance_requests.unit_id', '=', 'property_units.id')
            ->join('maintenance_issues', 'maintenance_requests.issue_id', '=', 'maintenance_issues.id')
            ->select('maintenance_requests.*', 'properties.name as property_name', 'maintenance_issues.name as issue_name', 'property_units.unit_name')
            ->where('maintenance_requests.id', $id)
            ->firstOrFail();

        $maintenance->attach = $maintenance->attach;
        $maintenance->invoice = $maintenance->invoice;
        return $maintenance;
    }

    public function statusChange($request)
    {
        DB::beginTransaction();
        try {
            $ownerUserId = auth()->user()->role == USER_ROLE_OWNER ? auth()->id() : auth()->user()->owner_user_id;
            $maintenance = MaintenanceRequest::where('owner_user_id', $ownerUserId)->findOrFail($request->id);
            $maintenance->status = $request->status;
            $maintenance->amount = $request->amount;
            $maintenance->save();

            /*File Manager Call upload*/
            if ($request->hasFile('invoice')) {
                $exitFile = FileManager::where('origin_type', 'App\Models\MaintenanceRequest')->where('id', $maintenance->invoice_id)->first();
                if ($exitFile) {
                    $exitFile->removeFile();
                    $upload = $exitFile->updateUpload($exitFile->id, 'MaintenanceRequest', $request->invoice);
                } else {
                    $newFile = new FileManager();
                    $upload = $newFile->upload('MaintenanceRequest', $request->invoice);
                }

                if ($upload['status']) {
                    $maintenance->invoice_id = $upload['file']->id;
                    $maintenance->save();
                    $upload['file']->origin_type = "App\Models\MaintenanceRequest";
                    $upload['file']->save();
                } else {
                    throw new Exception($upload['message']);
                }
            }
            /*End*/
            DB::commit();
            $message = STATUS_UPDATED_SUCCESSFULLY;
            return $this->success([], $message);
        } catch (Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }

    public function deleteById($id)
    {
        DB::beginTransaction();
        try {
            $information =  MaintenanceRequest::where('owner_user_id', auth()->id())->findOrFail($id);
            $information->delete();
            DB::commit();
            $message = DELETED_SUCCESSFULLY;
            return $this->success([], $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $message = getErrorMessage($e, $e->getMessage());
            return $this->error([],  $message);
        }
    }
}
