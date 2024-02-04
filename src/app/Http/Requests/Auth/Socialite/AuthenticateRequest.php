<?php

namespace App\Http\Requests\Auth\Socialite;

use App\Http\Services\UserService;
use App\Models\Social;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthenticateRequest extends FormRequest
{
    /**
     * バリデーション失敗時のリダイレクトルート
     *
     * @var string
     */
    protected $redirectRoute = 'login';

    /**
     * ユーザー情報サービス
     *
     * @var UserService
     */
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        parent::__construct();
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }

    /**
     * リクエストが認可チェックに合格したかどうかを確認
     *
     * @param  Social $social
     * @return User
     */
    public function authenticate(Social $social)
    {
        // ユーザーログイン
        $socialiteUser = $this->session()->pull('socialiteUser');

        if ($socialiteUser) {
            $user = $this->userService->findBySocialUserId($socialiteUser['id']);
        }
    
        if ($user && $user->id !== null) {
            Auth::login($user);
        }

        Log::warning("ソーシャルログイン 失敗エラー");
        throw ValidationException::withMessages([
            'social.' . $social->driver => __('socialite.failed', ['social' => $social->name]),
        ]);

    }

}
