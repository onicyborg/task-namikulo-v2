<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('password_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 255)->nullable();
            $table->text('password')->nullable();
            $table->integer('user_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('password_history');
    }
};
