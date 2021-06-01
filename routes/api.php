<?php

use App\Http\Controllers\AccessRightsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\AwsController;
use App\Http\Controllers\BinhLuanController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChuDeController;
use App\Http\Controllers\ForgotController;
use App\Http\Controllers\HocTapController;
use App\Http\Controllers\LophocController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolesResourcesController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TaiLieuController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UsersRolesController;
use App\Models\ResourceRole;
use App\Models\ScheduleCalendar;
use App\Models\TaiLieu;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Auth::routes();
Route::get('/get-users', [UserController::class, 'index']);
Route::get('/get-all-users', [UserController::class, 'get_all_users']);
Route::post('/create-new-user', [UserController::class, 'create_new_user']);
Route::post('/update-user', [UserController::class, 'update_user']);
Route::post('/delete-user', [UserController::class, 'delete_user']);
Route::post('/update-user-chat-id', [UserController::class, 'update_user_chat_id']);
Route::post('/update-user-chat-id-using-email', [UserController::class, 'update_user_chat_id_using_email']);


Route::get('/get-all-classes', [LophocController::class, 'get_all_classes']);
Route::post('/get-students-of-class', [LophocController::class, 'get_students_of_class']);

Route::post('/user_import', [UserController::class, 'store']);
Route::get("/get-private-files/{mauser}", [UserController::class, 'get_private_files']);
Route::get("/get-private-info/{mauser}/{magiaovien}", [UserController::class, 'get_private_info']);
Route::post("/update-private-info", [UserController::class, 'update_private_info']);

Route::post('/login', [AuthenticationController::class, 'index']);
Route::get('/logout', [AuthenticationController::class, 'logout']);
try{
    Route::get('/user', [AuthenticationController::class, 'current_user'])->middleware('auth:api');
}
catch(Error $err)
{
    return response([
        'status' => 'false'
    ]);
}
Route::post('/change_password', [AuthenticationController::class, 'change_password']);

Route::post('/forgot', [ForgotController::class, 'forgot']);
Route::post('/xacthucemail_reset_password', [ForgotController::class, 'xacthucemail_reset_password']);

Route::get('get_file_from_aws/{file_name}', [AwsController::class, 'get_file_from_aws']);
Route::post('/upload-file-aws', [AwsController::class, 'upload_file_aws']);
Route::post('/update-private-file-of-user', [AwsController::class, 'update_private_files_of_user']);

Route::get('/create-chat-room', [ChatController::class, 'create_chat_room']);
Route::get('/get-users-in-specific-class/{class_name}', [ChatController::class, 'get_users_in_class']);
Route::get('/check-edit-users/{email}/{class_name}', [ChatController::class, 'check_edit_users']);
Route::post('/update-room-chat-for-user', [ChatController::class, 'update_room_chat_id_for_user']);
Route::post('/remove-member-in-chat', [ChatController::class, 'remove_member_in_chat']);

Route::post('/get-schedule-list', [ScheduleController::class, 'get_schedule_calendar_of']);
Route::post('/add-appointment', [ScheduleController::class, 'add_appointment']);
Route::post('/edit-appointment', [ScheduleController::class, 'edit_appointment']);
Route::post('/delete-appointment', [ScheduleController::class, 'delete_appointment']);

Route::post('/check-access-rights-for-schedule-calendar', [AccessRightsController::class, 'check_access_rights_for_schedule_calendar']);

Route::get('/get-all-users-roles', [UsersRolesController::class, 'get_all_users_roles']);
Route::post('/add-user-role', [UsersRolesController::class, 'store']);
Route::delete('/delete-user-role', [UsersRolesController::class, 'destroy']);


Route::get('/get-roles', [RoleController::class, 'index']);
Route::post('/add-role', [RoleController::class, 'store']);
Route::put('/update-role/{id}', [RoleController::class, 'update']);
Route::delete('/delete-role/{id}', [RoleController::class, 'destroy']);

Route::get('/get-resources', [ResourceController::class, 'index']);
Route::post('/add-resource', [ResourceController::class, 'store']);
Route::put('/update-resource/{id}', [ResourceController::class, 'update']);
Route::delete('/delete-resource/{id}', [ResourceController::class, 'destroy']);
Route::post('/check-is-create-specific-resource', [ResourceController::class, 'check_is_create_specific_resource']);
Route::post('/create-new-resource', [ResourceController::class, 'create_new_resource']);

Route::get('/get-all-roles-resources', [RolesResourcesController::class, 'get_all_roles_resources']);
Route::post('/add-role-resource', [RolesResourcesController::class, 'store']);

Route::delete('/delete-role-resource/{id}', [RolesResourcesController::class, 'destroy']);

Route::post("/get-tailieu-of-specific-class", [TaiLieuController:: class, 'show']);
Route::post("/tao-tai-lieu", [TaiLieuController::class, 'store']);
Route::delete('/xoa-tai-lieu/{id}', [TaiLieuController::class, 'destroy']);
Route::post('/chap-nhan-tai-lieu', [TaiLieuController::class, 'approve']);
Route::post('/sua-tai-lieu', [TaiLieuController::class, 'update']);

Route::post('/get-access-rights', [AccessRightsController::class, 'get_access_rights_of_user']);

Route::post('/topics', [ChuDeController::class, 'show']);
Route::post('/add-topic', [ChuDeController::class, 'store']);
Route::post('/chap-nhan-chu-de', [ChuDeController::class, 'approve']);
Route::delete('/delete-topic/{id}', [ChuDeController::class, 'destroy']);
Route::put('/edit-topic/{id}', [ChuDeController::class, 'update']);

Route::post('/gui-thong-bao', [UserController::class, 'gui_thong_bao']);

Route::post('/xem-diem', [HocTapController::class, 'xem_diem']);

Route::post('/xem-binh-luan', [BinhLuanController::class, 'xembinhluan']);
Route::post('/tao-binh-luan', [BinhLuanController::class, 'create_a_comment']);