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
        Schema::create('class_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('kind');
            $table->string('level');
            $table->string('summary');
            $table->text('description');
            $table->unsignedSmallInteger('duration_minutes')->default(50);
            $table->unsignedTinyInteger('capacity');
            // Kelas untuk yang pertama kali: selalu tampil paling atas.
            $table->boolean('is_intro')->default(false);
            $table->string('color')->default('lime');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_types');
    }
};
