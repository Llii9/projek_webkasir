<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pendapatan</title>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .logo {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <h3 class="text-center">Laporan Pendapatan</h3>
    <div class="text-center">
        @if ($setting->path_logo)
        <img src="{{ public_path($setting->path_logo) }}" alt="Logo" width="80" style="margin-bottom: 5px;">
        @endif
        <h3>{{ strtoupper($setting->nama_toko) }}</h3>
        <p>{{ strtoupper($setting->alamat) }}</p>
    </div>
    <h4 class="text-right">
        Tanggal {{ tanggal_indonesia($awal, false) }} s/d Tanggal {{ tanggal_indonesia($akhir, false) }}
    </h4>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Produk</th>
                <th>Total Item</th>
                <th>Total Harga</th>
                <th>Diskon</th>
                <th>Total Bayar</th>
                <th>Kasir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $penjualan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ tanggal_indonesia($penjualan->created_at, false) }}</td>
                    <td>
                        @foreach ($penjualan->detail as $detail)
                            {{ $detail->produk->nama_produk ?? 'Produk tidak ditemukan' }}<br>
                        @endforeach
                    </td>
                    <td>{{ $penjualan->total_item }}</td>
                    <td>Rp. {{ format_uang($penjualan->total_harga) }}</td>
                    <td>{{ $penjualan->diskon }}%</td>
                    <td>Rp. {{ format_uang($penjualan->bayar) }}</td>
                    <td>{{ $penjualan->user->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
