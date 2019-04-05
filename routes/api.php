<?php



Route::post('login', 'API\AuthController@login');
Route::post('forgotten-password', 'UserController@resetPassword');
Route::post('register-user', 'UserController@registerUser');
Route::middleware('jwt.auth')->group(function(){

    Route::get('logout', 'API\AuthController@logout');
    Route::get('profile', 'UserController@showProfile');
    Route::patch('profile', 'UserController@editProfile');
    Route::patch('users/changepassword', 'UserController@changePassword');

    Route::post('categories', 'CategoryController@createCategory');
    Route::patch('categories/{id}', 'CategoryController@editCategory');
    Route::delete('categories/{id}', 'CategoryController@deleteCategory');
    Route::get('categories', 'CategoryController@showAllCategories');
    Route::get('categories/{id}', 'CategoryController@showCategory');

    Route::get('bookmarks','BookmarkController@getBookmarks');
    Route::post('bookmarks','BookmarkController@createBookmark');
    Route::patch('bookmarks/{id}','BookmarkController@editBookmark');
    Route::patch('bookmarks/{id}/mark-read','BookmarkController@markReadFlag');
    Route::delete('bookmarks/{id}','BookmarkController@deleteBookmark');
    Route::post('search-bookmarks','BookmarkController@searchBookmarks');

    Route::post('comments', 'CommentController@createComment');
    Route::patch('comments/{id}', 'CommentController@editComment');
    Route::delete('comments/{id}', 'CommentController@deleteComment');

    Route::post('bookmark-lists', 'BookmarkListController@createBookmarkList');
    Route::post('bookmark-lists/{id}', 'BookmarkListController@addBookmarkToList');
    Route::patch('bookmark-lists/{id}/order', 'BookmarkListController@setBookmarkOrder');
    Route::delete('bookmark-lists/{id}', 'BookmarkListController@deleteBookmark');

        Route::get('users', 'UserController@getUsers')->middleware('admin');
        Route::get('users/{id}', 'UserController@showUser')->middleware('admin');
        Route::delete('users/{id}', 'UserController@deleteUser')->middleware('admin');
        Route::patch('profile/{id}', 'UserController@editProfileAdmin')->middleware('admin');
        Route::patch('users/{id}/activate', 'UserController@activateUser')->middleware('admin');
        Route::patch('users/{id}/deactivate', 'UserController@deactivateUser')->middleware('admin');
});