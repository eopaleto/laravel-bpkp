<?php

namespace Database\Factories;

use App\Models\KartuGudang;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KartuGudang>
 */
class KartuGudangFactory extends Factory
{
    protected $model = KartuGudang::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_barang' => fake()->numerify('######'),
            'nama_barang' => fake()->words(3, true),
            'unit_kerja_id' => fake()->numberBetween(1, 5),
            'tanggal_keluar' => fake()->dateTimeBetween('-30 days', 'now'),
            'jumlah_keluar' => fake()->numberBetween(1, 100),
            'sisa_stok' => fake()->numberBetween(0, 500),
            'jenis' => fake()->randomElement(['barang_keluar', 'barang_masuk']),
            'keterangan' => fake()->optional()->sentence(),
            'periode_tahun' => fake()->year(),
        ];
    }
}
