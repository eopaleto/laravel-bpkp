<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('log_barang_keluar', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_kerja_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
    }
};
