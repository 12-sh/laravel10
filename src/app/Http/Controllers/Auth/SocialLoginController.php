<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\SocialiteService;
use App\Http\Services\UserService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    use AuthenticatesUsers;

    protected $socialiteService;
    protected $userService;

    public function __construct(
        SocialiteService $socialiteService,
        UserService $userService
    ) {
        $this->socialiteService = $socialiteService;
        $this->userService = $userService;
    }

    /**
     * ソーシャル認証
     *
     * @param Request $request
     * @param string $social
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function redirectToProvider(Request $request, string $social)
    {
        Log::info(sprintf("ソーシャルログイン リダイレクト開始[%s]", $social));
        return Socialite::driver($social)->redirect();
    }

    /**
     * ソーシャルコールバック
     *
     * @param Request $request
     * @param string $social
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleProviderCallback(Request $request, string $social)
    {
        Log::info(sprintf("ソーシャルログイン コールバック開始[%s]", $social), $request->all());

        if (!$this->socialiteService->checkResponse($request, $social)) {
            return $this->sendFailedLoginResponse($request);
        }
        $socialiteUser = Socialite::driver($social)->user();

        $user = $this->userService->findBySocialUserId($socialiteUser->id);
        if (!$user) {
            $user = $this->userService->createBySocialUser($socialiteUser, $social);
            Log::info(sprintf("ソーシャルログイン ユーザー登録 [id:%s]", $user->id));
        } elseif ($user->social->name == $social) {
            $user = $this->userService->updateBySocialUser($socialiteUser, $user);
            Log::info(sprintf("ソーシャルログイン ユーザー更新 [id:%s]", $user->id));
        } else {
            Log::info("ソーシャルログイン 既存ユーザーと不一致[id:%s][パラメータ:%s]", $user->id, $social);
            return $this->sendFailedLoginResponse($request);
        }

        $this->guard()->login($user, true);

        Log::info("ソーシャルログイン 認証完了");
        return $this->sendLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            $this->username() => [trans('socialite.failed')],
        ]);
    }

    /**
     * ユーザーをリダイレクトするパスの取得
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        return route('login');
    }
}
