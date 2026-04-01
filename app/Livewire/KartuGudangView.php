<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\KartuGudang;
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

        // Get data dari KartuGudang (dengan global scope periode_tahun)
        $kartuGudang = KartuGudang::with(['barang', 'unitKerja'])->get();
        foreach ($kartuGudang as $log) {
            $items->push([
                'nama_barang' => $log->nama_barang,
                'unit_kerja' => $log->unitKerja?->name,
                'tanggal' => $log->tanggal_keluar,
                'jumlah' => $log->jumlah_keluar,
                'sisa_stok' => $log->sisa_stok,
                'jenis' => $log->jenis,
            ]);
        }

        // Get all barang names dari tabel barang sesuai periode (dengan global scope periode_tahun)
        $allBarang = Barang::pluck('nama')->unique()->sort();
        
        // Ensure all barang appear in items, even if empty
        foreach ($allBarang as $namaBarang) {
            if (!$items->pluck('nama_barang')->contains($namaBarang)) {
                $items->push([
                    'nama_barang' => $namaBarang,
                    'unit_kerja' => null,
                    'tanggal' => null,
                    'jumlah' => 0,
                    'sisa_stok' => 0,
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
        ]);
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




