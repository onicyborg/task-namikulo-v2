<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $table = 'client';
    protected $fillable = [
        'id',
        'customer',
        'handphone',
        'jk',
        'asal',
        'user_id',
    ];
}
