<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use App\Models\LopHoc;
use App\Http\Controllers\UserController;
use Error;

class ChatController extends Controller
{
    public $userController;
    public function __construct()
    {
        $this->userController = new UserController;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function create_chat_room()
    {
    }

    public function get_users_in_class($class_name)
    {
        try {
            $lophoc = LopHoc::where('tenlop', $class_name)->first();
            $users_not_attend_room_chat = $lophoc->users->where('room_chat_id', null)->pluck('email');

            return response([
                'status' => true,
                'users' => $users_not_attend_room_chat,
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function check_edit_users($email, $class_name)
    {
        $lophoc = LopHoc::where('tenlop', $class_name)->first();
        $malophoc = $lophoc->malop;
        $user = User::where('email', $email)->where('malop', $malophoc)->where('group', 'gv')->get();
        return $user->isEmpty() ? response([
            'status' => false,
        ]) : response([
            'status' => true,
        ]);
    }

    public function update_room_chat_id_for_user(Request $request)
    {
        $email = $request['email'];
        $room_chat_id = $request['room_chat_id'];

        // return response([
        //     'email' => $email,
        //     'room_chat_id' => $room_chat_id,
        // ]);
        $user = User::where('email', $email);

        if ($user->get()->isEmpty()) {
            return response([
                'message' => 'fail',
            ]);
        } else {
            $data = [
                'room_chat_id' => (string)$room_chat_id,
            ];
            $user->update($data, ['upsert' => true]);

            return response([
                'message' => 'success',
            ]);
        }
    }

    public function remove_member_in_chat(Request $request)
    {
        $email_to_be_delete = $request->email;
        // $user = User::where('email', $email_to_be_delete);
        // if($user->get()->isEmpty())
        // {
        //     return response([
        //         'status' => 'fail',
        //         'message' => 'Người dùng không tồn tại',
        //     ]);
        // }
        // else
        // {
        //     $data_update = [
        //         'room_chat_id' => null,
        //     ];

        //     $user->update($data_update, ['upsert' => true]);
        //     return response([
        //         'status' => 'success',
        //         'message' => 'Xóa thành viên thành công.',
        //         'email' => $email_to_be_delete,
        //     ]);
        // }
        $result = $this->userController->check_user_exists($email_to_be_delete);
        $status = $result['status'];
        if (!$status) {
            return response([
                'status' => false,
                'message' => 'Người dùng không tồn tại',
            ]);
        } else {
            $data_update = [
                'room_chat_id' => null,
            ];

            $result['user']->update($data_update, ['upsert' => true]);
            return response([
                'status' => true,
                'message' => 'Xóa thành viên thành công.',
            ]);
        }
    }
}
