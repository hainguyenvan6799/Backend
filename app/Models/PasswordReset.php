<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class PasswordReset extends Model
{
    use HasFactory;
    protected $collection = 'password_resets';
}
