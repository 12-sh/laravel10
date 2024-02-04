<?php

namespace App\Http\Controllers\Auth\Socialite;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\Socialite\LoginRequest;
use App\Http\Requests\Auth\Socialite\ProviderCallbackRequest;
use App\Models\Social;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class SocialiteController extends Controller
{
    protected $redirectToAfterCallbackRoute;

    /**
     * ソーシャル認証
     *
     * @param  Request $request
     * @param  string $driver
     * @return Response
     */
    public function redirectToProvider(
        Request $request,
        string $driver
    ): Response
    {
        Log::info(sprintf("ソーシャルログイン リダイレクト開始[%s]", $driver));

        return Socialite::driver($driver)->redirect();
    }

    /**
     * ソーシャルコールバック
     *
     * @param  ProviderCallbackRequest $request
     * @param  string $driver
     * @return RedirectResponse
     */
    public function handleProviderCallback(
        ProviderCallbackRequest $request,
        Social $social,
    ): RedirectResponse
    {
        Log::info(sprintf("ソーシャルログイン コールバック開始[%s]", $social->driver), $request->all());
        $socialiteUserId = $request->getSocialiteUser($social);
        Log::info(sprintf("ソーシャルログイン コールバック完了[%s][%s]", $social->driver, $socialiteUserId));

        return redirect()->route('socialite.authenticate', ['social' => $social->driver]);
    }
}
