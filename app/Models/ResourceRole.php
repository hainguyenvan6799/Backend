<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ResourceRole extends Model
{
    use HasFactory;
    protected $collection = 'resource_role';
    protected $primaryKey = '_id';

    protected $fillable = ['can_read', 'can_add', 'can_update', 'can_delete'];

    public function specific_resource(){
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    public function specific_role(){
        return $this->belongsTo(Role::class, 'role_id');
    }
}
