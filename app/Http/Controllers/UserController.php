<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;

class UserController extends Controller
{
    public function showProfile(){
        $id = JWTAuth::user()->id;
        $user = User::find($id);

        return $user;
    }

    public function editProfile(Request $request){
        $id = JWTAuth::user()->id;


        User::find($id)->update([
            "name" => $request->name,
            "surname" => $request->surname
        ]);
        $user = User::find($id);
        return response()->json($user);
    }

    public function editProfileAdmin($id, Request $request){
        User::find($id)->update([
            "name" => $request->name,
            "surname" => $request->surname
        ]);
        $user = User::find($id);
        return response()->json($user);
    }
}
