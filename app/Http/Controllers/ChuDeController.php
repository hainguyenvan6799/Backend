<?php

namespace App\Http\Controllers;

use App\Models\ChuDe;
use App\Models\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nullix\CryptoJsAes\CryptoJsAes;
use App\Events\Topic;

class ChuDeController extends Controller
{
    public function __construct()
    {
        $this->key = (string)config('app.key');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try
        {
            $chude = ChuDe::all();
            return response([
                'status' => true,
                'data' => $chude,
            ]);
        }catch(Error $err){
            return response([
                'status' => false,
                'message' => "Something went wrong",
            ]);
        }
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

    public function approve(Request $request)
    {
        try{
            $id = $request->id;
            $chude = ChuDe::where('_id', $id)->first();
            $chude->update(['active' => true], ['upsert' => true]);

            $chude['username'] = $chude->user->name;
            $username = $chude->user->name;

            event(new Topic([
                'isApprove' => true,
                'active' => filter_var(true, FILTER_VALIDATE_BOOLEAN),
                'machude' => $chude->machude,
                'mota' => $chude->mota,
                'noidung' => $chude->noidung,
                'resource_id' => $chude->resource_id,
                'mauser' => $chude->mauser,
                'username' => $username,
                '_id' => $chude->_id,
            ]));
    
            return response([
                'status' => true,
                'data' => $chude,
            ]);
        }catch(Error $err)
        {
            return response([
                'status' => false,
                'data' => "Something went wrong",
            ]);
        }
        
    }

    public function get_latest_chude()
    {
        $chude = DB::table('chude')->latest()->first();
        return $chude['machude'];
    }

    public function set_new_chude()
    {
        $current_latest_id = $this->get_latest_chude();
        $new_id = "";
        $arr = explode("_", $current_latest_id);
        $number = (int)$arr[1];
        if ($number < 9) {
            $new_id = "chude_0" . (string)($number + 1);
        } else if ($number >= 9) {
            $new_id = "chude_" . (string)($number + 1);
        }
        return ($new_id);
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

        try {
            // $arr = [];
            // $a = $request->files;
            // foreach($a as $b)
            // {
            //     foreach($b as $c)
            //     {
            //         array_push($arr, $c->getClientOriginalName());
            //     }
                
            // }
            
            $chude = new ChuDe;
            
            // $can_add = $request->can_add;
            $chude->machude = $this->set_new_chude();
            $chude->mota = $request->mota;
            $chude->noidung = $request->noidung;
            $chude->active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            $chude->resource_id = CryptoJsAes::decrypt($request->resource_id, $this->key);
            $chude->mauser = CryptoJsAes::decrypt($request->mauser, $this->key);
            $chude->save();

            $latest_chude = ChuDe::find($chude->machude);
            // if($can_add)
            // {
            //     $chude->trangthai = true;
            // }
            // else
            // {
            //     $chude->trangthai = false;
            // }
            $latest_chude['username'] = $latest_chude->user->name;
            $username = $latest_chude->user->name;
            $_id = $latest_chude['_id'];

            event(new Topic([
                'isAdd' => true,
                'active' => filter_var($latest_chude->active, FILTER_VALIDATE_BOOLEAN),
                'machude' => $latest_chude->machude,
                'mota' => $latest_chude->mota,
                'noidung' => $latest_chude->noidung,
                'resource_id' => $latest_chude->resource_id,
                'mauser' => $latest_chude->mauser,
                'username' => $username,
                '_id' => $_id,
            ]));
            
            return response([
                'status' => true,
                'data' => $latest_chude,
            ]);
        } catch (Error $err) {
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
    public function show(Request $request)
    {
        //
        try {
            $result = [];
            $resource_id = CryptoJsAes::decrypt($request->resource_id, $this->key);
            $chude = ChuDe::where('resource_id', $resource_id)->get();
            $users = User::all('mauser', 'name');
            // if($chude)
            // {
            //     foreach($chude as $cd)
            //     {
            //         $cd['username'] = $cd->user->name;
            //         array_push($result, $cd);
            //     }
            // }

            return response([
                'status' => true,
                // 'data' => $result,
                'data' => $chude,
                'users' => $users,
                // 'resource_id' => $resource_id
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
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
    public function update($id, Request $request)
    {
        //
        // try{
            $mota = $request->mota;
            $noidung = $request->noidung;
            $active = $request->active;

            $chude = ChuDe::where('_id', $id)->first();
            $chude->mota = $mota;
            $chude->noidung = $noidung;
            $chude->active = filter_var($active, FILTER_VALIDATE_BOOLEAN);
            $chude->save();
    
            $chude['username'] = $chude->user->name;
        
            $username = $chude->user->name;

            event(new Topic([
                'isUpdate' => true,
                'active' => filter_var($chude->active, FILTER_VALIDATE_BOOLEAN),
                'machude' => $chude->machude,
                'mota' => $chude->mota,
                'noidung' => $chude->noidung,
                'resource_id' => $chude->resource_id,
                'mauser' => $chude->mauser,
                'username' => $username,
                '_id' => $chude->_id,
            ]));
        
            return response([
                'status' => true,
                'data' => $chude,
            ]);
        // }catch(Error $err)
        // {
        //     return response([
        //         'status' => false,
        //         'message' => "Something went wrong",
        //     ]);
        // }  
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
            $chude = ChuDe::where('_id', $id);
            $active = $chude->first()->active;
            $resource_id = $chude->first()->resource_id;

            event(new Topic([
                'isDelete' => true,
                'active' => filter_var($active, FILTER_VALIDATE_BOOLEAN),
                '_id' => $id,
                'resource_id' => $resource_id,
            ]));

            $chude->delete();
            
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
