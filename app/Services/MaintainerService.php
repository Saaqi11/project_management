<?php

namespace App\Services;

use App\Models\FileManager;
use App\Models\Maintainer;
use App\Models\User;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MaintainerService
{
    use ResponseTrait;

    public function getAllData()
    {
        $maintainer = Maintainer::join('users', 'maintainers.user_id', '=', 'users.id')
            ->leftJoin('file_managers', ['file_managers.origin_id' => 'users.id', 'file_managers.origin_type' => (DB::raw("'App\\\Models\\\User'"))])
            ->join('properties', 'maintainers.property_id', '=', 'properties.id')
            ->where('maintainers.owner_user_id', auth()->id())
            ->select('maintainers.id', 'properties.name as property_name', 'users.first_name', 'users.last_name', 'users.email', 'users.contact_number', 'file_managers.file_name', 'file_managers.folder_name');
        return datatables($maintainer)
            ->addIndexColumn()
            ->addColumn('image', function ($maintainer) {
                return '<div class="tenants-tbl-info-object tbl-info-property-img d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="' . getFileUrl($maintainer->folder_name, $maintainer->file_name) . '"
                                class="rounded avatar-md tbl-user-image"
                                alt="' . $maintainer->file_name . '">
                            </div>
                        </div>';
            })
            ->editColumn('name', function ($maintainer) {
                return $maintainer->first_name . ' ' . $maintainer->last_name;
            })
            ->addColumn('email', function ($maintainer) {
                return $maintainer->email;
            })
            ->addColumn('contact_number', function ($maintainer) {
                return $maintainer->contact_number;
            })
            ->addColumn('property', function ($maintainer) {
                return $maintainer->property_name;
            })
            ->addColumn('action', function ($maintainer) {
                $id = $maintainer->id;
                return '<div class="tbl-action-btns d-inline-flex">
                            <button type="button" class="p-1 tbl-action-btn edit" data-id="' . $id . '" title="Edit"><span class="iconify" data-icon="clarity:note-edit-solid"></span></button>
                            <button onclick="deleteItem(\'' . route('owner.maintainer.delete', $id) . '\', \'allDatatable\')" class="p-1 tbl-action-btn"   title="Delete"><span class="iconify" data-icon="ep:delete-filled"></span></button>
                        </div>';
            })
            ->rawColumns(['image', 'action'])
            ->make(true);
    }

    public function getAll()
    {
        return  Maintainer::query()
            ->join('users', 'maintainers.user_id', '=', 'users.id')
            ->where('maintainers.owner_user_id', auth()->id())
            ->select('maintainers.user_id', 'users.*')
            ->get();
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $id = $request->get('id', '');
            if ($id != '') {
                $maintainer = Maintainer::where('owner_user_id', auth()->id())->findOrFail($request->id);
                $user = User::where('owner_user_id', auth()->id())->findOrFail($maintainer->user_id);
            } else {
                if (!getOwnerLimit(RULES_MAINTAINER) > 0) {
                    throw new Exception('Your Maintainer Limit finished');
                }
                $maintainer = new Maintainer();
                $user = new User();
            }

            // User
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->contact_number = $request->contact_number;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->role = USER_ROLE_MAINTAINER;
            $user->owner_user_id = auth()->id();
            $user->status = ACTIVE;
            $user->save();

            // Maintainer
            $maintainer->user_id = $user->id;
            $maintainer->property_id = $request->property_id;
            $maintainer->owner_user_id = auth()->id();
            $maintainer->save();

            /*File Manager Call upload*/
            if ($request->hasFile('image')) {
                $new_file = FileManager::where('origin_type', 'App\Models\User')->where('origin_id', $user->id)->first();
                if ($new_file) {
                    $new_file->removeFile();
                    $upload = $new_file->updateUpload($new_file->id, 'User', $request->image);
                } else {
                    $new_file = new FileManager();
                    $upload = $new_file->upload('User', $request->image);
                }

                if ($upload['status']) {
                    $upload['file']->origin_id = $user->id;
                    $upload['file']->origin_type = "App\Models\User";
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
        return Maintainer::findOrFail($id);
    }

    public function getInfo($id)
    {
        $maintainer = Maintainer::query()
            ->join('users', 'maintainers.user_id', '=', 'users.id')
            ->select('maintainers.id', 'maintainers.user_id', 'maintainers.property_id', 'users.first_name', 'users.last_name', 'users.email', 'users.contact_number')
            ->where('maintainers.owner_user_id', auth()->id())
            ->findOrFail($id);

        $maintainer->image = $maintainer->user->image;
        return $maintainer;
    }

    public function deleteById($id)
    {
        DB::beginTransaction();
        try {
            $information =  Maintainer::where('owner_user_id', auth()->id())->findOrFail($id);
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
