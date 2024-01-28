<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'social_id',
        'user_id',
        'social_user_id'
    ];

    protected $casts = [
        'social_id' => 'integer',
        'user_id' => 'integer',
        'social_user_id' => 'string'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
