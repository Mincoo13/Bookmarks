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
        if($exist != []){
            return response()->json([
                'status' => 'error',
                'message' => 'Kategoria s tymto nazvom uz existuje. '.$exist
            ],409);
        }else{
            DB::table('categories')->insert([
                'user_id' => $user_id,
                'name' => $name,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Kategoria bola uspesne vytvorena.'
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
        $category = Category::find($id);
        if($category == []){
            return response()->json([
                'status' => 'error',
                'message' => 'Kategoria neexistuje.'
            ],404);
        }
        else if($category->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zmenu tejto kategorie nemate pravo. '
            ],401);
        }
        else if($exist != []){
            return response()->json([
                'status' => 'error',
                'message' => 'Kategoria s danym nazvom uz existuje.'
            ],409);
        }
        else{
            $category->update([
                "name" => $name,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Kategoria bola uspesne zmenena.'
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
                'message' => 'Nie je mozne vymazat neexistujucu kategoriu.'
            ],409);
        }
        else if($category->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zmazanie tejto kategorie nemate pravo'
            ],401);
        }
        else if(!empty($bookmarks)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nie je mozne vymazat kategoriu, ktorej su priradene existujuce zalozky. '
            ], 409);
        }
        else{
            $category->forceDelete();
            return response()->json([
                'status' => 'success',
                'message' => 'Zmazanie kategorie bolo uspesne.'
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
                'message' => 'Nie je mozne zobrazit neexistujucu kategoriu.'
            ],409);
        }
        else if($category->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zobrazenie tejto kategorie nemate pravo'
            ],401);
        }
        else{
            $category->bookmarks = $bookmarks;
            return $category;
        }
    }
}