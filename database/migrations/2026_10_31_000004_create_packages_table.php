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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('summary');
            $table->unsignedSmallInteger('class_count');
            // Rupiah, tanpa desimal.
            $table->unsignedInteger('price');
            $table->unsignedSmallInteger('validity_days');
            $table->boolean('is_intro')->default(false);
            // Untuk berdua: kuota dipakai bersama teman.
            $table->boolean('is_shareable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
