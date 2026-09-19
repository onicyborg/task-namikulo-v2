<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('kode_task', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('file', 255)->nullable();
            $table->integer('user_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('task_detail');
    }
};
