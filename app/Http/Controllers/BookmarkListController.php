<?php

namespace App\Http\Controllers;

use App\Bookmark;
use App\BookmarkList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class BookmarkListController extends Controller
{
    public function createBookmarkList(Request $request){
        $user_id = JWTAuth::user()->id;
        $name = $request->name;
        $isVisible = $request->isVisible;

        $exist = BookmarkList::where([
            ['user_id', '=', $user_id],
            ['name', '=', $name]
        ])->first();

        if(!empty($exist)){
            return response()->json([
                'status' => 'error',
                'message' => 'Zoznam s tymto nazvom uz existuje.'
            ],409);
        }
        elseif(empty($name)){
            return response()->json([
                'status' => 'error',
                'message' => 'Zoznam musi mat nazov.'
            ],409);
        }
        else{
            if(empty($isVisible)){
                $isVisible = 1;
            }
            DB::table('bookmarklists')->insert([
                'user_id' => $user_id,
                'name' => $name,
                'isVisible' => $isVisible,
                'created_at' => Carbon::now(),
            ]);

            $bookmarklist = BookmarkList::where([
                ['user_id','=',$user_id],
                ['name','=',$name],
            ])->first();

            return response()->json([
                $bookmarklist
            ],200);
        }
    }

    public function addBookmarkToList($id, Request $request){
        $user_id = JWTAuth::user()->id;

        $bookmark_id = $request->bookmark_id;
        $bookmark = Bookmark::find($bookmark_id);

        $bookmarkList = BookmarkList::find($id);
        $count = DB::table('bookmarklists_bookmarks')->where('bookmarklist_id', $id)->count();
        $order = $count + 1;

        if(empty($bookmarkList)){
            return response()->json([
                'status' => 'error',
                'message' => 'Dany zoznam neexistuje.',
            ],409);
        }
        else{
            if(empty($bookmark)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Zalozka, ktoru chcete pridat do zoznamu, neexistuje.',
                ],409);
            }
            else{
                $exist = DB::table('bookmarklists_bookmarks')->where([
                    ['bookmark_id', '=', $bookmark_id],
                    ['bookmarklist_id', '=', $id],
                ])->first();
                if($bookmarkList->user_id != $user_id){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Na pridanie zalozky do tohto zoznamu nemate povolenie.',
                    ],401);
                }
                elseif($bookmark->user_id != $user_id){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Na pridanie tejto zalozky do zoznamu nemate povolenie.',
                    ],401);
                }
                elseif (!empty($exist)){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Dana zalozka sa v tomto zozname uz nachadza.',
                    ],409);
                }
                else{
                    DB::table('bookmarklists_bookmarks')->insert([
                        'bookmark_id' => $bookmark_id,
                        'bookmarklist_id' => $id,
                        'order' => $order,
                        'created_at' => Carbon::now(),
                    ]);
                    if($bookmarkList->isVisible == 1) {
                        $bookmark->isVisible = 1;
                        $bookmark->save();
                    }
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Zalozka bola do zoznamu uspesne pridana.',
                    ],200);
                }
            }
        }
    }

    public function setBookmarkOrder($id, Request $request){
        $user_id = JWTAuth::user()->id;

        $new_order = (int)$request->order;
        $bookmark_id = (int)$request->bookmark_id;

        $bookmark = DB::table('bookmarklists_bookmarks')->where([
            ['bookmark_id', '=', $bookmark_id],
            ['bookmarklist_id', '=', $id],
        ])->first();

        $bookmarkList = BookmarkList::find($id);

        if(empty($bookmarkList)){
            return response()->json([
                'status' => 'error',
                'message' => 'Dany zoznam neexistuje.',
            ],409);
        }
        else{
            if(empty($bookmark)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dana zalozka sa v zozname nenachadza.',
                ],409);
            }
            else{
                $count = DB::table('bookmarklists_bookmarks')->where('bookmarklist_id', $id)->count();
                if($bookmarkList->user_id != $user_id){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Na zmenu poradia v tomto zozname nemate povolenie.',
                    ],401);
                }
                elseif ($new_order < 1 || $new_order > $count){
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Zadali ste neexistujuce poradie.',
                    ],409);
                }
                else{
                    if($new_order == $bookmark->order){
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Poradie sa zachovalo.',
                        ],200);
                    }
                    elseif(($new_order - $bookmark->order) < 0){
//                        1 - 4 = -3

                        $all = DB::table('bookmarklists_bookmarks')->where([
                            ['bookmarklist_id','=', $id],
                            ['order', '>=', $new_order],
                            ['order', '<', $bookmark->order],
                        ])->get();

                        $array = array();
                        foreach ($all as $item_id){
                            array_push($array,[$item_id->id]);
                        }
                        $i = 0;
                        foreach ($all as $item){

                            $new = $item->order + 1;
                            DB::table('bookmarklists_bookmarks')->where('id', $array[$i])->update(['order' => $new]);
                            $i++;
                        }
                        DB::table('bookmarklists_bookmarks')->where([
                            ['bookmark_id', '=', $bookmark_id],
                            ['bookmarklist_id', '=', $id],
                        ])->update(['order' => $new_order]);
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Poradie bolo uspesne aktualizovane.',
                        ],200);
                    }
                    else{
                        $all = DB::table('bookmarklists_bookmarks')->where([
                            ['bookmarklist_id','=', $id],
                            ['order', '<=', $new_order],
                            ['order', '>', $bookmark->order],
                        ])->get();

                        $array = array();
                        foreach ($all as $item_id){
                            array_push($array,[$item_id->id]);
                        }
                        $i = 0;
                        foreach ($all as $item){

                            $new = $item->order - 1;
                            DB::table('bookmarklists_bookmarks')->where('id', $array[$i])->update(['order' => $new]);
                            $i++;
                        }
                        DB::table('bookmarklists_bookmarks')->where([
                            ['bookmark_id', '=', $bookmark_id],
                            ['bookmarklist_id', '=', $id],
                        ])->update(['order' => $new_order]);
                        return response()->json([
                            'status' => 'success',
                            'message' => 'Poradie bolo uspesne aktualizovane.',
                        ],200);
                    }
                }
            }
        }
    }

    public function deleteBookmark($id, Request $request){
        $user_id = JWTAuth::user()->id;

        $bookmark_id = $request->bookmark_id;

        $bookmarkList = BookmarkList::find($id);
        $bookmark = DB::table('bookmarklists_bookmarks')->where([
            ['bookmark_id', '=', $bookmark_id],
            ['bookmarklist_id', '=', $id],
        ])->first();

        if($bookmarkList->user_id != $user_id){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zmazanie z tohto zoznamu nemate pravo.',
            ],401);
        }
        elseif (empty($bookmark)){
            return response()->json([
                'status' => 'error',
                'message' => 'Bookmark sa v zozname nenachadza.',
            ],409);
        }
        else{
            DB::table("bookmarklists_bookmarks")->where([
                ['bookmarklist_id', '=', $id],
                ['bookmark_id', '=', $bookmark_id],
            ])->delete();

            $all = DB::table('bookmarklists_bookmarks')->where([
                ['bookmarklist_id','=', $id],
                ['order', '>', $bookmark->order],
            ])->get();

            if(empty($all)){
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bookmark bol uspesne odstraneny zo zoznamu.',
                ],200);
            }
            else{
                $array = array();
                foreach ($all as $item_id){
                    array_push($array,[$item_id->id]);
                }
                $i = 0;
                foreach ($all as $item){

                    $new = $item->order - 1;
                    DB::table('bookmarklists_bookmarks')->where('id', $array[$i])->update(['order' => $new]);
                    $i++;
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bookmark bol uspesne odstraneny zo zoznamu.',
                ],200);
            }
        }
    }

    public function deleteBookmarkList($id){
        $user = JWTAuth::user();
        $user_id = $user->id;
        $bookmarkList = BookmarkList::find($id);

        if($user_id != $bookmarkList->user_id && $user->isAdmin != 1){
            return response()->json([
                'status' => 'error',
                'message' => 'Na zmazanie tohto zoznamu nemate pravo.',
            ],401);
        }
        else{
            $bookmarkList->forceDelete();
            return response()->json([
                'status' => 'success',
                'message' => 'Zoznam bol uspesne zmazany.',
            ],200);
        }
    }

    public function editBookmarkList($id, Request $request){
        $user = JWTAuth::user();
        $user_id = $user->id;
        $bookmarklist = BookmarkList::find($id);
        $isVisible = $request->isVisible;
        $name = $request->name;
        $exist = BookmarkList::where([
            ['user_id','=',$user_id],
            ['name','=',$name],
        ])->first();
        if($user_id != $bookmarklist->user_id && $user->isAdmin != 1){
            return response()->json([
                'status' => 'error',
                'message' => 'Na úpravu tohto zoznamu nemáte právo.'.$user_id." ".$bookmarklist->user_id,
            ],401);
        }else{
            if(empty($name)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Názov nemôže byť prázdny.',
                ],409);
            }
            elseif(!empty($exist)){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Zoznam s týmto názvom už existuje.',
                ],409);
            }
            else{
                $bookmarklist->update([
                    "name" => $name,
                    "isVisible" => $isVisible,
                    "updated_at" => Carbon::now(),
                ]);
                return $bookmarklist;
            }
        }
    }

    public function getBookmarkLists(){
        $user = JWTAuth::user();
        $user_id = $user->id;
        $bookmarkLists = BookmarkList::where('user_id', $user_id)->get();
        $array = array();
        foreach($bookmarkLists as $list){
            $count = DB::table('bookmarklists_bookmarks')->where('bookmarklist_id',$list->id)->get()->count();
            $list->count = $count;
            array_push($array, $list);
        }
        return $array;
    }

    public function showBookmarkList($id){
        $user = JWTAuth::user();
        $user_id = $user->id;
        $bookmarkList = BookmarkList::find($id);
        if($user_id != $bookmarkList->user_id && $bookmarkList->isVisible == 0 && $user->isAdmin == 0){
            return response()->json([
                'status' => 'error',
                'message' => 'Tento zoznam nie je verejný.',
            ],401);
        }
        else
            return $bookmarkList;
    }

    public function getContent($id){
        $user = JWTAuth::user();
        $user_id = $user->id;
        $bookmarkList = BookmarkList::find($id);
        $pivot_items = DB::table('bookmarklists_bookmarks')->where('bookmarklist_id',$id)->get();
        $sorted = $pivot_items->sortBy('order');

        if($user_id != $bookmarkList->user_id && $bookmarkList->isVisible == 0 && $user->isAdmin == 0){
            return response()->json([
                'status' => 'error',
                'message' => 'Tento zoznam nie je verejný.',
            ],401);
        }
        else{
            $bookmarks = array();
            foreach ($sorted as $item){
                $bookmark = Bookmark::find($item->bookmark_id);
                $bookmark->order = $item->order;
                array_push($bookmarks, $bookmark);
            }
        }
        return $bookmarks;
    }
}
