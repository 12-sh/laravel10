<?php

namespace App\Http\Controllers\Auth\Socialite;

use App\Http\Requests\Auth\Socialite\AuthenticateRequest;
use App\Models\Social;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Log;

class LoginController extends SocialiteController
{
    /**
     * ログイン認証を行う
     *
     * @param  AuthenticateRequest $request
     * @param  Social $social
     * @return void
     */
    public function authenticate(AuthenticateRequest $request, Social $social) {
        Log::info(sprintf("ソーシャルログイン 認証開始[%s]", $social->driver));
        $socialUserId = $request->authenticate($social);
        Log::info(sprintf("ソーシャルログイン 認証完了[%s][ID：%s]", $social->driver, $socialUserId));

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
