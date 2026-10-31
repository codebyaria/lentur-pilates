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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('classes_total');
            $table->unsignedSmallInteger('classes_remaining');
            // Harga saat dibeli, supaya riwayat tidak berubah kalau harga paket diganti.
            $table->unsignedInteger('price_paid');
            $table->string('payment_status');
            $table->string('payment_reference')->unique();
            $table->dateTime('purchased_at');
            $table->dateTime('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
