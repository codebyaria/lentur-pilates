<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Versi bahasa Inggris (/en) untuk teks yang tampil di situs publik.
 * Kosong = pakai teks bahasa Indonesia.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('summary_en')->nullable()->after('summary');
            $table->text('description_en')->nullable()->after('description');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
            $table->string('summary_en')->nullable()->after('summary');
        });

        Schema::table('instructors', function (Blueprint $table) {
            $table->string('tagline_en')->nullable()->after('tagline');
            $table->text('bio_en')->nullable()->after('bio');
            $table->json('specialties_en')->nullable()->after('specialties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_types', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'summary_en', 'description_en']);
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'summary_en']);
        });

        Schema::table('instructors', function (Blueprint $table) {
            $table->dropColumn(['tagline_en', 'bio_en', 'specialties_en']);
        });
    }
};
