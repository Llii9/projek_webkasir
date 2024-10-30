<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Setting;
use Illuminate\Http\Request;
use PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Jika request tidak mengirim tanggal, ambil semua data
        $tanggalAwal = $request->tanggal_awal ?? Penjualan::min('created_at'); // ambil tanggal paling awal dari data penjualan
        $tanggalAkhir = $request->tanggal_akhir ?? date('Y-m-d'); // ambil tanggal hari ini

        // Lakukan query untuk mengambil data berdasarkan rentang tanggal
        $penjualan = Penjualan::with('detail.produk')
            ->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
            ->get();

        return view('laporan.index', compact('penjualan', 'tanggalAwal', 'tanggalAkhir'));
    }
    
    public function exportPDF($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);
        $setting = Setting::first();
        $pdf = PDF::loadView('laporan.pdf', compact('awal', 'akhir', 'data', 'setting'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Laporan-Pendapatan-' . date('Y-m-d-his') . '.pdf');
    }

    private function getData($awal, $akhir)
    {
        return Penjualan::with('detail.produk')
            ->whereBetween('created_at', [$awal, $akhir])
            ->get();
    }
}
