<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskDraft extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_draft';

    protected $fillable = [
        'client_id', 'category_id', 'task_id', 'kode_request', 'task', 'order', 'deadline',
        'prodi', 'judul', 'keterangan', 'is_lanjutan_metopen', 'status',
        'assigned_at', 'assigned_by',
    ];

    protected $casts = [
        'is_lanjutan_metopen' => 'boolean',
        'assigned_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id')->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'category_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id')->withTrashed();
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by')->withTrashed();
    }
}
