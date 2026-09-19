<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullname', 255)->nullable();
            $table->string('username', 255)->nullable();
            $table->string('handphone', 255)->nullable();
            $table->string('jk', 255)->nullable();
            $table->string('asal', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('role', 255)->nullable();
            $table->string('img', 255)->nullable();
            $table->string('hex', 255)->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
