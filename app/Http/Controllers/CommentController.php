<?php

namespace App\Http\Controllers;

use App\Bookmark;
use App\Comment;
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
                'message' => 'Dana zalozka neexistuje.',
            ],409);
        }
        elseif($bookmark->isVisible == 0 && $bookmark->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na komentovanie tejto zalozky nemate pravo.',
            ],401);
        }
        elseif (empty($text)){
            return response()->json([
                'status' => 'error',
                'message' => 'Komentar nemoze byt prazdny.',
            ],409);
        }
        elseif (empty($bookmark_id)){
            return response()->json([
                'status' => 'error',
                'message' => 'Komentar musi byt priradeny k zalozke.',
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
                'message' => 'Komentar bol pridany.',
            ],200);
        }
    }

    public function editComment($id, Request $request){
        $user_id = JWTAuth::user()->id;
        $text = $request->text;
        $comment = Comment::find($id);

        if($user_id != $comment->user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na upravu tohto komentaru nemate povolenie.',
            ],401);
        }
        elseif (empty($text)){
            return response()->json([
                'status' => 'error',
                'message' => 'Komentar nemoze byt prazdny.',
            ],409);
        }
        else{
            $comment->text = $text;
            $comment->updated_at = Carbon::now();
            $comment->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Komentar bol uspesne upraveny.',
            ],200);
        }
    }

    public function deleteComment($id){
        $user_id = JWTAuth::user()->id;
        $comment = Comment::find($id);

        if($comment == NULL){
            return response()->json([
                'status' => 'error',
                'message' => 'Dany komentar neexistuje.',
            ],409);
        }
        else{
            $bookmark_id = $comment->bookmark_id;
            $bookmark = Bookmark::find($bookmark_id);
            if($user_id != $bookmark->user_id && $user_id != $comment->user_id){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Na zmazanie tohto komentaru nemate pravo.',
                ],401);
            }
            else{
                $comment->forceDelete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Komentar bol uspesne zmazany.',
                ],200);
            }
        }

    }
}
