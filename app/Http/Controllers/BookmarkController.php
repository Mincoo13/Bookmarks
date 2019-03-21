<?php

namespace App\Http\Controllers;

use App\Bookmark;
use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class BookmarkController extends Controller
{
    public function createBookmark(Request $request){
        $user_id = JWTAuth::user()->id;
        $url = $request->url;
        $name = $request->name;
        $description = $request->description;
        $category_id = $request->category_id;
        $isVisible = $request->isVisible;
        $category = Category::find($category_id);
        $exist = Bookmark::where([
            ['user_id', '=', $user_id],
            ['name', '=', $name]
        ])->first();

        if(!empty($category_id)){
            if(empty($category) && !empty($category_id)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kategoria neexistuje.'
                ],409);
            }
            elseif($category->user_id != $user_id){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Na priradenie tejto kategorie nemate pravo.'
                ],401);
            }
        }
        if(!empty($exist)){
            return response()->json([
                'status' => 'error',
                'message' => 'Zalozka s tymto nazvom uz existuje.'
            ],409);
        }
        elseif(empty($url)){
            return response()->json([
                'status' => 'error',
                'message' => 'Zalozka musi mat uvedenu URL adresu.'
            ],409);
        }
        elseif(empty($name)){
            return response()->json([
                'status' => 'error',
                'message' => 'Zalozka musi mat uvedeny nazov.'
            ],409);
        }
        else{
            if($isVisible == null){
                $isVisible = true;
            }
            DB::table('bookmarks')->insert([
                'user_id' => $user_id,
                'category_id' => $category_id,
                'name' => $name,
                'url' => $url,
                'description' => $description,
                'isRead' => false,
                'isVisible' => $isVisible,
                'created_at' => Carbon::now(),
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Zalozka bola uspesne vytvorena.',
            ],200);
        }
    }

    public function editBookmark($id, Request $request){
        $user_id = JWTAuth::user()->id;
        $name = $request->name;
        $url = $request->url;
        $description = $request->description;
        $category_id = $request->category_id;
        $isVisible = $request->isVisible;
        $category = Category::find($category_id);
        $bookmark = Bookmark::find($id);
        $bookmarkWithName = Bookmark::where([
            ['user_id', '=', $user_id],
            ['name', '=', $name]
        ])->first();

        if(empty($bookmark)){
            return response()->json([
                'status' => 'error',
                'message' => 'Zalozka neexistuje.',
            ],409);
        }
        else{
            if($bookmark->user_id != $user_id){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Na upravu tejto zalozky nemate opravnenie.',
                ],401);
            }
            if(!empty($category_id)){
                if(empty($category) && !empty($category_id)){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Kategoria neexistuje.'
                    ],409);
                }
                elseif($category->user_id != $user_id){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Na priradenie tejto kategorie nemate pravo.'
                    ],401);
                }
            }
            if(!empty($bookmarkWithName) && ($bookmark->name != $name)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Zalozka s tymto nazvom uz existuje.',
                ],409);
            }
            elseif(empty($url)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Zalozka musi mat uvedenu URL adresu.'
                ],409);
            }
            elseif(empty($name)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Zalozka musi mat uvedeny nazov.'
                ],409);
            }
            else{
                if(empty($description)){
                    $description = null;
                }
                if(empty($category_id)){
                    $category_id = null;
                }
                if($isVisible == null){
                    $isVisible = $bookmark->isVisible;
                }

                $bookmark->url = $url;
                $bookmark->name = $name;
                $bookmark->description = $description;
                $bookmark->category_id = $category_id;
                $bookmark->isVisible = $isVisible;
                $bookmark->updated_at = Carbon::now();
                $bookmark->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Zalozka bola uspesne zmenena.'
                ],200);
            }
//

        }
    }

    public function markReadFlag($id){
        $user_id = JWTAuth::user()->id;
        $bookmark = Bookmark::find($id);

        if(empty($bookmark)){
            return response()->json([
                'status' => 'error',
                'message' => 'Dana zalozka neexistuje.',
            ],409);
        }
        else{
            $flag = $bookmark->isRead;
            if($bookmark->user_id != $user_id){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Na zmenu oznacenia tejto zalozky nemate opravnenie.',
                ],401);
            }
            elseif ($flag == false){
                $bookmark->isRead = true;
                $bookmark->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Zalozka bola uspesne oznacena za precitanu',
                ],200);
            }
            else{
                $bookmark->isRead = false;
                $bookmark->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Zalozka bola uspesne oznacena za neprecitanu',
                ],200);
            }
        }
    }

    public function deleteBookmark($id){
        $user_id = JWTAuth::user()->id;
        $bookmark = Bookmark::find($id);

        if(empty($bookmark)){
            return response()->json([
                'status' => 'error',
                'message' => 'Dana zalozka neexistuje.',
            ],409);
        }
        elseif($user_id != $bookmark->user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zmazanie tejto zalozky nemate pravo.',
            ],401);
        }
        else{
//            Vyberiem vsetky riadky v spojovacej tabulke, kde sa nachadza tento bookmark, teda vsetky zoznamy
            $all_bookmarks = DB::table('bookmarklists_bookmarks')->where([
                ['bookmark_id', '=', $bookmark->id],
            ])->get();

            //            Vytvorim pole, do ktoreho vlozim ich idcka
                $array_lists_id = array();
                foreach ($all_bookmarks as $one_list){
                    array_push($array_lists_id,[$one_list->bookmarklist_id]);
                }
                if(empty($array_lists_id)){
                    $bookmark->forceDelete();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Bookmark bol uspesne odstraneny.',
                    ],200);
                }
                else{
//            Opakujem tolkokrat, kolko zoznamov s danym bookmarkom existuje
                foreach($array_lists_id as $list_id){
//                Najdem poradie bookmarku v danom zozname
                    $order = DB::table("bookmarklists_bookmarks")->where([
                        ['bookmarklist_id', '=', $list_id],
                        ['bookmark_id', '=', $id],
                    ])->first()->order;

//                Mazem bookmark zo zoznamu
                    DB::table("bookmarklists_bookmarks")->where([
                        ['bookmarklist_id', '=', $list_id],
                        ['bookmark_id', '=', $id],
                    ])->delete();

//                Vyberiem vsetky zaznamy, ktore maju poradie vyssie ako zaznam s bookmarkom, ktory idem odstranit
                    $all_bookmarks = DB::table('bookmarklists_bookmarks')->where([
                        ['bookmarklist_id','=', $list_id],
                        ['order', '>', $order],
                    ])->get();
//                 Ak take zaznamu neexistuju, t.j. mazany bookmark je posledny v poradi, tak neriesime zoradovanie
                    if(empty($all_bookmarks)){
                        $bookmark->forceDelete();
                        continue;
                    }
                    else{
                        //                Znovu vytvaram pole, pre zoradenie zvysnych bookmarkov v zozname
                        $array = array();
                        foreach ($all_bookmarks as $item_id){
                            array_push($array,[$item_id->id]);
                        }
                        $i = 0;
                        foreach ($all_bookmarks as $item){

                            $new = $item->order - 1;
                            DB::table('bookmarklists_bookmarks')->where('id', $array[$i])->update(['order' => $new]);
                            $i++;
                        }
                    }
//            Nakoniec samotny bookmark zmazem (zmazu sa aj vsetky komentare)

                }
                    $bookmark->forceDelete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bookmark bol uspesne odstraneny.',
                ],200);
            }
        }
    }
}
