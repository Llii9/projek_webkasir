<?php

namespace App\Http\Controllers;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\Setting;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Dompdf\Adapter\PDFLib;
use Illuminate\Http\Request;
use PDF;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');
    
        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }
    
        return view('penjualan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }
    
    public function data(Request $request)
{
    // Ambil tanggal awal dan akhir dari request, dengan nilai default jika tidak ada
    $tanggalAwal = $request->input('tanggal_awal', date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y'))));
    $tanggalAkhir = $request->input('tanggal_akhir', date('Y-m-d'));

    // Ambil data penjualan berdasarkan rentang tanggal
    $penjualan = Penjualan::whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])
        ->orderBy('id_penjualan', 'desc')
        ->get();

        return datatables()
            ->of($penjualan)
            ->addIndexColumn()
            ->addColumn('produk', function ($penjualan) {
                // Menampilkan nama produk dan jumlah item per transaksi
                $produkList = '';
                foreach ($penjualan->produk as $produk) {
                    $produkList .= $produk->nama_produk ;
                }
                return $produkList;
            })
            ->addColumn('total_item', function ($penjualan) {
                return format_uang($penjualan->total_item);
            })
            ->addColumn('total_harga', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->total_harga);
            })
            ->addColumn('bayar', function ($penjualan) {
                return 'Rp. '. format_uang($penjualan->bayar);
            })
            ->addColumn('tanggal', function ($penjualan) {
                return tanggal_indonesia($penjualan->created_at, false);
            })
            ->editColumn('diskon', function ($penjualan) {
                return $penjualan->diskon . '%';
            })
            ->editColumn('kasir', function ($penjualan) {
                return $penjualan->user->name ?? '';
            })
            ->addColumn('aksi', function ($penjualan) {
                return '
                <div class="btn-group">
                    <button onclick="showDetail(`'. route('penjualan.show', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`'. route('penjualan.destroy', $penjualan->id_penjualan) .'`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['produk', 'aksi'])
            ->make(true);
    
    }
    public function create(){

        $penjualan = new Penjualan();
        $penjualan->total_item = 0;
        $penjualan->total_harga = 0;
        $penjualan->diskon = 0;
        $penjualan-> bayar = 0;
        $penjualan-> diterima = 0;
        $penjualan-> id_user = auth()->id();
        $penjualan->save();

        session(['id_penjualan'=> $penjualan->id_penjualan]);
        return redirect()->route('transaksi.index');
        
    }

    public function store(Request $request)
    {
    $penjualan = penjualan::findOrFail($request->id_penjualan);
    $penjualan->total_item = $request->total_item;
    $penjualan->total_harga = $request->total;
    $penjualan->diskon = $request->diskon;
    $penjualan->bayar = $request->bayar;
    $penjualan->diterima = $request->diterima;
    $penjualan->update();

    $detail = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
    foreach ($detail as $item){
        $item->diskon = $request->diskon;
        $item->update();
        $produk = Produk::find($item->id_produk);
        $produk->stok -= $item->jumlah;
        $produk->update();
    }

    return redirect()->route('transaksi.selesai');

}
public function show($id)
    {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">'. $detail->produk->kode_produk .'</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga_jual', function ($detail) {
                return 'Rp. '. format_uang($detail->harga_jual);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. '. format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }
    public function destroy($id)
    {
        $penjualan = Penjualan::find($id);
        $detail    = PenjualanDetail::where('id_penjualan', $penjualan->id_penjualan)->get();
        foreach ($detail as $item) {
            $produk = Produk::find($item->id_produk);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->update();
            }

            $item->delete();
        }

        $penjualan->delete();

        return response(null, 204);
    }

    public function selesai()
    {
        $setting = Setting::first();

        return view('penjualan.selesai', compact('setting'));
    }
    public function notaKecil()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();
        
        return view('penjualan.nota_kecil', compact('setting', 'penjualan', 'detail'));
    }

    public function notaBesar()
    {
        $setting = Setting::first();
        $penjualan = Penjualan::find(session('id_penjualan'));
        if (! $penjualan) {
            abort(404);
        }
        $detail = PenjualanDetail::with('produk')
            ->where('id_penjualan', session('id_penjualan'))
            ->get();

        $pdf = PDF::loadView('penjualan.nota_besar', compact('setting', 'penjualan', 'detail'));
        $pdf->setPaper(0,0,609,440, 'potrait');
        return $pdf->stream('Transaksi-'. date('Y-m-d-his') .'.pdf');
    }

}