<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class SimrsUser extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'simrs_users';
    protected $primaryKey = 'user_id';
    public $timestamps = false; // karena kita pakai manual created_at & updated_at

    protected $fillable = [
        'user_full_name',
        'user_username',
        'user_password',
        'created_at',
        'updated_at',
    ];
}
