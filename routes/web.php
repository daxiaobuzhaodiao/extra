<?php

Auth::routes(['verify' => true]);

Route::get('/', 'HomeController@index')->name('home');
