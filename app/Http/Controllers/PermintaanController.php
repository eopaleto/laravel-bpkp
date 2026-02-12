<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Crypt;

class PermintaanController extends Controller
{
    public function downloadPdf($token)
    {
        try {
            $id = Crypt::decryptString($token);
        } catch (\Exception $e) {
            abort(403, 'Token tidak valid');
        }

        $permintaan = Permintaan::with('items', 'user')->findOrFail($id);
        // Refresh relasi items dari database untuk mendapatkan data terbaru
        $permintaan->load('items');

        if ($permintaan->status !== 'Disetujui') {
            abort(403, 'Permintaan belum Disetujui.');
        }

        if (
            auth()->id() !== $permintaan->user_id &&
            !auth()->user()->hasAnyRole(['User', 'Admin'])
        ) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.permintaan', compact('permintaan'));
        return $pdf->stream('PermintaanBarang_' . Str::slug($permintaan->user->name) . '.pdf');
    }
}
