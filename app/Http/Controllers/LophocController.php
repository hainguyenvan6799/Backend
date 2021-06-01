<?php

namespace App\Http\Controllers;

use App\Models\LopHoc;
use App\Models\User;
use Illuminate\Http\Request;


class LophocController extends Controller
{
    //
    public function get_all_classes(){
        $classes = LopHoc::all();
        return response([
            'classes' => $classes,
        ]);
    }

    public function get_students_of_class(Request $request){
        $malop = $request->malop;
        $students = User::where('malop', $malop)->where('group', 'sv')->get();
        return response([
            'status' => true,
            'data' => $students,
        ]);
    }
}
