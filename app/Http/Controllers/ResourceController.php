<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resource;
use App\Models\ResourceRole;
use Error;
use Illuminate\Support\Facades\DB;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $resources = Resource::all();
        return response([
            'status' => true,
            'resources' => $resources,
        ]);
    }

    public function get_latest_resource(){
        $latest_resource = DB::table('resource')->latest()->first();
        return $latest_resource['resource_id'];
    }

    public function set_new_resource_id(){
        $current_latest_id = $this->get_latest_resource();
        $new_id = "";
        $arr = explode("_", $current_latest_id);
        $number = (int)$arr[1];
        if($number < 10)
        {
            $new_id = "view_0" . (string)($number + 1);
        }
        else if($number >= 10)
        {
            $new_id = "view_" . (string)($number + 1);
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
    public function store(Request $request)
    {
        //
        try{
            $resource = new Resource;
            $resource->resource_name = $request->resource_name;
            $resource->resource_id = (string)$this->set_new_resource_id();
            $resource->save();
            return response([
                'status' => true,
                'resource' => $resource,
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
            $resource = Resource::findOrFail($id);
            return [
                'status' => true,
                'resource' => $resource,
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
        //
        try{
            $resource = Resource::findOrFail($id);
            $resource->update($request->all(), ['upsert' => true]);
            return [
                'status' => true,
                'resource' => $resource,
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
            Resource::destroy($id);
            ResourceRole::where('resource_id', $id)->delete();
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

    public function check_resource($resource_id)
    {
        $resource = Resource::find($resource_id);
        if($resource)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function check_is_create_specific_resource(Request $request){
        $resource_id = $request->resource_id;
        $result = $this->check_resource($resource_id);
        return response([
            'status' => true,
            'result' => $result,
        ]);
    }

    public function create_new_resource(Request $request)
    {
        try{
            $resource_id = $request->resource_id;
            $resource_name = $request->resource_name;
            $resource = new Resource();
            $resource->resource_id = $resource_id;
            $resource->resource_name = $resource_name;
            $resource->save();
            return response([
                'status' => true,
            ]);
        }catch(Error $err)
        {
            return response([
                'status' => false,
            ]);
        }
        
    }
}
