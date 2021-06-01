<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class OauthToken extends Model
{
    use HasFactory;
    protected $collection = 'oauth_token';
    protected $fillable = ['mauser', 'access_token', 'expires_in', 'refresh_token'];

    public function hasExpired(){
        return now()->gte($this->updated_at->addSeconds($this->expires_in));
    }
}
