<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use App\Models\LopHoc;
use App\Models\Role;
use App\Models\Users_Roles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $collection = 'users';
    protected $primaryKey = "mauser";
    protected $fillable = [
        'mauser',
        'name',
        'email',
        'password',
        'sdt',
        'malop',
        'group',
        'is_updated_info',
        'room_chat_id',
        'sex',
        'active',
        'user_chat_id',
        'code', // trường này mới được thêm vào, càn xem xét lại,
        'private_files',
        // 'mauser',
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function lophoc(){
        return $this->belongsTo(LopHoc::class, 'malop');
    }

    // public function roles(){
    //     return $this->belongsToMany('App\Models\Role', 'users_roles', 'mauser', 'role_id');
    // }
    public function roles(){
        return $this->hasMany(UsersRoles::class, 'mauser');
    }

    public function accessTokens()
    {
        return $this->hasMany(OauthAccessToken::class);
    }
}
