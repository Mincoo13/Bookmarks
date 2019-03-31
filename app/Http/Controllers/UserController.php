<?php

namespace App\Http\Controllers;

use App\Mail\ForgottenPassword;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use JWTAuth;
use Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function registerUser(Request $request){
        $name = $request->name;
        $surname = $request->surname;
        $email = $request->email;
        $password = $request->password;
        $password_confirmation = $request->password_confirmation;

        if($password != $password_confirmation){
            return response()->json([
                'status' => 'error',
                'message' => 'Hesla sa nezhoduju.'
            ],409);
        }

        $this->validate(request(), [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/'],
        ]);

        return User::create([
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'password' => Hash::make($password),
            'isAdmin' => false,
            'isActive' => true,
        ]);
    }

    public function showProfile(){
        $id = JWTAuth::user()->id;
        $user = User::find($id);

        return $user;
    }

    public function editProfile(Request $request){
        $id = JWTAuth::user()->id;

        if(empty($request->name)){
            return response()->json([
                'status' => 'error',
                'message' => 'Musite zadat meno.'
            ],409);
        }
        elseif(empty($request->surname)){
            return response()->json([
                'status' => 'error',
                'message' => 'Musite zadat priezvisko.'
            ],409);
        }
        else{
            User::find($id)->update([
                "name" => $request->name,
                "surname" => $request->surname,
                "updated_at" => Carbon::now()
            ]);
            $user = User::find($id);
            return response()->json($user);
        }
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
        $confirmPassword = $request->confirmPassword;

        if(!(Hash::check($oldPassword, $actualPassword))){
            return response()->json([
                'status' => 'error',
                'message' => 'Aktualne heslo je nespravne.'
            ],500);
        }
        elseif(empty($newPassword)){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zmenu hesla je potrebne zadat nove heslo.'
            ],500);
        }
        $validator = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$/',
            'confirmPassword' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nove heslo nesplna poziadavky dostatocne silneho hesla.'
            ],500);
        }
        elseif ($newPassword != $confirmPassword){
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
