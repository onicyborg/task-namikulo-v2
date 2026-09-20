<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL may leave the table behind when the original FK creation fails.
        // Complete that partial table instead of trying to create it again.
        if (Schema::hasTable('task_academic')) {
            DB::statement('ALTER TABLE `task_academic` MODIFY `task_id` INT NOT NULL');
            Schema::table('task_academic', function (Blueprint $table) {
                $table->unique('task_id');
                $table->foreign('task_id')->references('id')->on('task')->cascadeOnDelete();
            });
            return;
        }

        Schema::create('task_academic', function (Blueprint $table) {
            $table->increments('id');
            // The legacy task table uses a signed INT primary key.
            $table->integer('task_id')->unique();
            $table->string('prodi', 255);
            $table->text('judul');
            $table->text('keterangan')->nullable();
            $table->boolean('is_lanjutan_metopen')->default(false);
            $table->boolean('tugas_1')->default(false);
            $table->boolean('tugas_2')->default(false);
            $table->boolean('tugas_3')->default(false);
            $table->boolean('tugas_4')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('task')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_academic');
    }
};
