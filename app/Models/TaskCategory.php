<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'task_category';

    protected $fillable = ['nama', 'tipe', 'is_system'];

    protected $casts = ['is_system' => 'boolean'];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'category_id');
    }
}
