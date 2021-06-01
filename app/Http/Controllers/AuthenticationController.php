<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController;
use Error;
use Nullix\CryptoJsAes\CryptoJsAes;


class AuthenticationController extends Controller
{
    protected $userController;
    public function __construct()
    {
        $this->userController = new UserController;
        $this->key = (string)config('app.key');
        $this->privateKey =  substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 1, 10);
    }

    public function mahoadulieu($value)
    {
        return CryptoJsAes::encrypt($value, $this->key);
    }

    public function giaimadulieu($value)
    {
        return CryptoJsAes::decrypt($value, $this->key);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $emailDecrypted = $this->giaimadulieu($request->email);
            $passwordDecrypted = $this->giaimadulieu($request->password);

            if (Auth::attempt(['email' => $emailDecrypted, 'password' => $passwordDecrypted])) {
                /** @var User $user */
                $user = Auth::user();
                $token = $user->createToken('app')->accessToken;
                $giaovien = User::where('group', 'gv')->where('malop', $user->malop)->first();
                if ($giaovien) {
                    $magiaovien = $giaovien->mauser;
                } else {
                    $magiaovien = null;
                }
                // $users = User::all('mauser', 'malop', 'group');

                return response([
                    'status' => true,
                    'token' => $token,
                    'data' => $this->mahoadulieu($user),
                    'magiaovien' => $this->mahoadulieu($magiaovien),
                    // 'users' => $users,
                ]);
                // if ($user->active === true) {
                //     $token = $user->createToken('app')->accessToken;
                //     return response([
                //         'status' => 'success',
                //         'token' => $token,
                //         'user' => $this->mahoadulieu($user),
                //         'magiaovien' => $this->mahoadulieu($magiaovien),
                //     ]);
                // } else {
                //     return response([
                //         'status' => 'pending',
                //         'message' => 'Tài khoản chưa được kích hoạt, vui lòng liên hệ người quản trị.',
                //     ]);
                // }
            } else {
                return response([
                    'status' => false,
                    'message' => "email hoặc password chưa đúng.",
                ]);
            }
        } catch (\Exception $exception) {
            return response([
                'status' => false,
                'message' => "Something went wrong",
            ]);
        }
    }

    public function current_user()
    {
        try {
            $user = Auth::user();
            if ($user->name !== "") {
                $giaovien = User::where('group', 'gv')->where('malop', $user->malop)->first();
                // $users = User::all('mauser', 'malop', 'group');
                $magiaovien = $giaovien->mauser;
                return response([
                    'status' => "success",
                    "message" => "get user successfully",
                    "user" => $user,
                    'magiaovien' => $this->mahoadulieu($magiaovien),
                    // 'users' => $users,
                ]);
            }
        } catch (Error $error) {
            return response([
                'status' => 'false',
                'message' => 'Không tìm thấy người dùng',
            ]);
        }
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            echo "Bạn đã Logout thành công.";
        }
    }

    public function check_old_password_of_user($email, $old_password)
    {

        $result = $this->userController->check_user_exists($email);
        $status = $result['status'];
        if ($status) {
            $user = $result['user']->first();
            if (Hash::check($old_password, $user->password)) {
                return true;
            }
            return false;
        } else {
            return response([
                'message' => "Không tồn tại tài khoản này.",
            ]);
        }
        // $user = User::where('email', $email)->first();
    }

    public function change_password(Request $request)
    {
        $email = $request->email;
        $request_user = $request->request_from_user;

        $is_exists_user = $this->exists_user($email);
        if ($is_exists_user['status'] == false) {
            return response([
                'status' => 'False',
                'message' => $is_exists_user['message'],
            ]);
        } else {
            if ($request_user === 'change-password-after-login') {
                // check old password is true
                $old_password = $request->old_password;
                if ($this->check_old_password_of_user($email, $old_password) === false) {
                    return response([
                        "status" => 'false',
                        "message" => "Mật khẩu cũ không chính xác",
                    ]);
                }
            }
            $password = $this->validate_data($request->password);
            $data =
                [
                    'password' => $password,
                ];
            $is_exists_user['user']->update($data, ['upsert' => true]);
            return response(
                [
                    'status' => 'Success',
                    'message' => 'Change password successfully',
                ]
            );
        }
    }

    public function validate_data($data)
    {
        if ($data !== "") {
            return Hash::make($data);
        } else {
            return response([
                'error' => 'Data validate can not be empty.',
            ]);
        }
    }

    //check user exists in database base on email
    public function exists_user($email)
    {
        if ($email != "") {

            $user = User::where('email', $email);
            if ($user->get()->isEmpty()) {
                return
                    [
                        'status' => false,
                        'message' => 'Email not exists',
                    ];
            } else {
                return
                    [
                        'status' => true,
                        'user' => $user,
                    ];
            }
        } else {
            return
                [
                    'status' => false,
                    'message' => 'Email can not be empty',
                ];
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

    public function encRSA($M)
    {
        $data[0] = 1;
        for ($i = 0; $i < 35; $i++) {
            $rest[$i] = pow($M, 1) % 119;
            if ($data[$i] > 119) {
                $data[$i + 1] = $data[$i] * $rest[$i] % 119;
            } else {
                $data[$i + 1] = $data[$i] * $rest[$i];
            }
        }
        $get = $data[35] % 119;
        return $get;
    }

    public function decRSA($E)
    {
        $data[0] = 1;
        for ($i = 0; $i < 11; $i++) {
            $rest[$i] = pow($E, 1) % 119;
            if ($data[$i] > 119) {
                $data[$i + 1] = $data[$i] * $rest[$i] % 119;
            } else {
                $data[$i + 1] = $data[$i] * $rest[$i];
            }
        }
        $get = $data[11] % 119;
        return $get;
    }

    public function abc()
    {
        $b = $this->encRSA(1564);
        $c = $this->decRSA($b);
        echo $b . ' ' . $c;
    }

    public function testRSA()
    {
        $config = array(
            "digest_alg" => "sha512",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        );

        // Create the private and public key
        $res = openssl_pkey_new($config);

        // Extract the private key from $res to $privateKey
        openssl_pkey_export($res, $privateKey);

        // Extract the public key from $res to $pubKey
        $pubKey = openssl_pkey_get_details($res);
        $publicKey = $pubKey['key'];
        echo $publicKey;
    }

    public function gcd($a, $b)
    {
        while ($b != 0) {
            $temp = $a % $b;
            $a = $b;
            $b = $temp;
        }
        return $a;
    }

    public function multiplicativeInverse($a, $b)
    {
        $x = 0;
        $y = 1;
        $lx = 1;
        $ly = 0;
        $oa = $a;
        $ob = $b;
        while ($b != 0) {
            $q = floor($a / $b);
            [$a, $b] = [$b, $a % $b];
            [$x, $lx] = [($lx - ($q * $x)), $x];
            [$y, $ly] = [($ly - ($q * $y)), $y];
        }
        if ($lx < 0) {
            $lx += $ob;
        }
        if ($ly < 0) {
            $ly += $oa;
        }
        return $lx;
    }

    public function generatePrime($keysize)
    {
        while (true) {
            $num = rand(pow(2, ($keysize - 1)), pow(2, ($keysize)));
            if ($this->isPrime($num)) {
                return $num;
            }
        }
    }

    public function isPrime($num)
    {
        if ($num < 2) {
            return false; # 0, 1, and negative numbers are not prime
        }
        $lowPrimes = [
            2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89,
            97, 101, 103, 107, 109, 113, 127, 131, 137, 139, 149, 151, 157, 163, 167, 173, 179, 181, 191,
            193, 197, 199, 211, 223, 227, 229, 233, 239, 241, 251, 257, 263, 269, 271, 277, 281, 283, 293,
            307, 311, 313, 317, 331, 337, 347, 349, 353, 359, 367, 373, 379, 383, 389, 397, 401, 409, 419,
            421, 431, 433, 439, 443, 449, 457, 461, 463, 467, 479, 487, 491, 499, 503, 509, 521, 523, 541,
            547, 557, 563, 569, 571, 577, 587, 593, 599, 601, 607, 613, 617, 619, 631, 641, 643, 647, 653,
            659, 661, 673, 677, 683, 691, 701, 709, 719, 727, 733, 739, 743, 751, 757, 761, 769, 773, 787,
            797, 809, 811, 821, 823, 827, 829, 839, 853, 857, 859, 863, 877, 881, 883, 887, 907, 911, 919,
            929, 937, 941, 947, 953, 967, 971, 977, 983, 991, 997
        ];

        if (in_array($num, $lowPrimes)) {
            return true;
        }

        foreach ($lowPrimes as $prime) {
            if ($num % $prime == 0) {
                return false;
            }
        }


        return $this->millerRabin($num);
    }
}
