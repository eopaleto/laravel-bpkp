<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_id')->nullable()->after('keterangan');
            $table->foreign('kategori_id')->references('id')->on('kategori')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropForeign(['kategori_id']);
            $table->dropColumn('kategori_id');
        });
    }
};
