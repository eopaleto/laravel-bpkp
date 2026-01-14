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
        Schema::table('barang', function (Blueprint $table) {
            $table->integer('periode_tahun')->index();
        });

        Schema::table('log_barang_masuk', function (Blueprint $table) {
            $table->integer('periode_tahun')->index();
        });

        Schema::table('log_barang_keluar', function (Blueprint $table) {
            $table->integer('periode_tahun')->index();
        });

        Schema::table('log_permintaan', function (Blueprint $table) {
            $table->integer('periode_tahun')->index();
        });

        Schema::table('permintaan_checkout', function (Blueprint $table) {
            $table->integer('periode_tahun')->index();
        });

        schema::table('permintaan_checkout_items', function (Blueprint $table) {
            $table->integer('periode_tahun')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            //
        });
    }
};
