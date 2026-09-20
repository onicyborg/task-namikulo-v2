<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 100);
            $table->string('tipe', 50)->default('general');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['nama', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_category');
    }
};
