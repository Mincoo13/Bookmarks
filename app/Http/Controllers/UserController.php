<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;

class UserController extends Controller
{
    public function showProfile(){
        $id = JWTAuth::user()->id;
        $user = User::find($id)->name;

        return $user;
    }

    public function editProfile(Request $request){
        $id = JWTAuth::user()->id;


        $user = User::find($id)->update([
            "name" => $request->name,
            "surname" => $request->surname
        ]);
        return response()->json(["user" => $user]);
    }

    public function editProfileAdmin($id, Request $request){
        $user = User::find($id)->update([
            "name" => $request->name,
            "surname" => $request->surname
        ]);
        return response()->json(["user" => $user]);
    }
}
