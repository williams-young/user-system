<?php

Route::group([], function () {
    Route::get('login', 'AdminController@login');
    Route::get('logout', 'AdminController@logout');
    Route::post('login', 'AdminController@postLogin');
});

Route::group(['middleware' => 'admin'], function () {

    Route::get('/', 'AdminController@dashboard');


});