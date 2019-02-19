<?php

Auth::routes(['verify' => true]);

Route::get('/', 'ProductsController@index')->name('products.index');


Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::resource('user_addresses', 'UserAddressesController');
    Route::post('products/{product}/favor', 'ProductsController@favor')->name('products.favor');
    Route::delete('products/{product}/disfavor', 'ProductsController@disfavor')->name('products.disfavor');
    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');
});

Route::get('products/{product}', 'ProductsController@show')->name('products.show');

