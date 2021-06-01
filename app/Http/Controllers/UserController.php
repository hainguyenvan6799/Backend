<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\UsersImport;
use App\Mail\ActiveAccount;
use App\Mail\SendMail;
use App\Mail\SendNotification;
use App\Models\User;
use Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Nullix\CryptoJsAes\CryptoJsAes;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = User::all();

        return response([
            'status' => true,
            'users' => $users,
        ]);
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
        $file = $request->file('file')->store('import');
        $import = new UsersImport();

        $import->import($file);


        $failures = $import->failures();
        $errors = [...$failures, ...$import->getRowsNotImport()];

        // return $failures;

        return response([
            // 'data'=> $import->getRowsNotImport(),
            // 'fail' => $failures,
            'errors' => $errors,
            'usersImported' => $import->getRowsImported(),
        ]);
    }

    public function get_private_files($mauser)
    {

        $files = User::where('mauser', $mauser)->get(['private_files']);

        return response([
            'files' => $files[0],
        ]);
    }

    public function get_teacher_info($magiaovien)
    {
        $giao_vien_cn = User::find($magiaovien);
        if ($giao_vien_cn) {
            $giaovien_cn_info = [
                'tengiangvien' => $giao_vien_cn->name,
                'sodienthoai' => $giao_vien_cn->sdt,
                'email' => $giao_vien_cn->email,
            ];
            return $giaovien_cn_info;
        }
        return "";
    }

    public function get_class_info_of_user($mauser)
    {
        $user = User::findOrFail($mauser);
        $lophoc = $user->lophoc;
        $class_info = [
            'malop' => $lophoc->malop,
            'tenlop' => $lophoc->tenlop,
            'nienkhoa' => $lophoc->nienkhoa,
        ];
        return $class_info;
    }

    public function get_private_files_of_user($mauser)
    {
        $all = [];
        $user = User::findOrFail($mauser);
        $private_files_of_user = $user->private_files;
        if ($private_files_of_user) {
            foreach ($private_files_of_user as $filename) {
                if (Storage::disk('s3')->exists($mauser . '_privatefiles/' . $filename)) {
                    $url = Storage::temporaryUrl(
                        $mauser . '_privatefiles/' . $filename,
                        now()->addMinutes(5)
                    );
                    array_push($all, [
                        'filename' => $filename,
                        'fileurl' => $url,
                    ]);
                }
            }
        }
        return $all;
    }

    public function get_private_info($mauser, $magiaovien)
    {
        try {
            $class_info = $this->get_class_info_of_user($mauser);

            // $giao_vien_cn = User::where('group', 'gv')->where('malop', $user->malop)->first();

            $giaovien_cn_info = $this->get_teacher_info($magiaovien);

            $all = $this->get_private_files_of_user($mauser);

            if ($all) {

                return response()->json([
                    'class_info' => $class_info,
                    'giaovien_info' => $giaovien_cn_info,
                    'private_files_of_user' => $all,
                ]);
            } else {
                return response()->json([
                    'class_info' => $class_info,
                    'giaovien_info' => $giaovien_cn_info,
                ]);
            }

            // return response([
            //     'class_info' => $class_info,
            //     'giaovien_info' => $giaovien_cn_info,
            //     'private_files_of_user' => $private_files_of_user,
            // ]);

        } catch (ModelNotFoundException $e) {
            dd("Khong ton tai mauser nay");
        }
    }

    public function update_private_info(Request $request)
    {
        $mauser = $request->mauser;
        $email = $request->email;
        $sdt = $request->sdt;
        try {
            $user = User::where('mauser', $mauser);
            $data = [
                "email" => $email,
                "sdt" => $sdt,
                "is_updated_info" => 1,
            ];
            $result = $user->update($data, ['upsert' => true]);
            return response([
                'status' => true,
                'message' => "Cập nhật thông tin người dùng thành công.",
                'user' => $data,
                'result' => $result,
            ]);
        } catch (ModelNotFoundException $e) {
            return response([
                'status' => false,
                'message' => "Không tồn tại sinh viên này.",
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
    }

    public function gui_thong_bao(Request $request)
    {
        try {
            $chude = $request->chude;
            $noidung = $request->noidung;
            $malop = $request->malop;
            $users = User::where('malop', $malop)->get();
            $details = [
                'chude' => $chude,
                'noidung' => $noidung,
            ];
            foreach ($users as $u) {

                Mail::to($u->email)->send(new SendNotification($details));
            }

            return response([
                'status' => true,
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
            ]);
        }
    }

    public function check_user_exists($email)
    {
        $user = User::where('email', $email);
        if ($user->get()->isEmpty()) {
            return ['status' => false];
        } else {
            return ['status' => true, 'user' => $user];
        }
    }

    public function get_all_users()
    {
        $users = User::paginate(2);
        return response([
            'users' => $users,
        ]);
    }

    public function get_latest_user()
    {
        $latest_user = DB::table('users')->latest()->first();
        return $latest_user['mauser'];
    }

    public function get_latest_user_code()
    {
        $latest_user = DB::table('users')->latest()->first();
        return $latest_user['code'];
    }

    public function set_new_mauser()
    {
        $current_latest_id = $this->get_latest_user();
        $new_id = "";
        $arr = explode("u", $current_latest_id);
        $number = (int)$arr[1];
        if ($number < 9) {
            $new_id = "u0" . (string)($number + 1);
        } else if ($number >= 9) {
            $new_id = "u" . (string)($number + 1);
        }
        return ($new_id);
    }

    public function set_new_code()
    {
        $current_latest_id = (int)$this->get_latest_user_code();
        $newCode = $current_latest_id + 1;
        return (string)$newCode;
    }

    public function checkExistsTeacherInCLass($malop)
    {
        try {
            $user = User::where('malop', $malop)->where('group', 'gv')->first();
            if ($user) {
                return true;
            } else {
                return false;
            }
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function create_new_user(Request $request)
    {
        try {
            if ($this->checkExistsTeacherInCLass($request->malop)) {
                return response([
                    'status' => false,
                    'message' => 'Đã tồn tại giáo viên trong lớp học này.',
                ]);
            }
            $data = $request->all();
            $data['mauser'] = $this->set_new_mauser();
            $data['active'] = false;
            $data['is_updated_info'] = 0;
            $data['code'] = $this->set_new_code();
            $user = new User($data);
            $user->password = Hash::make('IUHDHCNHCM', ['rounds' => 12]);
            $user->save();

            $username = explode(" ", $request->name);
            $firstName = $username[count($username) - 1];
            $lastName = $username[0];

            $details = [
                'title' => "Active account",
                'email' => $request->email,
            ];
            Mail::to($request->email)->send(new ActiveAccount($details));

            $key = (string)config('app.key');

            $encryptedEmail = CryptoJsAes::encrypt($request->email, $key);
            $encryptedPassword = CryptoJsAes::encrypt('IUHDHCNHCM', $key);
            // $encryptedMauser = CryptoJsAes::encrypt($new_mauser, $key);
            $encryptedMauser = CryptoJsAes::encrypt($data['mauser'], $key);


            return response([
                'status' => true,
                'email' => $encryptedEmail,
                'password' => $encryptedPassword,
                'mauser' => $encryptedMauser,
                'firstName' => $firstName,
                'lastName' => $lastName,
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
            ]);
        }
        // $latest_user = DB::table('users')->latest()->first();
        // $mauser_latest_user = (int)$latest_user['mauser'];
        // $new_mauser = $mauser_latest_user + 1;

    }

    public function update_user_chat_id_using_email(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            $user->user_chat_id = $request->user_chat_id;

            $details = [
                'title' => "Active account",
                'email' => $request->email,
            ];
            Mail::to($request->email)->send(new ActiveAccount($details));

            $user->save();
            return response([
                'status' => true,
                'data' => $user,
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function update_user_chat_id(Request $request)
    {
        $mauser = $request->mauser;
        $user_chat_id = $request->user_chat_id;
        try {
            if ($mauser !== "" && $user_chat_id !== '') {
                $user = User::find($mauser);
                $user->user_chat_id = $user_chat_id;
                $user->save();
                return response([
                    'status' => true,
                    'message' => 'update user chat id',
                ]);
            }
        } catch (Error $e) {
            return response([
                'status' => false,
                'message' => 'can not update user chat id',
            ]);
        }
    }

    public function update_user(Request $request)
    {
        $mauser = $request->mauser;

        try {
            $user = User::findOrFail($mauser);
            if ($request->password !== '') {
                $data_update = $request->all();
                $data_update['password'] = Hash::make($request->password);
            } else {
                $data_update = [...$request->all(), 'password' => Hash::make($user->password)];
            }

            $user->update($data_update, ['upsert' => true]);

            return response([
                'status' => true,
                'message' => 'Chỉnh sửa thành công.',
                'dataupdate' => $data_update,
            ]);
        } catch (Error $e) {
            return response([
                'status' => false,
                'message' => 'Chỉnh sửa thất bại.',
            ]);
        }
    }

    public function delete_user(Request $request)
    {
        $mauser = $request->mauser;
        $key = (string)config('app.key');

        $decrypted = CryptoJsAes::decrypt($mauser, $key);
        try {
            // $user = User::findOrFail($mauser);

            // $data = [
            //     'active' => false
            // ];

            // $user->update($data, ['upsert' => true]);

            User::destroy($decrypted);

            return response([
                'status' => true,
                'message' => 'Xóa thành công',
            ]);
        } catch (Error $e) {
            return response([
                'status' => false,
                'message' => 'Xóa thất bại',
            ]);
        }
        return response([
            'mauser' => ($decrypted),
            'key' => $key
        ]);
    }

    public function active_account(Request $request)
    {
        $email = $request->email;
        $user = User::where('email', $email);
        if ($user->get()->isEmpty()) {
            return response([
                'status' => false,
                'message' => 'Người dùng không tồn tại'
            ]);
        } else {
            $user->update(['active' => true], ['upsert' => true]);
            return response([
                'status' => true,
                'message' => 'Kích hoạt thành công.'
            ]);
        }
    }
}
