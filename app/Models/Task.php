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
        'category_id',
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

    protected static function booted(): void
    {
        static::deleting(function (Task $task) {
            if ($task->isForceDeleting()) {
                $task->academic()->withTrashed()->forceDelete();
            } else {
                $task->academic()->delete();
            }
        });
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id', 'id')->withTrashed();
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id')->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    public function academic()
    {
        return $this->hasOne(TaskAcademic::class, 'task_id');
    }
}
