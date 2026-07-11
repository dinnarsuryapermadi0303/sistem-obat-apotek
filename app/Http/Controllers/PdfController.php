<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RecommendationValidation;

class PdfController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Preview PDF
    |--------------------------------------------------------------------------
    */
    public function preview($id)
    {
        $item = RecommendationValidation::where('kode', $id)->first();

        if (!$item) {
            return redirect()
                ->back()
                ->with('error', 'Data tidak ditemukan.');
        }

        if (!$item->pdf_ready) {
            return back()->with(
                'error',
                'Laporan belum disetujui Admin.'
            );
        }

        $detail = array_merge($item->toArray(), [
            'key' => $item->kode,
            'tanggal' => $item->created_at->format('d-m-Y H:i:s'),
            'approved_at' => $item->approved_at ? $item->approved_at->format('d-m-Y H:i:s') : null,
        ]);

        $pdf = Pdf::loadView('pdf.preview', compact('detail'));

        return $pdf->stream(
            'Laporan_' . $detail['key'] . '.pdf'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Download PDF
    |--------------------------------------------------------------------------
    */

    public function download($id)
    {
        $item = RecommendationValidation::where('kode', $id)->first();

        if (!$item) {
            return redirect()
                ->back()
                ->with('error', 'Data tidak ditemukan.');
        }

        if (!$item->pdf_ready) {
            return redirect()
                ->back()
                ->with('error', 'PDF belum tersedia.');
        }

        $detail = array_merge($item->toArray(), [
            'tanggal' => $item->created_at->format('d-m-Y H:i:s'),
            'approved_at' => $item->approved_at ? $item->approved_at->format('d-m-Y H:i:s') : null,
        ]);

        $pdf = Pdf::loadView('pdf.download', [
            'detail' => $detail
        ]);

        return $pdf->download(
            'Laporan_' . $detail['nama'] . '_' . date('dmY') . '.pdf'
        );
    }
}
