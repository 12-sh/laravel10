<?php
namespace App\Http\Services;

use App\Models\SocialUser;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserService
{
    /**
     * ソーシャルユーザー情報によりユーザー情報を取得
     *
     * @param  integer $socialUserId
     * @return User
     */
    public function findBySocialUserId(
        string $socialUserId
    ): User
    {
        Log::debug("in findBySocialUserId");
        $socialUser = SocialUser::where('social_user_id', $socialUserId)
            ->first();

        if ($socialUser) {
            return $socialUser->user;
        }
        return new User();
    }
}