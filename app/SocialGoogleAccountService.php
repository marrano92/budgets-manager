<?php

namespace App;

use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialGoogleAccountService {

    public function createOrGetUser( ProviderUser $providerUser ) {

        try {
            $account = SocialGoogleAccount::whereProvider( 'google' )
                                          ->whereProviderUserId( $providerUser->getId() )
                                          ->first();

            if ( $account ) {
                return $account->user;
            } else {
                $account = new SocialGoogleAccount( [
                    'provider_user_id' => $providerUser->getId(),
                    'provider'         => 'google'
                ] );

                $user    = User::whereEmail( $providerUser->getEmail() )->first();

                if ( ! $user ) {
                    $user = User::create( [
                        'email'    => $providerUser->getEmail(),
                        'name'     => $providerUser->getName(),
                        'password' => md5( rand( 1, 10000 ) ),
                        'image'    => $providerUser->getAvatar(),
                    ] );
                }

                $account->user()->associate( $user );
                $account->save();
            }
        } catch ( \Exception $exception ) {
            var_dump( $exception->getMessage() );
            die;
        }

        return $user;
    }

}
