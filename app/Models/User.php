<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $table = 'users';
    protected $fillable = [
        'id',
        'fullname',
        'username',
        'handphone',
        'jk',
        'asal',
        'email',
        'role',
        'img',
        'password',
        'hex',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'id_user' => 'string',
    ];
}
