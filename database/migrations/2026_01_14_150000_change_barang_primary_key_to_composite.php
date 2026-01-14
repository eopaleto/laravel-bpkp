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
            // Drop the old single primary key
            $table->dropPrimary(['kode']);
            
            // Add composite primary key
            $table->primary(['kode', 'periode_tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Drop composite primary key
            $table->dropPrimary(['kode', 'periode_tahun']);
            
            // Restore single primary key
            $table->primary(['kode']);
        });
    }
};
