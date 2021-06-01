<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Models\ResourceRole;
use App\Models\Role;
use App\Models\UsersRoles;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return response([
            'status' => true,
            'roles' => $roles,
        ]);
    }

    public function get_latest_role(){
        $latest_role = DB::table('roles')->latest()->first();
        return $latest_role['role_id'];
    }

    public function set_new_role_id(){
        $current_latest_id = $this->get_latest_role();
        $new_id = "";
        $arr = explode("r", $current_latest_id);
        $number = (int)$arr[1];
        if($number < 9)
        {
            $new_id = "r0" . (string)($number + 1);
        }
        else if($number >= 9)
        {
            $new_id = "r" . (string)($number + 1);
        }
        return ($new_id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        try{
            $role = new Role;
            $role->role_name = $request->role_name;
            $role->active = $request->active;
            $role->role_id = (string)$this->set_new_role_id();
            $role->save();
            return response([
                'status' => true,
                'role' => $role,
            ]);
        }catch(Error $err){
            return response([
                'status' => false,
                'message' => "Something went wrong",
            ]);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try{
            $role = Role::findOrFail($id);
            return [
                'status' => true,
                'role' => $role,
            ];
        }catch(Error $err){
            return [
                'status' => false,
                'message' => 'Something went wrong',
            ];
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $role = Role::findOrFail($id);
            $role->update($request->all(), ['upsert' => true]);
            return [
                'status' => true,
                'role' => $role,
            ];
        }catch(Error $err)
        {
            return [
                'status' => false,
                'message' => 'Something went wrong',
            ];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        try{
            Role::destroy($id);
            UsersRoles::where('role_id', $id)->delete();
            ResourceRole::where('role_id', $id)->delete();
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
