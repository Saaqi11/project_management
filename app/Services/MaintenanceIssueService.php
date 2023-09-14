<?php

namespace App\Services;

use App\Models\MaintenanceIssue;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class MaintenanceIssueService
{
    use ResponseTrait;

    public function getAll()
    {
        return MaintenanceIssue::where('owner_user_id', auth()->id())->get();
    }

    public function getActiveAll()
    {
        return MaintenanceIssue::where('owner_user_id', auth()->id())->where('status', ACTIVE)->get();
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            MaintenanceIssue::updateOrCreate([
                'id' => $request->id,
                'owner_user_id' => auth()->id(),
            ], [
                'name' => $request->name,
                'owner_user_id' => auth()->id(),
                'status' => $request->status,
            ]);
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
        return MaintenanceIssue::findOrFail($id);
    }

    public function delete($id)
    {
        $issue = MaintenanceIssue::where('owner_user_id', auth()->id())->findOrFail($id);
        $issue->delete();
        return redirect()->back()->with('success', DELETED_SUCCESSFULLY);
    }
}
