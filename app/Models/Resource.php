<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;
    protected $collection = 'resource';
    protected $primaryKey = 'resource_id';
    protected $fillable = ['resource_name'];

    public function roles(){
        return $this->hasMany(ResourceRole::class, 'resource_id');
    }
}
