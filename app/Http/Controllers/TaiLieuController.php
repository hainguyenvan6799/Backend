<?php

namespace App\Http\Controllers;

use App\Events\MyEvent;
use App\Models\TaiLieu;
use App\Models\User;
use Error;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Nullix\CryptoJsAes\CryptoJsAes;

class TaiLieuController extends Controller
{
    public function __construct()
    {
        $this->awsController = new AwsController;
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
        try {
            $tailieu = TaiLieu::all();
            return response([
                'status' => true,
                'data' => $tailieu,
            ]);
        } catch (Error $err) {
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
        try {
            $id = $request->id;
            $tailieu = TaiLieu::where('_id', $id)->first();
            $username = $tailieu->user->name;

            // $tailieu['url'] = $this->awsController->get_url_file_aws_base($tailieu->resource_id, $tailieu->file);
            event(new MyEvent(
                [
                    'isApprove' => true,
                    'active' => filter_var(true, FILTER_VALIDATE_BOOLEAN),
                    'file' => $tailieu->file,
                    'matailieu' => $tailieu->matailieu,
                    'mauser' => $tailieu->mauser,
                    'resource_id' => $tailieu->resource_id,
                    'tentailieu' => $tailieu->tentailieu,
                    '_id' => $tailieu->_id,
                    'url' => $this->awsController->get_url_file_aws_base($tailieu->resource_id, $tailieu->file),
                    'username' => $username,
                ]
            ));

            $tailieu->update(['active' => true], ['upsert' => true]);
            return response([
                'status' => true,
                // 'data' => $tailieu,
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'data' => "Something went wrong",
            ]);
        }
    }

    public function get_latest_tailieu()
    {
        $tailieu = DB::table('tailieu')->latest()->first();
        return $tailieu['matailieu'];
    }

    public function set_new_tailieu()
    {
        $current_latest_id = $this->get_latest_tailieu();
        $new_id = "";
        $arr = explode("_", $current_latest_id);
        $number = (int)$arr[1];
        if ($number < 9) {
            $new_id = "tailieu_0" . (string)($number + 1);
        } else if ($number >= 9) {
            $new_id = "tailieu_" . (string)($number + 1);
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
            $tailieu = new TaiLieu;
            $tailieu->matailieu = $this->set_new_tailieu();
            $tailieu->tentailieu = $request->tentailieu;
            $tailieu->active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            $tailieu->mauser = $request->mauser;
            $file = $request->file('file');
            $tailieu->file = time() . $file->getClientOriginalName();
            $tailieu->resource_id = "tl_" . $request->class_id;
            $folderName = "tl_" . $request->class_id . '/';
            // tại đây thực hiện đẩy file lên aws
            $this->awsController->uploadfileaws_base($file, $folderName);


            $tailieu->save();

            $username = User::find($request->mauser)->name;

            $latest_tailieu = DB::table('tailieu')->latest()->first();
            $_id = $latest_tailieu['_id'];

            // $latest_tailieu['url'] = $this->awsController->get_url_file_aws_base($latest_tailieu['resource_id'], $latest_tailieu['file']);
            // $latest_tailieu['username'] = $username;
            // $latest_tailieu['isAdd'] = true;
            event(new MyEvent([
                'isAdd' => true,
                'active' => filter_var($request->active, FILTER_VALIDATE_BOOLEAN),
                'file' => $tailieu->file,
                'matailieu' => $tailieu->matailieu,
                'mauser' => $tailieu->mauser,
                'resource_id' => $tailieu->resource_id,
                'tentailieu' => $tailieu->tentailieu,
                '_id' => $_id,
                'url' => $this->awsController->get_url_file_aws_base($tailieu->resource_id, $tailieu->file),
                'username' => $username,
            ]));
            return response([
                'status' => true,
                // 'data' => $latest_tailieu,
                // 'event' => $event,
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
        // try {
        $resource_id = CryptoJsAes::decrypt($request->resource_id, $this->key);
        // $tailieu = TaiLieu::all();
        // foreach ($tailieu as $tl) {
        //     $seperate_resource_id = explode("_", $tl->resource_id);
        //     $class_id = $seperate_resource_id[1];
        //     if ($class_id === $resource_id) {
        //         $tl['username'] = $tl->user->name;
        //         $tl['url'] = $this->awsController->get_url_file_aws_base($tl->resource_id, $tl->file);
        //         array_push($result, $tl);
        //     }
        // }

        $tailieu = TaiLieu::where('resource_id', $resource_id)->get();
        // ->all();
        foreach ($tailieu as $tl) {
            $result[] = [
                'matailieu' => $tl->matailieu,
                'url' => $this->awsController->get_url_file_aws_base($tl->resource_id, $tl->file),
            ];
        }

        $users = User::all('mauser', 'name');
        // $data = array_map(function($tl){
        //     $tl['username'] = $tl->user->name;
        //     $tl['url'] = $this->awsController->get_url_file_aws_base($tl->resource_id, $tl->file);
        //     return $tl;
        // }, $tailieu);

        return response([
            'status' => true,
            'data' => $tailieu,
            'urls' => $result,
            'users' => $users,
        ]);
        // } catch (Error $err) {
        //     return response([
        //         'status' => false,
        //         'message' => 'Something went wrong',
        //     ]);
        // }
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
    public function update(Request $request)
    {
        //
        // try{
        $tentailieu = $request->tentailieu;
        $_id = $request->_id;
        $file = $request->file;
        $active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);

        $tailieu = TaiLieu::where('_id', $_id)->first();
        $tailieu->tentailieu = $tentailieu;
        $tailieu->active = $active;
        if ($file) {
            $folderName = $tailieu->resource_id . '/';

            // xóa file cũ trên aws
            $this->awsController->delete_file_from_aws($tailieu->file, $folderName);

            $fileName = $file->getClientOriginalName();
            $tailieu->file = time() . $fileName;

            $folderName = $tailieu->resource_id . '/';
            // tại đây thực hiện đẩy file lên aws
            $this->awsController->uploadfileaws_base($file, $folderName);
        }
        $tailieu->save();
        // $tailieu['url'] = $this->awsController->get_url_file_aws_base($tailieu->resource_id, $tailieu->file);
        $username = $tailieu->user->name;
        // $tailieu['username'] = $username;
        event(new MyEvent([
            'isUpdate' => true,
            'active' => filter_var($tailieu->active, FILTER_VALIDATE_BOOLEAN),
            'file' => $tailieu->file,
            'matailieu' => $tailieu->matailieu,
            'mauser' => $tailieu->mauser,
            'resource_id' => $tailieu->resource_id,
            'tentailieu' => $tailieu->tentailieu,
            '_id' => $tailieu->_id,
            'url' => $this->awsController->get_url_file_aws_base($tailieu->resource_id, $tailieu->file),
            'username' => $username,
        ]));
        return response([
            'status' => true,
            // 'data' => $tailieu,
        ]);
        // }catch(Error $err)
        // {
        //     return response([
        //         'status' => false,
        //         'message' => "Something went wrong",
        //         'request' => $request->all(),
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
        // not using primary key to delete
        try {
            $tailieu = TaiLieu::where('_id', $id);
            $active = $tailieu->first()->active;
            $resource_id = $tailieu->first()->resource_id;
            event(new MyEvent([
                'isDelete' => true,
                '_id' => $id,
                'active' => filter_var($active, FILTER_VALIDATE_BOOLEAN),
                'resource_id' => $resource_id,
            ]));
            $tailieu->delete();

            return response([
                'status' => true,
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
            ]);
        }
    }
}
