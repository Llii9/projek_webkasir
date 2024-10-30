<div class="modal fade" id="modal-periode" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ubah Periode</h4>
            </div>
            <div class="modal-body">
                <form method="GET" action="{{ route('laporan.pdf') }}">
                    <div class="form-group">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="text" name="tanggal_awal" id="tanggal_awal" class="form-control datepicker" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="text" name="tanggal_akhir" id="tanggal_akhir" class="form-control datepicker" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
