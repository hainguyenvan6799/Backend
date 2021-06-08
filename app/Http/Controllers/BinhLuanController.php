<?php

namespace App\Http\Controllers;

use App\Events\Notification;
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
            $urls = [];
            $machude = $request->machude;
            $folderName = "chude" . $machude . "_comment";
            $comments = BinhLuan::where('machude', $machude)->get();

            $users = User::all('mauser', 'name');

            if (count($comments) > 0) {
                foreach ($comments as $comment) {
                    if ($comment->files) {
                        foreach ($comment->files as $fileName) {
                            $urls[] = $this->awsController->get_file_aws_base($folderName, $fileName);
                        }
                    }
                    $comment['urls'] = $urls;
                    $urls = [];
                }
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
        if ($request->file("files")) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $arrFiles[] = time() . $file->getClientOriginalName();
                $this->awsController->uploadfileaws_base($file, $folderName);
            }
            $comment->files = $arrFiles;
        }

        $comment->save();

        event(new Notification(
            [
                'username' => $comment->user->name,
                'tenchude' => $comment->chude->mota,
            ]
        ));
        return response([
            'status' => true,
            'binhluan' => $comment,
        ]);
        // } catch (Error $err) {
        //     return response([
        //         'status' => false,
        //         'message' => "Something went wrong",
        //     ]);
        // }
    }
}
