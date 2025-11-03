<?php

namespace App\Http\Controllers;

use App\Models\Permintaan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PermintaanController extends Controller
{
    public function downloadPdf($id)
    {
        $permintaan = Permintaan::with('items', 'user')->findOrFail($id);

        if ($permintaan->status !== 'Disetujui') {
            abort(403, 'Permintaan belum Disetujui.');
        }

        if (auth()->id() !== $permintaan->user_id && !auth()->user()->hasRole('User')) {
            abort(403);
        }

        $pdf = Pdf::loadView('pdf.permintaan', compact('permintaan'));
        return $pdf->stream('PermintaanBarang_' . Str::slug($permintaan->user->name) . '.pdf');
    }
}
