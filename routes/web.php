<?php

use App\Events\MyEvent;
use App\Http\Controllers\AuthenticationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MongoTestController;
use App\Http\Controllers\OauthController;
use App\Http\Controllers\RSAController;
use App\Http\Controllers\TestController;
use App\Models\ResourceRole;
use App\Models\Test;
use App\Models\User;
use App\Models\Users_Roles;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseInterface as HttpClientResponseInterface;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/user/import', [UserController::class, 'store']);

Route::get('/user/get_list_users', [UserController::class, 'index']);

// Login for users
Route::get('/mongo', [MongoTestController::class, 'mongoConnect']);

Route::get('/get_form_test', function () {
    return view('form_test_upload');
});

Route::post('/post_form_test_upload', function (Request $request) {
    $file = $request->file('uploadfile'); // input name="uploadfile"
    $filename = $file->getClientOriginalName();
    $filename = time() . $filename;
    $path = $file->storeAs('public/', $filename, 's3');
});

// lấy file từ aws
Route::get("get_file_from_aws", function () {
    if (Storage::disk('s3')->exists('1617081124react_new_update_2019.pdf')) {
        echo "Có file";
    }
    $url = Storage::temporaryUrl(
        '1617081124react_new_update_2019.pdf',
        now()->addMinutes(5)
    );
    echo $url;
});

//lấy file từ collection test
Route::get("getdatatest", function () {
    $container_files = [];
    $data = Test::where('user_id', 1)->get();

    foreach ($data as $d) {
        $files = $d->files;
        foreach ($files as $file) {
            array_push($container_files, $file);
        }
    }
    return response([
        'files' => $container_files,
    ]);
});

Route::get('/test', function () {
    $a = User::where('email', "nguyennhuhoa@gmail.com")->first();
    dd($a->roles);
});

Route::get('/get_active_account_view', function () {
    return view('emails.ActiveAccount', ['details' => [
        'title' => "Reset Password",
        'email' => 'ptud2020@gmail.com',
    ]]);
});
Route::post('/active_account', [UserController::class, 'active_account'])->name('active_account');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('testupdate', [UserController::class, 'test']);

Route::get('/testapi', function () {
    $client = new GuzzleHttp\Client();
    $res = $client->request('GET', 'https://api.chatengine.io/users/', [
        'headers' => [
            // 'PRIVATE-KEY' => '62294469-da47-4cc9-ab51-13e6da7fe0d6',
            'PRIVATE-KEY' => config('app.projectchatprivate'),
        ]
    ]);
    dd($res->getBody()->getContents());
});

Route::get('/testoperator', function () {
    $read = [];
    $edit = [];
    $delete = [];
    $arr1 = [true, false, true];
    $arr2 = [false, false, true];
    $arrsum = [$arr1, $arr2];
    foreach ($arrsum as $arr) {
        array_push($read, $arr[0]);
        array_push($edit, $arr[1]);
        array_push($delete, $arr[2]);
    }

    print_r($read);
    print_r($edit);
    print_r($delete);
    $arrtest = array_merge($arr1, $arr2);
    print_r($arrtest);
    // echo true | false;
});

Route::get("/abc", function () {
    $arr = [];
    $c = [];
    $roles = User::find("u05")->roles;
    foreach ($roles as $role) {
        $resource_role = ResourceRole::where('role_id', $role->role_id)->where('resource_id', "view_01")->first();
        if ($resource_role) {
            array_push(
                $arr,
                [
                    "can_read" => $resource_role->can_read ? "read" : "",
                    "can_add" => $resource_role->can_add ? "add" : "",
                    "can_update" => $resource_role->can_update ? "update" : "",
                    "can_delete" => $resource_role->can_delete ? "delete" : "",
                ]
            );
        }
    }

    $array = array_unique((array_filter($arr, function ($a) {
        return trim($a) !== "";
    })));

    dd($array);

    return response([
        'status' => true,
        'data' => $array,
    ]);
});

Route::get('/testcreatechatuser', function () {
    dd(Auth::user());
    //     $client = new GuzzleHttp\Client(
    //         ['auth' => [Auth::user()->email, "Hai123456"]]
    //     );
    //     $headers = [
    //         "PRIVATE-KEY" => "62294469-da47-4cc9-ab51-13e6da7fe0d6",
    // ];
    //     $promise = $client->requestAsync('POST', 'https://api.chatengine.io/users/', $headers, [
    //         'form_params' => [
    //             'username' => 'happypola',
    //             'secret' => '123456',
    //         ]
    //     ]);

    //     dd($promise->wait());
    //     // $promise->then(
    //     //     function (ResponseInterface $res) {
    //     //         dd($res->getStatusCode());
    //     //     },
    //     //     function (RequestException $e) {
    //     //         dd($e->getMessage());
    //     //         dd($e->getRequest()->getMethod());
    //     //     }
    //     // );
})->middleware('auth:api');

Route::get('/testRSAss', [TestController::class, 'test']);
Route::get('/testEvent', function () {
    echo "Hello";
    event(new MyEvent("Hello xin chao, t đang test cái này sấp mặt-"));
});
