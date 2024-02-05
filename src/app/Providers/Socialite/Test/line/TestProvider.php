<?php

namespace App\Providers\Socialite\Test\line;

use App\Models\User;
use Faker\Factory;
use Illuminate\Http\Request;
use SocialiteProviders\Line\Provider;

class TestProvider extends Provider
{
    protected $faker;

    /**
     * LINE用テストプロバイダーのインスタンスを作成
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $clientId
     * @param  string  $clientSecret
     * @param  string  $redirectUrl
     * @param  array  $guzzle
     * @return void
     */
    public function __construct(
        Request $request,
        string $clientId,
        string $clientSecret,
        string $redirectUrl,
        array $guzzle = []
    )
    {
        $this->faker = Factory::create("ja_JP");
        parent::__construct($request, $clientId, $clientSecret, $redirectUrl, $guzzle);
    }

    /**
     * 直接コールバックにリダイレクト
     *
     * @param string $state
     *
     * @return string
     */
    protected function getAuthUrl(
        $state
    ): string
    {
        return route('socialite.callback', [
            'social' => 'line',
            'state' => $state,
            'code' => $this->faker->hexColor(),
            'friendship_status_changed' => 'true',
        ]);
    }

    /**
     * テスト用アクセストークンを返却
     *
     * @param  string  $code
     * @return array
     */
    public function getAccessTokenResponse(
        $code
    ): array
    {
        return [
            'access_token' => $this->faker->md5(),
            "expires_in" => $this->faker->unixTime(),
            "refresh_token" => $this->faker->md5(),
            "scope" => "profile",
            "token_type" => "Bearer"
        ];
    }

    /**
     * ユーザー情報を返却
     *
     * @param string $token
     *
     * @return array
     */
    protected function getUserByToken(
        $token
    ): array
    {
        $user = User::inRandomOrder()->first();
        return [
            "userId" => $user->socials->where('driver', 'line')->first()->pivot->social_user_id,
            "displayName" => $user->name,
            "pictureUrl" => "https://profile.line-scdn.net/abcdefghijklmn",
            "statusMessage" => $this->faker->realText(10)
        ];
    }
}
