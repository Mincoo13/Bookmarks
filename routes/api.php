<?php



Route::post('login', 'API\AuthController@login');
Route::middleware('jwt.auth')->group(function(){

    Route::get('logout', 'API\AuthController@logout');
    Route::get('profile', 'UserController@showProfile');
    Route::patch('profile', 'UserController@editProfile');

        Route::patch('profile/{id}', 'UserController@editProfileAdmin')->middleware('admin');

});