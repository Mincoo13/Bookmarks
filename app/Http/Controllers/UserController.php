<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Hash;

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

    public function deleteUser($id){
        $user = User::find($id);
        $user->forceDelete();
        return 'delete succesful';
    }

    public function changePassword(Request $request){
        $id = JWTAuth::user()->id;

        $actualPassword = JWTAuth::user()->password;
        $oldPassword = $request->oldPassword;
        $newPassword = $request->newPassword;
        $confirm = $request->confirm;

        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/',
            'confirm' => 'required',
        ]);

        if ($validator->fails()) {
            return 'Nove heslo nesplna pozdiadavky.';
        }
        
        if(!(Hash::check($oldPassword, $actualPassword))){
            return 'Aktualne heslo je nespravne.';
        }elseif ($newPassword != $confirm){
            return 'Nove hesla sa nezhoduju.';
        }elseif (Hash::check($newPassword, $actualPassword)){
            return 'Nove heslo nemoze byt rovnake ako stare heslo.';
        }else{
            User::find($id)->update([
                "password" => Hash::make($newPassword)
            ]);
            return response()->json('Heslo bolo zmenene.');
        }
    }
}
