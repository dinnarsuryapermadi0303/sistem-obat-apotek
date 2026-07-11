<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RecommendationValidation;
use App\Models\Product;

class ValidationController extends Controller
{
    public function riwayat()
    {
        $data = RecommendationValidation::orderBy('created_at', 'desc')->get();

        return view('admin.riwayat', compact('data'));
    }

    public function index()
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $data = RecommendationValidation::latest()
            ->where(function ($query) {
                $query->whereNull('admin_status')
                    ->orWhere('admin_status', 'Menunggu Validasi')
                    ->orWhere('admin_status', 'pending')
                    ->orWhere('status', 'pending')
                    ->orWhere('user_status', 'pending');
            })
            ->whereNotIn('admin_status', ['Disetujui Admin', 'Ditolak Admin'])
            ->whereNotIn('status', ['approved', 'rejected'])
            ->whereNotIn('user_status', ['approved', 'rejected'])
            ->paginate(15);

        return view('admin.validasi', compact('data'));
    }

    public function detail($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $item = RecommendationValidation::findOrFail($id);

        $selected = array_merge($item->toArray(), [
            'id' => $item->id,
            'key' => $item->kode,
            'approved_meds' => $item->approved_meds ?? [],
            'admin_conditions' => $item->admin_conditions ?? $item->catatan_admin ?? '',
            'status' => $item->status === 'rejected' ? 'Ditolak' : ($item->status === 'approved' ? 'Disetujui' : ($item->status ?? 'pending')),
        ]);

        if (!empty($item->recommended_meds) && is_array($item->recommended_meds)) {
            $recommendedProduk = collect($item->recommended_meds)->map(function ($product) use ($item) {
                if (!isset($product['similarity_pct'])) {
                    if (isset($product['persentase'])) {
                        $product['similarity_pct'] = $product['persentase'];
                    } elseif (isset($product['similarity'])) {
                        $product['similarity_pct'] = $product['similarity'] > 1 ? $product['similarity'] : round($product['similarity'] * 100, 2);
                    } else {
                        $product['similarity_pct'] = $item->similarity ?? 0;
                    }
                }
                return $product;
            });
        } else {
            $recommendedProduk = $this->buildRecommendedProducts($item);
        }

        return view('admin.validasi-detail', compact('selected', 'recommendedProduk'));
    }

    public function approve(Request $request, $id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'conditions' => 'nullable|string|max:2000',
            'status' => 'required|in:Disetujui,Ditolak',
            'approved' => 'nullable|array',
            'approved.*' => 'string|max:255',
        ]);

        $item = RecommendationValidation::findOrFail($id);

        $isApproved = $request->status === 'Disetujui';

        $item->status = $isApproved ? 'approved' : 'rejected';
        $item->user_status = $isApproved ? 'approved' : 'rejected';
        $item->admin_status = $isApproved ? 'Disetujui Admin' : 'Ditolak Admin';
        $item->pdf_ready = $isApproved;
        $item->catatan_admin = $request->input('conditions');
        $item->admin_conditions = $request->input('conditions');
        $item->approved_meds = $request->input('approved', []);
        $item->approved_by = Auth::check()
            ? Auth::user()->name
            : 'Administrator';
        $item->approved_at = now();

        $item->save();

        return redirect()->route('admin.validasi')
            ->with('success', 'Rekomendasi berhasil divalidasi');
    }

    public function reject(Request $request, $id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $request->validate([

            'catatan_admin' => 'required|string|max:500'

        ]);

        $item = RecommendationValidation::findOrFail($id);

        $item->status = 'rejected';

        $item->user_status = 'rejected';

        $item->admin_status = 'Ditolak Admin';

        $item->pdf_ready = false;

        $item->catatan_admin = $request->catatan_admin;

        $item->approved_by = 'Administrator';

        $item->approved_at = now();

        $item->save();

        return redirect()->route('admin.validasi')
            ->with('success', 'Rekomendasi berhasil ditolak');
    }

    private function buildRecommendedProducts(RecommendationValidation $item)
    {
        $produk = Product::all()->toArray();
        $matched = [];

        foreach ($produk as $product) {
            if (isset($product['nama']) && trim($product['nama']) === trim($item->obat)) {
                $matched[] = array_merge($product, [
                    'similarity_pct' => $item->similarity ?? 0,
                ]);
                break;
            }
        }

        if (empty($matched)) {
            $matched[] = [
                'nama' => $item->obat,
                'kategori' => $item->kategori ?? '-',
                'similarity_pct' => $item->similarity ?? 0,
                'deskripsi' => $item->obat,
            ];
        }

        return collect($matched);
    }

    public function destroy($id)
    {
        $item = RecommendationValidation::find($id);
        if (!$item) {
            return redirect()->route('admin.validasi')->with('error', 'Data validasi tidak ditemukan.');
        }

        // Mark as deleted for admin only; keep DB record for user visibility.
        $item->admin_status = 'Dihapus Admin';
        $item->save();

        return redirect()->route('admin.validasi')
            ->with('success', 'Data validasi ditandai sebagai dihapus oleh Admin (tetap ada untuk pengguna).');
    }

    public function edit($id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $item = RecommendationValidation::findOrFail($id);

        $selected = array_merge($item->toArray(), [
            'id' => $item->id,
            'key' => $item->kode,
            'approved_meds' => $item->approved_meds ?? [],
            'admin_conditions' => $item->admin_conditions ?? $item->catatan_admin ?? '',
            'status' => $item->status,
            'pdf_ready' => $item->pdf_ready,
        ]);

        $recommendedProduk = $this->buildRecommendedProducts($item);

        return view('admin.edit-validasi', compact('selected', 'recommendedProduk'));
    }

    public function update(Request $request, $id)
    {
        if (!session('admin_logged_in')) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'approved_meds' => 'nullable|array',
            'approved_meds.*' => 'string|max:255',
            'admin_status' => 'required|string',
            'admin_conditions' => 'nullable|string|max:2000',
            'pdf_ready' => 'nullable|in:1',
        ]);

        $item = RecommendationValidation::findOrFail($id);

        $item->approved_meds = $request->input('approved_meds', []);
        $item->admin_conditions = $request->input('admin_conditions');
        $item->admin_status = $request->input('admin_status');
        $item->pdf_ready = $request->has('pdf_ready');

        // map admin_status to status/user_status
        if ($item->admin_status === 'Disetujui Admin') {
            $item->status = 'approved';
            $item->user_status = 'approved';
            $item->approved_by = session('admin_email') ?? 'Administrator';
            if (!$item->approved_at) {
                $item->approved_at = now();
            }
        } elseif ($item->admin_status === 'Ditolak Admin') {
            $item->status = 'rejected';
            $item->user_status = 'rejected';
            $item->approved_by = session('admin_email') ?? 'Administrator';
            $item->approved_at = now();
            $item->pdf_ready = false;
        }

        $item->save();

        return redirect()->route('admin.laporan')->with('success', 'Perubahan validasi tersimpan.');
    }
}
