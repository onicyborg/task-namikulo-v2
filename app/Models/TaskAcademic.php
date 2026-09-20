<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskAcademic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_academic';

    protected $fillable = [
        'task_id', 'prodi', 'judul', 'keterangan', 'is_lanjutan_metopen',
        'tugas_1', 'tugas_2', 'tugas_3', 'tugas_4',
    ];

    protected $casts = [
        'is_lanjutan_metopen' => 'boolean',
        'tugas_1' => 'boolean', 'tugas_2' => 'boolean',
        'tugas_3' => 'boolean', 'tugas_4' => 'boolean',
    ];

    protected $appends = ['progress_count'];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function getProgressCountAttribute(): int
    {
        return collect([$this->tugas_1, $this->tugas_2, $this->tugas_3, $this->tugas_4])
            ->filter(fn ($value) => (bool) $value)->count();
    }
}
