@empty($penjualan)
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Data penjualan tidak ditemukan.
                </div>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Edit Data Penjualan</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="penjualan_kode">Kode Penjualan</label>
                            <input type="text" name="penjualan_kode" class="form-control"
                                value="{{ $penjualan->penjualan_kode }}" readonly>
                            <small id="error-penjualan_kode" class="text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="user_id">Petugas</label>
                            <select name="user_id" class="form-control">
                                <option value="">- Pilih Petugas -</option>
                                @foreach($user as $u)
                                    <option value="{{ $u->user_id }}" {{ $u->user_id == $penjualan->user_id ? 'selected' : '' }}>
                                        {{ $u->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small id="error-user_id" class="text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="pembeli">Pembeli</label>
                            <input type="text" name="pembeli" class="form-control" value="{{ $penjualan->pembeli }}">
                            <small id="error-pembeli" class="text-danger"></small>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="penjualan_tanggal">Tanggal Penjualan</label>
                            <input type="date" name="penjualan_tanggal" class="form-control"
                                value="{{ \Carbon\Carbon::parse($penjualan->penjualan_tanggal)->format('Y-m-d') }}"
                                readonly>
                            <small id="error-penjualan_tanggal" class="text-danger"></small>
                        </div>
                    </div>

                    <hr>
                    <h5>Detail Barang</h5>
                    <table class="table table-sm table-bordered" id="barangTable">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50%;">Barang</th>
                                <th style="width: 25%;">Jumlah</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan->details as $detail)
                                <tr>
                                    <td>
                                        <select name="barang_id[]" class="form-control" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach($barangs as $barang)
                                                <option value="{{ $barang->barang_id }}" {{ $detail->barang_id == $barang->barang_id ? 'selected' : '' }}>
                                                    {{ $barang->barang_nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="error-barang_id text-danger"></small>
                                    </td>
                                    <td>
                                        <input type="number" name="jumlah[]" class="form-control" min="1"
                                            value="{{ $detail->jumlah }}" required>
                                        <small class="error-jumlah text-danger"></small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addRow">
                        + Tambah Barang
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(function () {
            // Tambah baris barang
            $('#addRow').click(function () {
                let row = `
                    <tr>
                        <td>
                            <select name="barang_id[]" class="form-control" required>
                                <option value="">Pilih Barang</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->barang_id }}">{{ $barang->barang_nama }}</option>
                                @endforeach
                            </select>
                            <small class="error-barang_id text-danger"></small>
                        </td>
                        <td>
                            <input type="number" name="jumlah[]" class="form-control" min="1" required>
                            <small class="error-jumlah text-danger"></small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row">Hapus</button>
                        </td>
                    </tr>`;
                $('#barangTable tbody').append(row);
            });

            // Hapus baris
            $(document).on('click', '.remove-row', function () {
                $(this).closest('tr').remove();
            });

            // Submit AJAX
            $('#form-edit').submit(function (e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let data = form.serialize();

                // Reset error
                $('.text-danger').text('');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: data,
                    success: function (res) {
                        if (res.status) {
                            $('#modal').modal('hide');
                            Swal.fire('Sukses', res.message, 'success');
                            $('#datatable').DataTable().ajax.reload(); // Jika pakai DataTable
                        } else {
                            Swal.fire('Gagal', res.message, 'error');
                            if (res.msgField) {
                                $.each(res.msgField, function (key, val) {
                                    $('#error-' + key).text(val[0]);
                                });
                            }
                        }
                    },
                    error: function (xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Error', 'Terjadi kesalahan saat memproses data', 'error');
                    }
                });
            });
        });
    </script>

@endempty