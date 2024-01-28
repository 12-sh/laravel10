<?php
namespace App\Http\Services;

use App\Models\Social;
use App\Models\SocialUser;
use App\Models\User;
use Laravel\Socialite\Contracts\User as ContractsUser;

class UserService
{
    /**
     * ソーシャルユーザー情報によりユーザー情報を取得
     *
     * @param  integer $socialUserId
     * @return User
     */
    public function findBySocialUserId(
        int $socialUserId
    ): User
    {
        return SocialUser::where('social_user_id', $socialUserId)
            ->first()
            ->user;
    }

    /**
     * ユーザー情報を登録or更新
     *
     * @param Social $social
     * @param array  $params
     * @return User
     */
    public function createOrUpdate(
        Social $social,
        array $params,
    ): User
    {
        $user = $this->findBySocialUserId($params['social_user_id']);
        if (!$user) {
            return $this->createBySocialUser($social, $params);
        } elseif ($user->social && $user->social->driver == $social->driver) {
            return $this->updateBySocialUser($user, $social, $params);
        }
        throw new Exception(
            sprintf("既存ユーザーとドライバー不一致[id:%s][パラメータ:%s]", $user->id, $social->driver)
        );
    }

    /**
     * ソーシャルユーザー情報によりユーザー情報を登録
     *
     * @param  Social $social
     * @param  array $params
     * @return User
     */
    public function createBySocialUser(
        Social $social,
        array $params
    ): User
    {
        return DB::transaction(function ($params, $social) {
            $user = $this->create($params);

            $param = ['social_user_id' => $params['id']];
            $user = $this->syncToSocial($user, $social, $param);

            Log::info(sprintf("ソーシャルログイン ユーザー登録 [id:%s]", $user->id));
            return $user;
        });
    }

    /**
     * ソーシャルユーザー情報によりユーザー情報を更新
     *
     * @param  User $user
     * @param  Social $social
     * @param  array $params
     * @return User
     */
    public function updateBySocialUser(
        User   $user,
        Social $social,
        array  $array,
    ): User
    {
        return DB::transaction(function ($user, $social, $params) {
            $user = $this->update($user, $params);

            if (!$user->social) {
                $param = ['social_user_id' => $params['id']];
                $user = $this->syncToSocial($user, $social, $param);
            }
            Log::info(sprintf("ソーシャルログイン ユーザー更新 [id:%s]", $user->id));
            return $user;
        });
    }

    /**
     * ユーザー情報を登録
     *
     * @param array $param
     * @return User
     */
    public function create(
        array $param
    ): User
    {
        $user = new User();
        $user->name = $param['name'];
        $user->email = $param['email'];
        $user->save();
        return $user;
    }

    /**
     * ユーザー情報を更新
     *
     * @param User $user
     * @param array $param
     * @return User
     */
    function update(
        User $user,
        array $param
    ): User
    {
        $user->save($param);
        return $user;
    }

    /**
     * ユーザー情報にソーシャル情報を追加
     *
     * @param User $user
     * @param Social $social
     * @param array $param
     * @return User
     */
    public function syncToSocial(
        User $user,
        Social $social,
        array $param
    ): User
    {
        $socialId = Social::where('driver', $social->driver)->value('id');
        $user->social->sync($param);
        return $user;
    }
}