<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class UsersRoles extends Model
{
    use HasFactory;
    protected $collection = "users_roles";

    protected $fillable = ['mauser', 'role_id'];

    public function user(){
        return $this->belongsTo(User::class, 'mauser');
    }

    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }
}
