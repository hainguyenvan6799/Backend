<?php

namespace App\Http\Controllers;

use App\Models\BinhLuan;
use App\Models\User;
use Error;
use Illuminate\Http\Request;

class BinhLuanController extends Controller
{
    //

    public function __construct()
    {
        $this->awsController = new AwsController;
    }

    public function xembinhluan(Request $request)
    {
        try {
            $machude = $request->machude;
            $comments = BinhLuan::where('machude', $machude)->get();
            $users = User::all('mauser', 'name');

            if (count($comments) > 0) {
                return response([
                    'status' => true,
                    'comments' => $comments,
                    'users' => $users,
                ]);
            }
            return response([
                'status' => false,
                'comments' => [],
                'users' => [],
            ]);
        } catch (Error $error) {
            return response([
                'status' => false,
                'message' => "Không tồn tại mã chủ đề.",
            ]);
        }
    }

    public function create_a_comment(Request $request)
    {
        // try {

        $machude = $request->machude;
        $folderName = "chude" . $machude . "_comment/";
        $mauser = $request->mauser;
        $noidung = $request->noidung;

        $comment = new BinhLuan();
        $comment->machude = $machude;
        $comment->mauser = $mauser;
        $comment->noidungbinhluan = $noidung;
        // $arr = [];
        // $arr_url = [];
        // $a = $request->files;
        // if ($a) {
        //     foreach ($a as $b) {
        //         foreach ($b as $file) {
        //             $this->awsController->uploadfileaws_base($file, $folderName);
        //             array_push($arr, time() . $file->getClientOriginalName());
        //             $url = $this->awsController->get_url_file_aws_base($folderName, time() . $file->getClientOriginalName());
        //             array_push($arr_url, [
        //                 'fileName' => time() . $file->getClientOriginalName(),
        //                 'fileUrl' => $url,
        //             ]);
        //         }
        //     }
        //     $comment->files = $arr;
        // }

        $comment->save();
        return response([
            'status' => true,
            'binhluan' => $comment,
            // 'arr_url' => $arr_url,
        ]);
        // } catch (Error $err) {
        //     return response([
        //         'status' => false,
        //         'message' => "Something went wrong",
        //     ]);
        // }
    }
}
