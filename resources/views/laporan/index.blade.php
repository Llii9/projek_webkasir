@extends('layouts.master')

@section('title')
    Daftar Penjualan {{ tanggal_indonesia($tanggalAwal, false) }} s/d {{ tanggal_indonesia($tanggalAkhir, false) }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('/admin1/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Daftar Penjualan</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="box">
            <div class="box-header with-border">
                <button onclick="showModalPeriode()" class="btn btn-info btn-xs btn-flat"><i class="fa fa-plus-circle"></i> Ubah Periode</button>
                <form action="{{ route('laporan.pdf', [$tanggalAwal, $tanggalAkhir]) }}" method="GET" target="_blank">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="tanggal_awal" id="tanggal_awal" class="form-control datepicker" 
                                value="{{ $tanggalAwal }}" placeholder="Tanggal Awal" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="tanggal_akhir" id="tanggal_akhir" class="form-control datepicker" 
                                value="{{ $tanggalAkhir }}" placeholder="Tanggal Akhir" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-xs btn-flat">Export PDF</button>

                </form>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-stiped table-bordered table-penjualan">
                    <thead>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Total Item</th>
                        <th>Total Harga</th>
                        <th>Diskon</th>
                        <th>Total Bayar</th>
                        <th>Kasir</th>
                        <th width="15%"><i class="fa fa-cog"></i></th>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


@includeIf('laporan.modal_periode')
@includeIf('laporan.detail')
 <!-- Modal untuk memilih periode -->
@endsection

@push('scripts')
<script src="{{ asset('/admin1/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script>
    let table, table1;

    $(function () {
        table = $('.table-penjualan').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                url: '{{ route('penjualan.data', [$tanggalAwal, $tanggalAkhir]) }}',
                data: function (d) {
                    d.tanggal_awal = $('#tanggal_awal').val();
                    d.tanggal_akhir = $('#tanggal_akhir').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'tanggal'},
                {data: 'produk'},
                {data: 'total_item'},
                {data: 'total_harga'},
                {data: 'diskon'},
                {data: 'bayar'},
                {data: 'kasir'},
                {data: 'aksi', searchable: false, sortable: false},
            ]
        });
        table1 = $('.table-detail').DataTable({
            processing: true,
            bSort: false,
            dom: 'Brt',
            columns: [
                {data: 'DT_RowIndex', searchable: false, sortable: false},
                {data: 'kode_produk'},
                {data: 'nama_produk'},
                {data: 'harga_jual'},
                {data: 'jumlah'},
                {data: 'subtotal'},
            ]
        })
    });

    function showModalPeriode() {
        $('#modal-periode').modal('show');
    }
    $(document).ready(function() {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });
    });
    
    function showDetail(url) {
        $('#modal-detail').modal('show');

        table1.ajax.url(url);
        table1.ajax.reload();
    }

    function deleteData(url) {
        if (confirm('Yakin ingin menghapus data terpilih?')) {
            $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                .done((response) => {
                    table.ajax.reload();
                })
                .fail((errors) => {
                    alert('Tidak dapat menghapus data');
                    return;
                });
        }
    }
</script>
@endpush
