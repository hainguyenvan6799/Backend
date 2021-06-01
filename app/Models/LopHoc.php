<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;
use App\Models\User;

class LopHoc extends Model
{
    use HasFactory;
    protected $collection = "lop";
    protected $primaryKey = "malop";

    public function users(){
        return $this->hasMany(User::class, 'malop', 'malop');
    }
}
