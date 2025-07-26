<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        return new Barang([
            'kode'        => $row['kode'] ?? null,
            'sku'         => $row['sku'] ?? null,
            'nama'        => $row['nama'] ?? null,
            'hargabeli'   => $row['hargabeli'] ?? null,
            'hargajual'   => $row['hargajual'] ?? null,
            'keterangan'  => $row['keterangan'] ?? null,
            'kategori_id' => $row['kategori_id'] ?? null,
            'satuan'      => $row['satuan'] ?? null,
            'terjual'     => $row['terjual'] ?? 0,
            'terbeli'     => $row['terbeli'] ?? 0,
            'sisa'        => $row['sisa'] ?? 0,
            'stokmin'     => $row['stokmin'] ?? 0,
            'barcode'     => $row['barcode'] ?? null,
            'brand'       => $row['brand'] ?? null,
            'lokasi'      => $row['lokasi'] ?? null,
            'expired'     => $row['expired'] ?? null,
            'warna'       => $row['warna'] ?? null,
            'ukuran'      => $row['ukuran'] ?? null,
            'avatar'      => $row['avatar'] ?? null,
        ]);
    }
}
