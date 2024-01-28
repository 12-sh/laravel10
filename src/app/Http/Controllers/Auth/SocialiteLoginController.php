<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\SocialiteService;
use App\Http\Services\SocialService;
use App\Http\Services\UserService;
use App\Models\Social;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class SocialiteLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $socialiteService;
    protected $socialService;
    protected $userService;

    public function __construct(
        SocialiteService $socialiteService,
        SocialService $socialService,
        UserService $userService
    ) {
        $this->socialiteService = $socialiteService;
        $this->socialService = $socialService;
        $this->userService = $userService;
    }

    /**
     * ソーシャル認証
     *
     * @param Request $request
     * @param string $driver
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
     * @param Request $request
     * @param string $driver
     * @return RedirectResponse
     */
    public function handleProviderCallback(
        Request $request,
        Social $social,
    ): RedirectResponse
    {
        Log::info(sprintf("ソーシャルログイン コールバック開始[%s]", $social->driver), $request->all());

        try {
            // レスポンス内容をチェック
            $this->socialiteService->checkResponse($request, $social);

            // ソーシャルユーザー情報取得
            $socialiteUser = Socialite::driver($social->driver)->user();

            $request->session()->put('socialiteUser', $socialiteUser);

            Log::info(sprintf("ソーシャルログイン コールバック完了[%s][%s]", $social->driver), $socialiteUser->id);
        } catch (ValidationException $e) {
            Log::warning("ソーシャルログイン 失敗 認証コード取得失敗", $e);
        } catch (RequestException $e) {
            Log::warning("ソーシャルログイン 失敗 ソーシャルユーザー取得失敗", $e);
        } finally {
            return $this->sendFailedLoginResponse($request, $social->driver);
        }

        return redirect()->route('socialite.confimation', ['social' => $social->driver]);
    }

    /**
     * ソーシャルユーザー情報の登録確認
     *
     * @param Request $request
     * @param Social $social
     * @return View
     */
    public function confirmation(
        Request $request,
        Social $social
    ): View
    {
        if ($request->session()->has('socialiteUser')) {
            $this->sendFailedLoginResponse($request, $social->driver);
        }

        $socialiteUser = $request->session()->pull('socialiteUser');

        // ソーシャルユーザー情報を元に既存ユーザ情報を取得
        $user = $this->userService->findBySocialUserId($socialiteUser->id);

        return view('socialite.confirmation', [
            'social' => $social,
            'socialiteUser' => $socialiteUser,
            'user' => $user
        ]);
    }

    /**
     * ソーシャルユーザー登録
     *
     * @param Request $request
     * @param Social $social
     * @return RedirectResponse
     */
    public function register(
        Request $request,
        Social $social,
    ): Response
    {
        try {
            // ユーザー登録or更新
            $params = $request->only(['name', 'email', 'social_user_id']);
            $user = $this->userService->createOrUpdate($social, $params);
        } catch (QueryException $e) {
            Log::warning("ソーシャルログイン 失敗 DBエラー", $e);
            return $this->sendFailedLoginResponse($request, $social->driver);
        }

        // ログイン
        // $this->guard()->login($user, true);

        Log::info("ソーシャルログイン 認証完了");
        return $this->sendLoginResponse($request);
    }

    /**
     * ソーシャルログイン失敗時の例外処理
     *
     * @param  Request  $request
     * @return void
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse(
        Request $request,
        string $name
    ): RedirectResponse
    {
        return redirect()
            ->route('login')
            ->withErrors([
                'social.' . $name => trans('socialite.failed', ['social' => $name])
            ]);
    }

    /**
     * ユーザーをリダイレクトするパスの取得
     *
     * @param  Request  $request
     * @return string
     */
    protected function redirectTo(
        $request
    ): string
    {
        return route('login');
    }
}
