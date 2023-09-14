<?php

namespace App\Services;

use App\Models\FileManager;
use App\Models\Information;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class InformationService
{
    use ResponseTrait;

    public function getAllData()
    {
        $information = Information::query()
            ->join('properties', 'information.property_id', '=', 'properties.id')
            ->where('information.owner_user_id', auth()->id())
            ->select('information.id', 'information.name', 'information.distance', 'information.additional_information', 'information.contact_number', 'properties.name as property_name');
        return datatables($information)
            ->addIndexColumn()
            ->addColumn('image', function ($data) {
                return '<div class="tenants-tbl-info-object tbl-info-property-img d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="' . $data->image . '"
                                class="rounded avatar-md tbl-user-image"
                                alt="">
                            </div>
                        </div>';
            })
            ->addColumn('property', function ($data) {
                return $data->property_name;
            })
            ->addColumn('action', function ($data) {
                $id = $data->id;
                return '<div class="tbl-action-btns d-inline-flex">
                            <button type="button" class="p-1 tbl-action-btn view" data-id="' . $id . '" title="View">
                                <span class="iconify" data-icon="carbon:view-filled"></span>
                            </button>
                            <button type="button" class="p-1 tbl-action-btn edit" data-id="' . $id . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>
                            <button onclick="deleteItem(\'' . route('owner.information.delete', $id) . '\', \'allDatatable\')" class="p-1 tbl-action-btn"   title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>
                        </div>';
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }


    public function store($request)
    {
        DB::beginTransaction();
        try {
            $information =  Information::updateOrCreate([
                'id' => $request->id,
                'owner_user_id' => auth()->id()
            ], [
                'property_id' => $request->property_id,
                'owner_user_id' => auth()->id(),
                'name' => $request->name,
                'distance' => $request->distance,
                'additional_information' => $request->additional_information,
                'contact_number' => $request->contact_number,
            ]);

            /*File Manager Call upload*/
            if ($request->hasFile('image')) {
                $new_file = FileManager::where('origin_type', 'App\Models\Information')->where('origin_id', $information->id)->first();

                if ($new_file) {
                    $new_file->removeFile();
                    $upload = $new_file->updateUpload($new_file->id, 'Information', $request->image);
                } else {
                    $new_file = new FileManager();
                    $upload = $new_file->upload('Information', $request->image);
                }

                if ($upload['status']) {
                    $upload['file']->origin_id = $information->id;
                    $upload['file']->origin_type = "App\Models\Information";
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

    public function getById($id)
    {
        return Information::findOrFail($id);
    }

    public function getInfo($id)
    {
        return Information::query()
            ->join('properties', 'information.property_id', '=', 'properties.id')
            ->select('properties.name as property_name', 'information.*')
            ->findOrFail($id);
    }

    public function getByPropertyId($id)
    {
        return Information::query()->where('owner_user_id', auth()->user()->owner_user_id)->where('property_id', $id)->get();
    }

    public function deleteById($id)
    {
        DB::beginTransaction();
        try {
            $information =  Information::where('owner_user_id', auth()->id())->findOrFail($id);
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
