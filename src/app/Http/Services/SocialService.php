<?php
namespace App\Http\Services;

use App\Models\Social;
use Illuminate\Support\Collection;

class SocialService
{
    /**
     * ソーシャル情報を1件取得
     *
     * @param array $params
     * @return Social|null
     */
    public function first(
        array $params
    ): ?Social
    {
        $query = Social::query();
        foreach ($params as $column => $value) {
            $query->where($column, $value);
        }
        return $query->first();
    }

    /**
     * ソーシャル情報を取得
     *
     * @return Collection
     */
    public function list(
    ): Collection
    {
        return Social::all();
    }
}