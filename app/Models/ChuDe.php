<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class ChuDe extends Model
{
    use HasFactory;
    protected $collection = "chude";
    protected $primaryKey = "machude";
    protected $fillable = ['mota', 'noidung', 'trangthai', 'active', 'mauser', 'resource_id'];

    public function user(){
        return $this->belongsTo(User::class, 'mauser', 'mauser');
    }
}
