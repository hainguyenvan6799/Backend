<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class Role extends Model
{
    use HasFactory;
    protected $collection = "roles";
    protected $primaryKey = "role_id";

    protected $fillable = ['role_name', 'active'];

    public function users(){
        return $this->hasMany(UsersRoles::class, 'role_id');
    }

    public function resources(){
        return $this->hasMany(ResourceRole::class, 'role_id');
    }
}
