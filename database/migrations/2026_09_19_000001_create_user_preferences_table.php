<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->unique();
            $table->enum('layout', ['light', 'dark'])->default('light');
            $table->enum('sidebar_color', ['light', 'dark'])->default('dark');
            $table->string('theme_color', 20)->default('blue');
            $table->boolean('mini_sidebar')->default(false);
            $table->boolean('sticky_header')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
