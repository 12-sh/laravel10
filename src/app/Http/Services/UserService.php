<?php
namespace App\Http\Services;

use App\Models\Social;
use App\Models\SocialUser;
use App\Models\User;
use Laravel\Socialite\Contracts\User as ContractsUser;

class UserService extends Controller
{
    public function findBySocialUserId(string $uniqeId) {
        return SocialUser::where('unique_id', $uniqeId)
            ->first()
            ->user;
    }

    public function createBySocialUser(ContractsUser $socialUser, string $social) {
        $user = new User();
        $user->name = $socialUser->name;
        $user->email = $socialUser->email;
        $user->save();

        $socialId = Social::where('name', $social)->value('id');
        $user->social->attach($socialId, ['unique_id' => $socialUser->id]);

        return $user;
    }

    public function updateBySocialUser(ContractsUser $socialUser, User $user) {
        $user->name = $socialUser->name;
        $user->email = $socialUser->email;
        $user->save();

        return $user;
    }
}