<?php

Auth::routes();

Route::get( '/redirect', 'SocialAuthGoogleController@redirect' );
Route::get( '/callback', 'SocialAuthGoogleController@callback' );

Route::get( '/', 'HomeController@index' )->name( 'home' );
