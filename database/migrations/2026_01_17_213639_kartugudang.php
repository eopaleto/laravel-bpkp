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
        Schema::create('kartu_gudang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang');
            $table->string('nama_barang');
            $table->foreignId('unit_kerja_id')->constrained('unit_kerja')->onDelete('cascade');
            $table->dateTime('tanggal_keluar');
            $table->integer('jumlah_keluar');
            $table->integer('sisa_stok');
            $table->enum('jenis', ['barang_keluar', 'barang_masuk'])->default('barang_keluar');
            $table->text('keterangan')->nullable();
            $table->string('periode_tahun')->nullable();
            $table->timestamps();
            
            // Index untuk performa query
            $table->index('kode_barang');
            $table->index('tanggal_keluar');
            $table->index(['kode_barang', 'tanggal_keluar']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_gudang');
    }
};
