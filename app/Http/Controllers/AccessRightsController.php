<?php

namespace App\Http\Controllers;

use App\Models\ResourceRole;
use App\Models\ScheduleCalendar;
use App\Models\User;
use Error;
use Illuminate\Http\Request;

class AccessRightsController extends Controller
{
    //
    // public function check_access_rights_for_schedule_calendar(Request $request){

    //     $mauser = $request->mauser;
    //     $resource_id = $request->object_id;

    //     $user = User::find($mauser);
    //     $roles = $user->roles;
    //     $access_rights = [];
    //     foreach($roles as $r)
    //     {
    //         $resource_role = ResourceRole::where('role_id', $r->role_id)->where('resource_id', $resource_id);
    //         if(!$resource_role->get()->isEmpty())
    //         {
    //             array_push($access_rights, $resource_role->first());
    //         }
    //     }

    //     foreach($access_rights as $a)
    //     {
    //         $access_rights = [
    //             'user_role' => $a->role_id,
    //             'can_read' => $a->can_read,
    //             'can_add' => $a->can_add,
    //             'can_update' => $a->can_update,
    //             'can_delete' => $a->can_delete,  
    //         ];
    //     }
    //     return response([
    //         'access_rights' => $access_rights,
    //     ]);
    // }

    public function get_access_rights_of_user(Request $request)
    {
        try{
            $user_role = [];
            $arr = [];
    
            $mauser = $request->mauser;
            $resource_id = $request->resource_id;
            $roles = User::find($mauser)->roles;
            foreach ($roles as $r) {
                $resource_role = ResourceRole::where('role_id', $r->role_id)->where('resource_id', $resource_id)->first();
                if ($resource_role) {
                    array_push($user_role, $resource_role->role_id);
                    array_push(
                        $arr,
                        $resource_role->can_read ? "read" : "",
                        $resource_role->can_add ? "add" : "",
                        $resource_role->can_update ? "update" : "",
                        $resource_role->can_delete ? "delete" : "",
                    );
                }
            }
            $array = array_unique((array_filter($arr, function ($a) {
                return trim($a) !== "";
            })));
    
            $access_rights = [
                'allowRead' => in_array("read", $array) ? true : false,
                'allowAdd' => in_array("add", $array) ? true : false,
                'allowUpdate' => in_array("update", $array) ? true : false,
                'allowDelete' => in_array("delete", $array) ? true : false,
            ];
            return response([
                'status' => true,
                'data' => $access_rights,
            ]);
        }catch(Error $err)
        {
            return response([
                'status' => false,
                'message' => "Something went wrong",
            ]);
        }
        


    }

    public function check_access_rights_for_schedule_calendar(Request $request)
    {
        try {
            $arr = [];
            $user_role = [];

            $mauser = $request->mauser;
            $resource_id = $request->resource_id;
            $magiaovien = $request->magiaovien;

            $schedule = ScheduleCalendar::where('magiaovien', $magiaovien)->first();

            // lấy tất cả các role của user
            $roles = User::find($mauser)->roles;

            foreach ($roles as $r) {
                // $resource_role = ResourceRole::where('role_id', $r->role_id)->where('resource_id', $resource_id);
                // if(!$resource_role->get()->isEmpty())
                // {
                //     $resource_role = $resource_role->first();
                //     $access_rights = [
                //         'user_role' => $resource_role->role_id,
                //         'allowRead' => $resource_role->can_read,
                //         'allowAdd' => $resource_role->can_add,
                //         'allowUpdate' => $resource_role->can_update,
                //         'allowDelete' => $resource_role->can_delete,
                //     ];
                //     return response([
                //         'status' => true,
                //         'accessRights' => $access_rights,
                //         'dataList' => $schedule->schedule_list,
                //     ]);

                // }
                $resource_role = ResourceRole::where('role_id', $r->role_id)->where('resource_id', $resource_id)->first();
                if ($resource_role) {
                    array_push($user_role, $resource_role->role_id);
                    array_push(
                        $arr,
                        $resource_role->can_read ? "read" : "",
                        $resource_role->can_add ? "add" : "",
                        $resource_role->can_update ? "update" : "",
                        $resource_role->can_delete ? "delete" : "",
                    );
                }
            }
            $array = array_unique((array_filter($arr, function ($a) {
                return trim($a) !== "";
            })));

            $access_rights = [
                'user_role' => $user_role,
                'allowRead' => in_array("read", $array) ? true : false,
                'allowAdd' => in_array("add", $array) ? true : false,
                'allowUpdate' => in_array("update", $array) ? true : false,
                'allowDelete' => in_array("delete", $array) ? true : false,
            ];
            return response([
                'status' => true,
                'accessRights' => $access_rights,
                'dataList' => $schedule->schedule_list,
            ]);
        } catch (Error $err) {
            return response([
                'status' => false,
                'message' => "Something went wrong."
            ]);
        }
    }
}
