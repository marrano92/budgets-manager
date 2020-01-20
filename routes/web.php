<?php

Auth::routes();

Route::get( '/redirect', 'SocialAuthGoogleController@redirect' );
Route::get( '/callback', 'SocialAuthGoogleController@callback' );

Route::get( '/', 'HomeController@index' )->name( 'home.index' );

Route::prefix( 'expense' )->group( function () {
    Route::get( '/', 'ExpenseController@index' )->name( 'expense.index' );
    Route::get( '/create', 'ExpenseController@create' )->name( 'expense.create' );
    Route::get( '/{expense}', 'ExpenseController@show' )->name( 'expense.show' );
    Route::post( '/', 'ExpenseController@store' )->name( 'expense.store' );
} );

Route::prefix( 'user' )->group( function () {
    Route::get( '/', 'UserController@index' )->name( 'user.index' );
} );
