<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->nullable();
            $table->integer('worker_id')->nullable();
            $table->string('kode_task', 255)->nullable();
            $table->text('task')->nullable();
            $table->date('order')->nullable();
            $table->date('deadline')->nullable();
            $table->bigInteger('price_order')->nullable();
            $table->bigInteger('pay_worker')->nullable();
            $table->bigInteger('margin')->nullable();
            $table->string('task_status', 255)->nullable();
            $table->string('pay_status', 255)->nullable();
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
        Schema::dropIfExists('task');
    }
};
