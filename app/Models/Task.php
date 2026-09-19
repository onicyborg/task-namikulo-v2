<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = true;
    protected $primaryKey = 'id';
    protected $table = 'task';
    protected $fillable = [
        'id',
        'client_id',
        'worker_id',
        'kode_task',
        'task',
        'order',
        'deadline',
        'price_order',
        'pay_worker',
        'margin',
        'task_status',
        'pay_status',
        'user_id',
    ];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id', 'id')->withTrashed();
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id')->withTrashed();
    }
}
