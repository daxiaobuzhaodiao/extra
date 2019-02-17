<?php

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('user_addresses', 'UserAddressesController', ['only' => ['index']]);
});
