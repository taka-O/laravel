<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->longText('description');
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->timestamps();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->index(['start_at', 'end_at']);
        });

        Schema::create('course_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->index();
            $table->foreignId('user_id');
            $table->string('user_type');
            $table->timestamps();
        });

        Schema::table('course_users', function (Blueprint $table) {
            $table->index(['user_type', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
