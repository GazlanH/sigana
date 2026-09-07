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
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tiket', 30)->unique();
            $table->string('judul', 200);
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('restrict')->onUpdate('cascade');
            $table->text('wilayah_terdampak');
            $table->text('penyebab');
            $table->text('tindakan');
            $table->enum('dampak_aliran', ['mati_total', 'aliran_kecil', 'bertekanan_rendah'])->default('mati_total');
            $table->enum('status', ['investigasi', 'perbaikan', 'normalisasi', 'selesai'])->default('perbaikan');
            $table->dateTime('waktu_mulai');
            $table->dateTime('estimasi_selesai')->nullable();
            $table->dateTime('waktu_selesai_aktual')->nullable();
            $table->string('kontak_posko', 50)->default('0811-2345-6789');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
    }
};
