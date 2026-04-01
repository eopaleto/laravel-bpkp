<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-start gap-3">
            <x-filament::button type="submit">
                Tambah Barang
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
