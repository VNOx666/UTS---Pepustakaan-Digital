<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk tabel dendas
 * Service: DendaService (Port 8004)
 * Database: db_denda_service
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pinjam_id');
            $table->decimal('nominal_denda', 10, 2)->default(0);
            $table->enum('status_bayar', ['lunas', 'belum_bayar'])->default('belum_bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dendas');
    }
};
