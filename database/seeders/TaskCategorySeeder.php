<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['nama' => 'Parafrase', 'tipe' => 'general', 'is_system' => true],
            ['nama' => 'Artikel Ilmiah', 'tipe' => 'artikel_ilmiah', 'is_system' => true],
            ['nama' => 'Metopen', 'tipe' => 'metopen', 'is_system' => true],
        ] as $category) {
            TaskCategory::withTrashed()->updateOrCreate(
                ['nama' => $category['nama']],
                array_merge($category, ['deleted_at' => null])
            );
        }
    }
}
