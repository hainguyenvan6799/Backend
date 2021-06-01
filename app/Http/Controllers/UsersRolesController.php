<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\UsersRoles;
use Error;

class UsersRolesController extends Controller
{
    //
    // public function get_all_users_roles(){
    //     // $users = User::all();
    //     // $roles = Role::all();
    //     $records = UsersRoles::all();
    //     $custom_records = [];
    //     foreach($records as $rec)
    //     {
    //         array_push($custom_records, [
    //             '_id' => $rec->_id,
    //             // 'mauser' => $rec->user->mauser,
    //             'name' => $rec->user->name,
    //             'role' => $rec->role->role_name,
    //         ]);
    //     }
    //     return response([
    //         'status' => true,
    //         'message' => 'get all datas users roles',
    //         'data' => $custom_records,
    //         // 'users' => $users, 
    //         // 'roles' => $roles,
    //     ]);
    // }

    public function get_all_users_roles(){
        try
        {
            // $records = UsersRoles::all()->all();

            // $newdata = array_map(function($item){
            //     return [
            //         '_id' => $item->_id,
            //         'name' => $item->user->name,
            //         'role' => $item->role->role_name,
            //     ];
            // }, $records);

            $users_roles = UsersRoles::all();
            $users = User::all('mauser', 'name');
            $roles = Role::all('role_id', 'role_name');

    
            return response([
                'status'=> true,
                // 'data' => $newdata,
                'data' => $users_roles,
                'users' => $users,
                'roles' => $roles,
                // 'message' => 'get all datas users roles'
            ]);
        }catch(Error $err)
        {
            return response([
                'status'=> false,
                'message' => 'Something went wrong.',
            ]);
        }
        
    }

    public function store(Request $request)
    {
        try
        {
            $userrole = new UsersRoles;
            $userrole->mauser = $request->mauser;
            $userrole->role_id = $request->role_id;
            $userrole->save();
            return response([
                'status' => true,
                'data' => $userrole,
            ]);
        }catch(Error $err)
        {
            return response([
                'status' => false,
                'message' => 'Something went wrong.',
            ]);
        }
        

    }

    public function destroy($id)
    {
        //
        try{
            UsersRoles::destroy($id);
            return response([
                'status' => true,
                'id' => $id,
            ]);
        }
        catch(Error $err)
        {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
        
    }
}
