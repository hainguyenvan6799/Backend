<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class BinhLuan extends Model
{
    use HasFactory;
    protected $collection = "binhluan";
    protected $fillable = ["noidungbinhluan", "files"];

    public function user()
    {
        return $this->belongsTo(User::class, 'mauser', 'mauser');
    }

    public function chude()
    {
        return $this->belongsTo(ChuDe::class, 'machude', 'machude');
    }
}
