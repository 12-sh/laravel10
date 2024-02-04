<?php

namespace App\Http\Requests\Auth\Socialite;

use App\Models\Social;
use Exception;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class ProviderCallbackRequest extends FormRequest
{
    /**
     * バリデーション失敗時のリダイレクトルート
     *
     * @var string
     */
    protected $redirectRoute = 'login';

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
        $rule = $this->route('social')->driver . 'Rule';
        return $this->$rule();
    }

    /**
     * リクエストが認可チェックに合格したかどうかを確認
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        //getで取得したパラメータをmergeする。
        $this->merge($this->all());
    }

    /**
     * ソーシャルユーザー情報を取得
     *
     * @param Social $social
     * @return int
     */
    public function getSocialiteUser(Social $social)
    {
        try {
            $socialiteUser = Socialite::driver($social->driver)->user();
            $this->session()->put('socialiteUser', [
                'id' => $socialiteUser->id,
                'name' => $socialiteUser->name,
                'email' => $socialiteUser->email
            ]);
        } catch (RequestException|InvalidStateException|Exception $e) {
            Log::warning("ソーシャルログイン ソーシャルユーザー取得失敗");
            Log::warning($e->__toString());
            throw ValidationException::withMessages([
                'social.' . $social->driver => __('socialite.failed', ['social' => $social->name]),
            ]);
        }

        return $socialiteUser->id;
    }

    /**
     * ラインログイン認証時のコールバックチェック
     *
     * @param  Request $request
     * @return array
     */
    protected function lineRule(): array
    {
        return [
            'code' => 'required',
            'state' => 'required',
            'friendship_status_changed' => 'required:accepted',
            'error' => 'exclude',
            'error_description' => 'exclude',
        ];
    }
}
