<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $table = 'worker';
    protected $fillable = [
        'id',
        'worker',
        'handphone',
        'jk',
        'asal',
        'username',
        'status',
        'user_id',
    ];
}
