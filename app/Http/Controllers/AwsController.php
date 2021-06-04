<?php

namespace App\Http\Controllers;

use App\Models\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AwsController extends Controller
{
    //
    public function delete_file_from_aws($filename, $foldername)
    {
        try{
            Storage::disk('s3')->delete($foldername . $filename);
            return true;
        }
        catch(Error $err)
        {
            return false;
        }
        
    }

    public function update_private_files_of_user(Request $request){
        try
        {
            $filename = $request->filename;
            $mauser = $request->mauser;
            $foldername_aws = $mauser . '_privatefiles/';
            $result = $this->delete_file_from_aws($filename, $foldername_aws);
            if($result)
            {
                $user = User::where('mauser', $mauser);
                $user->pull('private_files', $filename);
                return response([
                    'status' => true,
                    'message' => 'Cập nhật tệp riêng tư thành công',
                ]);
            }
        }catch(Error $err)
        {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
        

    }

    public function uploadfileaws_base($file, $foldername)
    {
        $filename = time() . $file->getClientOriginalName();
        $result = $file->storeAs($foldername, $filename, 's3');
        return $result;
    }

    public function upload_file_aws(Request $request)
    {
        try {
            $foldername = $request->folder_name;
            $email = $request->email;
            $mauser = $request->mauser;

            $user = User::where('email', $email)->first();
            $files = $request->file('files');
            foreach ($files as $file) {
                $filename = time() . $file->getClientOriginalName();
                $result = $file->storeAs($foldername, $filename, 's3');
                $user->push('private_files', $filename);
            }


            $get_file_from_aws = $this->get_file_from_aws($mauser);

            return response([
                'status' => true,
                // 'result' => $result,
                'result' => $get_file_from_aws,

            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function get_url_file_aws_base($folderName, $fileName)
    {
        $exactpath = $folderName . '/' . $fileName;
        if (Storage::disk('s3')->exists($exactpath))
        {
            $url = Storage::temporaryUrl(
                $exactpath,
                now()->addMinutes(5)
            );
            
            return $url;
        }
        else
        {
            return "Không xác định";
        }
    }

    public function get_file_aws_base($folderName, $fileName)
    {
        $exactpath = $folderName . '/' . $fileName;
        if (Storage::disk('s3')->exists($exactpath))
        {
            $url = Storage::temporaryUrl(
                $exactpath,
                now()->addMinutes(5)
            );
            
            $item = [
                'filename' => $fileName,
                'fileurl' => $url,
            ];
            return $item;
        }
        else
        {
            $item = [
                'filename' => "Không xác định",
                'fileurl' => "Không xác định",
            ];
            return $item;
        }
    }

    public function get_file_from_aws($mauser)
    {
        $all = [];
        $files = User::where('mauser', $mauser)->get(['private_files']);
        $folderName = $mauser . '_privatefiles';
        foreach ($files as $file) {
            foreach ($file->private_files as $fileName) {
                // if (Storage::disk('s3')->exists($mauser .  '_privatefiles/' . $file_name)) {
                //     $url = Storage::temporaryUrl(
                //         $file_name,
                //         now()->addMinutes(5)
                //     );
                //     array_push($all, [
                //         'filename' => $file_name,
                //         'fileurl' => $url,
                //     ]);
                // }
                $item = $this->get_file_aws_base($folderName, $fileName);
                array_push($all, $item);
            }
        }
        // dd($all);
        return response([
            'files' => $all,
        ]);

    }
}
