<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\LogBarangKeluar;
use App\Models\LogBarangMasuk;
use App\Models\Barang;

class KartuGudangView extends Component
{
    public $search = '';
    public $filterJenis = '';
    public $expandedGroups = [];
    public $page = 1;
    public $itemsPerPage = 10;

    protected $queryString = ['search', 'filterJenis', 'page'];

    public function updatingSearch()
    {
        $this->page = 1;
    }

    public function updatingFilterJenis()
    {
        $this->page = 1;
    }

    public function toggleGroup($namaBarang)
    {
        if (in_array($namaBarang, $this->expandedGroups)) {
            $this->expandedGroups = array_filter($this->expandedGroups, fn($item) => $item !== $namaBarang);
        } else {
            $this->expandedGroups[] = $namaBarang;
        }
    }

    public function getGroupedData()
    {
        $items = collect();

        // Get data dari LogBarangKeluar
        $keluar = LogBarangKeluar::with(['barang', 'unit_kerja'])->get();
        foreach ($keluar as $log) {
            $items->push([
                'nama_barang' => $log->barang?->nama ?? $log->kode_barang,
                'unit_kerja' => $log->unit_kerja?->name,
                'tanggal' => $log->created_at,
                'jumlah' => $log->jumlah,
                'jenis' => 'barang_keluar',
            ]);
        }

        // Get data dari LogBarangMasuk
        $masuk = LogBarangMasuk::with(['barang', 'unit_kerja'])->get();
        foreach ($masuk as $log) {
            $items->push([
                'nama_barang' => $log->barang?->nama ?? $log->kode_barang,
                'unit_kerja' => $log->unit_kerja?->name,
                'tanggal' => $log->created_at,
                'jumlah' => $log->jumlah,
                'jenis' => 'barang_masuk',
            ]);
        }

        // Get all barang names dari tabel barang
        $allBarang = Barang::pluck('nama')->unique()->sort();
        
        // Ensure all barang appear in items, even if empty
        foreach ($allBarang as $namaBarang) {
            if (!$items->pluck('nama_barang')->contains($namaBarang)) {
                $items->push([
                    'nama_barang' => $namaBarang,
                    'unit_kerja' => null,
                    'tanggal' => null,
                    'jumlah' => 0,
                    'jenis' => null,
                ]);
            }
        }

        // Filter
        if ($this->search) {
            $items = $items->filter(fn($item) => 
                stripos($item['nama_barang'], $this->search) !== false
            );
        }

        if ($this->filterJenis) {
            $items = $items->filter(fn($item) => $item['jenis'] === $this->filterJenis || $item['jenis'] === null);
        }

        // Group by nama barang dan sort
        return $items->sortBy('nama_barang')->groupBy('nama_barang');
    }

    public function render()
    {
        $groupedData = $this->getGroupedData();
        
        // Convert to array dan paginate
        $allGroups = $groupedData->toArray();
        $totalItems = count($allGroups);
        $totalPages = ceil($totalItems / $this->itemsPerPage) ?: 1;

        // Ensure page is valid
        if ($this->page > $totalPages) {
            $this->page = $totalPages;
        }
        if ($this->page < 1) {
            $this->page = 1;
        }

        $paginatedGroups = array_slice($allGroups, (($this->page - 1) * $this->itemsPerPage), $this->itemsPerPage);

        return view('livewire.kartu-gudang-view', [
            'groupedData' => $paginatedGroups,
            'totalPages' => $totalPages,
            'currentPage' => $this->page,
            'totalItems' => $totalItems,
        ])->layout('empty');
    }

    public function nextPage()
    {
        if ($this->page < ceil(count($this->getGroupedData()->toArray()) / $this->itemsPerPage)) {
            $this->page++;
        }
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function gotoPage($page)
    {
        $this->page = $page;
    }
}



