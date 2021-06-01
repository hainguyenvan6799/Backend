<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceRole;
use App\Models\Role;
use Error;
use Illuminate\Http\Request;
use Mockery\Undefined;

class RolesResourcesController extends Controller
{
    //
    public function get_all_roles_resources()
    {
        // $records = ResourceRole::all()->all();

        // $newdata = array_map(function($item){
        //     return [
        //         '_id' => $item->_id,
        //         'role_name' => $item->specific_role->role_name,
        //         'resource_name' => $item->specific_resource->resource_name,
        //         'can_read' => $item->can_read,
        //         'can_add' => $item->can_add,
        //         'can_update' => $item->can_update,
        //         'can_delete' => $item->can_delete,

        //     ];
        // }, $records);

        $resource_role = ResourceRole::all();
        $resources = Resource::all('resource_id', 'resource_name');
        $roles = Role::all('role_id', 'role_name');

        return response([
            'status' => true,
            // 'data' => $newdata,
            'data' => $resource_role,
            'resources' => $resources,
            'roles' => $roles,
        ]);
    }

    public function check_roles_resources_exist($role_id, $resource_id)
    {
        $check = ResourceRole::where('role_id', $role_id)->where('resource_id', $resource_id)->first();
        if ($check) {
            return [
                'status' => true,
                '_id' => $check->_id,
            ];
        } else {
            return [
                'status' => false,
            ];
        }
    }

    public function store(Request $request)
    {
        try {
            $role_id = $request->role_id;
            $resource_id = $request->resource_id;
            $can_read =  $request->can_read === null ? false : true;
            $can_add =  $request->can_add === null ? false : true;
            $can_update =  $request->can_update === null ? false : true;
            $can_delete =  $request->can_delete === null ? false : true;

            // $result = $this->check_roles_resources_exist($role_id, $resource_id);
            // if($result['status'] === true)
            // {
            //     $var = ResourceRole::findOrFail($result['_id']);
            //     $data = [
            //         'can_read' => $can_read,
            //         'can_add' => $can_add,
            //         'can_update' => $can_update,
            //         'can_delete' => $can_delete,
            //     ];

            //     $var->update($data, ['upsert' => true]);

            //     return response([
            //         'status' => true,
            //         'message' => 'Đã tồn tại. Đã thực hiện cập nhật.',
            //     ]);
            // }

            $role_resource = new ResourceRole;
            $role_resource->role_id = $role_id;
            $role_resource->resource_id = $resource_id;
            $role_resource->can_read = $can_read;
            $role_resource->can_add = $can_add;
            $role_resource->can_update = $can_update;
            $role_resource->can_delete = $can_delete;
            $role_resource->save();

            return response([
                'status' => true,
                'data' => $role_resource,
                'message' => "Thêm thành công.",

            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function destroy($id)
    {
        //
        try {
            ResourceRole::destroy($id);
            return response([
                'status' => true,
                'id' => $id,
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
