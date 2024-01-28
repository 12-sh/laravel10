<?php
namespace App\Http\Services;

use App\Models\Social;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SocialiteService
{
    /**
     * 認証時のコールバックチェック
     *
     * @param Request $request
     * @param Social $social
     * @return void
     * @throws Exception
     */
    public function checkResponse(
        Request $request,
        Social $social
    ): void
    {
        $rule = $social->driver . 'Rule';
        $request->validate($this->$rule);
    }

    /**
     * ラインログイン認証時のコールバックチェック
     *
     * @param  Request $request
     * @return bool
     */
    protected function lineRule(
    ): array
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