<?php

namespace App\Http\Controllers;

use App\Bookmark;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tests\Browser\Pages\Page;

class CategoryController extends Controller
{
    public function createCategory(Request $request){
        $name = $request->name;
        $user_id = JWTAuth::user()->id;
        $exist = Category::where([
            ['user_id', '=', $user_id],
            ['name', '=', $name]
        ])->first();
        if(empty($name)){
            return response()->json([
                'status' => 'error',
                'message' => 'Musíte zadať meno kategórie.'
            ],409);
        }
        elseif($exist != []){
            return response()->json([
                'status' => 'error',
                'message' => 'Kategória s názvom '.$name.' už existuje.'
            ],409);
        }else{
            DB::table('categories')->insert([
                'user_id' => $user_id,
                'name' => $name,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Kategória bola úspešne vytvorená.'
            ],200);
        }
    }

    public function editCategory($id, Request $request){
        $name = $request->name;
        $user_id = JWTAuth::user()->id;
        $exist = Category::where([
            ['user_id', '=', $user_id],
            ['name', '=', $name]
        ])->first();
        $bookmarks = Bookmark::where('category_id',$id)->get();
        $category = Category::find($id);
        if($category == []){
            return response()->json([
                'status' => 'error',
                'message' => 'Kategória neexistuje.'
            ],404);
        }
        else if($category->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zmenu tejto kategórie nemáte právo. '
            ],401);
        }
        else if($exist != []){
            return response()->json([
                'status' => 'error',
                'message' => 'Kategória s daným názvom už existuje.'
            ],409);
        }
        else{
            foreach($bookmarks as $bookmark){
                $bookmark->category_name = $name;
                $bookmark->save();
            }
            $category->update([
                "name" => $name,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Kategória bola úspešne zmenená.'
            ],200);
        }
    }

    public function deleteCategory($id){
        $category = Category::find($id);
        $bookmarks = Bookmark::where('category_id', $id)->first();
        $user_id = JWTAuth::user()->id;

        if(empty($category)){
            return response()->json([
                'status' => 'error',
                'message' => 'Nie je možné vymazať neexistujúcu kategóriu.'
            ],409);
        }
        else if($category->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zmazanie tejto kategórie nemáte právo'
            ],401);
        }
        else if(!empty($bookmarks)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nie je možné vymazať kategóriu, ktorej sú priradené existujúce záložky. '
            ], 409);
        }
        else{
            $category->forceDelete();
            return response()->json([
                'status' => 'success',
                'message' => 'Kategória bola zmazaná..'
            ],200);
        }
    }

    public function showAllCategories(){
        $user_id = JWTAuth::user()->id;
        $categories = Category::where('user_id',$user_id)->get();
        return $categories;
    }

    public function showCategory($id){
        $category = Category::find($id);
//        $bookmarks = Bookmark::where([
//            ['user_id','=',$user_id],
//            ['category_id','=', $id]
//        ])->get();
        $bookmarks = Bookmark::where('category_id', $id)->get();
        $user_id = JWTAuth::user()->id;

        if(empty($category)){
            return response()->json([
                'status' => 'error',
                'message' => 'Nie je možné zobraziť neexistujúcu kategóriu.'
            ],409);
        }
        else if($category->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zobrazenie tejto kategórie nemáte právo'
            ],401);
        }
        else{
            $category->bookmarks = $bookmarks;
            return $category;
        }
    }
}
