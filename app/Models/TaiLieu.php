<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;


class TaiLieu extends Model
{
    use HasFactory;
    protected $collection = "tailieu";
    protected $primaryKey = "matailieu";
    protected $fillable = ["tentailieu", "mota", "file", "active", '_id', 'mauser'];

    public function user()
    {
        return $this->belongsTo(User::class, 'mauser', 'mauser');
    }
}
