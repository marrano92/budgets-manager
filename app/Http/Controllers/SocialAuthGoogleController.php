<?php

namespace App\Http\Controllers;

use App\SocialGoogleAccountService;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthGoogleController extends Controller {

    /**
     * Create a redirect method to google api.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect() {
        return Socialite::driver( 'google' )->redirect();
    }

    /**
     * Return a callback method from google api.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback() {
        $service = new SocialGoogleAccountService();
        $user    = $service->createOrGetUser( Socialite::driver( 'google' )->user() );
        auth()->login( $user );

        return redirect()->route( 'home' );
    }
}
