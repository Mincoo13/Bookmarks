<?php

namespace App\Http\Controllers;

use App\Bookmark;
use App\Comment;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class CommentController extends Controller
{
    public function createComment(Request $request){
        $user_id = JWTAuth::user()->id;
        $bookmark_id = $request->bookmark_id;
        $text = $request->text;
        $bookmark = Bookmark::find($bookmark_id);

        if(empty($bookmark)){
            return response()->json([
                'status' => 'error',
                'message' => 'Daná záložka neexistuje.',
            ],409);
        }
        elseif($bookmark->isVisible == 0 && $bookmark->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na komentovanie tejto záložky nemáte právo.',
            ],401);
        }
        elseif (empty($text)){
            return response()->json([
                'status' => 'error',
                'message' => 'Komentár nemôže byť prázdny.',
            ],409);
        }
        elseif (empty($bookmark_id)){
            return response()->json([
                'status' => 'error',
                'message' => 'Komentar musí byť priradený k záložke.',
            ],409);
        }
        else{
            DB::table('comments')->insert([
                'user_id' => $user_id,
                'bookmark_id' =>  $bookmark_id,
                'text' => $text,
                'created_at' => Carbon::now(),
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Komentár bol pridaný.',
            ],200);
        }
    }

    public function getComments($id){
        $comments = Comment::where('bookmark_id', $id)->get();
        foreach($comments as $comment){
            $user = User::find($comment->user_id);
            $name = $user->name;
            $surname = $user->surname;
            $fullname = $name." ".$surname;
            $comment->fullname = $fullname;
        }
        return $comments;
    }

    public function getUserName($id){
        $user_id = Comment::find($id)->user_id;
        $user_name = User::find($user_id)->name;
        $user_surname = User::find($user_id)->surname;
        $fullname = $user_name." ".$user_surname;
        return $fullname;
    }

    public function editComment($id, Request $request){
        $user_id = JWTAuth::user()->id;
        $text = $request->text;
        $comment = Comment::find($id);

        if($user_id != $comment->user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na úpravu tohto komentáru nemáte povolenie.',
            ],401);
        }
        elseif (empty($text)){
            return response()->json([
                'status' => 'error',
                'message' => 'Komentár nemôže buyť prázdny.',
            ],409);
        }
        else{
            $comment->text = $text;
            $comment->updated_at = Carbon::now();
            $comment->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Komentár bol upravený.',
            ],200);
        }
    }

    public function deleteComment($id){
        $user = JWTAuth::user();
        $user_id = $user->id;
        $comment = Comment::find($id);

        if($comment == NULL){
            return response()->json([
                'status' => 'error',
                'message' => 'Daný komentár neexistuje.',
            ],409);
        }
        else{
            $bookmark_id = $comment->bookmark_id;
            $bookmark = Bookmark::find($bookmark_id);
            if($user_id != $bookmark->user_id && $user_id != $comment->user_id && $user->isAdmin != 1){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Na zmazanie tohto komentáru nemáte právo.',
                ],401);
            }
            else{
                $comment->forceDelete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Komentár bol odstránený.',
                ],200);
            }
        }

    }
}
