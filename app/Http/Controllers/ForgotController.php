<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotRequest;
use App\Mail\SendMail;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotController extends Controller
{
    //
    public function forgot(ForgotRequest $forgotRequest){
        $email = $forgotRequest->input('email');
        
        $user = User::where('email', $email)->get();

        
        // return response([
        //     'user'=> $user->isEmpty(),
        // ]);
        if($user->isEmpty())
        {
            return response([
                'message'=> 'User does not exists!'
            ]);
        }
        try{
            // làm một cái hàm upsert để không bị trùng

            $token = Str::random(10);
            
            $password_reset = PasswordReset::where('email', $email);
            if($password_reset->get()->isEmpty())
            {
                $password_reset = new PasswordReset();
                $password_reset->email = $email;
                $password_reset->token = $token;
                $password_reset->save();
            }
            else
            {
                $data = [
                    'token' => $token
                ];
                $password_reset->update($data, ['upsert' => true]);
            }
    
            // send email
            $details = [
                'title' => "Reset Password",
                'token' => $token,
            ];
            Mail::to($email)->send(new SendMail($details));

            // response
            return response([
                'state' => 'success',
                'message' => 'Please check your email.',
                'password_reset' => $password_reset,
            ]);
        }catch(\Exception $e)
        {
            return response([
                'state' => 'fail',
                'message' => $e->getMessage()
            ]);
        }
        
    }

    public function xacthucemail_reset_password(Request $request){
        $email = $request->email;
        $maxacnhan = $request->maxacnhan;
        $password_reset = PasswordReset::where('email', $email)->where('token', $maxacnhan);
        if($password_reset->get()->isEmpty())
        {
            return response([
                'status' => 'false',
                'message' => 'Error token',
            ]);
        }
        
        return response([
            'status' => 'success',
            'message' => 'Chuyển trang Nhập lại mật khẩu',
        ]);
    }
}
