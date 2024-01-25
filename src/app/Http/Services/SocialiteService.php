<?php
namespace App\Http\Services;

use Illuminate\Http\Request;

class SocialiteService extends Controller
{
    public function checkResponse(Request $request, string $social) {
        return $this->$social($request);
    }

    protected function line(Request $request) {
        if ($request->has('error')) {
            Log::warning(sprintf("ソーシャルログイン LINE認証エラー"), $request->all());
            return false;
        }
        return true;
    }
}