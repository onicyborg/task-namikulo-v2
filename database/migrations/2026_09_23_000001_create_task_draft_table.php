<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_draft', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->integer('category_id');
            $table->string('kode_request', 255)->unique();
            $table->text('task')->nullable();
            $table->date('order')->nullable();
            $table->date('deadline')->nullable();
            $table->string('prodi', 255)->nullable();
            $table->text('judul')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('is_lanjutan_metopen')->default(false);
            $table->string('status', 30)->default('pending');
            $table->dateTime('assigned_at')->nullable();
            $table->integer('assigned_by')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->index(['status', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_draft');
    }
};
