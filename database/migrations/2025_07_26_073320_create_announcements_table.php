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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->longText('content');
            $table->unsignedInteger('category')->default(0);
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->timestamps();
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->index(['start_at', 'end_at']);
            $table->index('category');
        });

        Schema::create('announcement_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->index();
            $table->foreignId('course_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
