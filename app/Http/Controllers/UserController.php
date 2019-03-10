<?php

namespace App\Http\Controllers;

use App\Mail\ForgottenPassword;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        return response()->json([
            'status' => 'success',
            'message' => 'Zmazanie pouzivatela bolo uspesne.'
        ],200);
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
            return response()->json([
                'status' => 'error',
                'message' => 'Nove heslo nesplna poziadavky dostatocne silneho hesla.'
            ],500);
        }

        if(!(Hash::check($oldPassword, $actualPassword))){
            return response()->json([
                'status' => 'error',
                'message' => 'Aktualne heslo je nespravne.'
            ],500);
        }elseif ($newPassword != $confirm){
            return response()->json([
                'status' => 'error',
                'message' => 'Nove hesla sa nezhoduju.'
            ],500);
        }elseif (Hash::check($newPassword, $actualPassword)){
            return response()->json([
                'status' => 'error',
                'message' => 'Nove heslo nemoze byt rovnake ako stare heslo.'
            ],500);
        }else{
            User::find($id)->update([
                "password" => Hash::make($newPassword)
            ]);
            return response()->json(['Heslo bolo zmenene.'],200);
        }
    }

    public function activateUser($id){

        User::find($id)->update([
            "isActive" => 1,
        ]);
        $user = User::find($id);
        return response()->json($user);
    }

    public function deactivateUser($id){

        User::find($id)->update([
            "isActive" => 0,
        ]);
        $user = User::find($id);
        return response()->json($user);
    }

    public function resetPassword(Request $request){
        $email = $request->email;
        if(User::where('email',$email)->first() == null){
            return response()->json([
                'status' => 'error',
                'message' => 'Ziaden pouzivatel nie je zaregistrovany pod touto e-mailovou adresou.'
            ], 409);
        }
        $newPassword = str_random(8);
        $hashedPassword = Hash::make($newPassword);
        User::where('email',$email)->first()->update([
            "password" => $hashedPassword
        ]);
        Mail::to($email)->send(new ForgottenPassword($newPassword));
        return $newPassword;
    }

}
