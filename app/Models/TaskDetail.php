<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDetail extends Model
{
    use HasFactory;
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $table = 'task_detail';
    protected $fillable = [
        'id',
        'kode_task',
        'deskripsi',
        'file',
        'user_id',
    ];
}
