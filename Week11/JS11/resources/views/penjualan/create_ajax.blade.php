<form action="{{ route('penjualan.store_ajax') }}" method="POST" id="form-tambah">
    @csrf
    <div class="modal-dialog modal-lg" role="document" id="modal-master">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Data Penjualan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label><strong>Kode Penjualan</strong></label>
                        <input type="text" name="penjualan_kode" class="form-control" value="TRX00" required>
                        <small class="form-text text-muted">Format: TRX00(ANGKA), contoh: TRX001</small>
                        <small id="error-penjualan_kode" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label><strong>Petugas</strong></label>
                        <select name="user_id" class="form-control" required>
                            <option value="">- Pilih Petugas -</option>
                            @foreach($user as $u)
                                <option value="{{ $u->user_id }}">{{ $u->nama }}</option>
                            @endforeach
                        </select>
                        <small id="error-user_id" class="text-danger"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label><strong>Pembeli</strong></label>
                        <input type="text" name="pembeli" class="form-control" required>
                        <small id="error-pembeli" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label><strong>Tanggal Penjualan</strong></label>
                        <input type="datetime-local" name="penjualan_tanggal" class="form-control"
                            value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        <small id="error-penjualan_tanggal" class="text-danger"></small>
                    </div>

                </div>

                <hr>
                <h5 class="mb-3">Detail Barang</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered" id="barangTable">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 45%;">Barang</th>
                                <th style="width: 25%;">Jumlah</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                    <button type="button"
                                        class="btn btn-outline-danger btn-sm remove-row">Hapus</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addRow">
                    + Tambah Barang
                </button>
            </div>

            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
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

        // Hapus baris barang
        $(document).on('click', '.remove-row', function () {
            if ($('#barangTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
            }
        });

        // Validasi form
        $("#form-tambah").validate({
            rules: {
                penjualan_kode: { required: true, minlength: 5 },
                pembeli: { required: true, minlength: 3 },
                penjualan_tanggal: { required: true, date: true },
                user_id: { required: true },
                "barang_id[]": { required: true },
                "jumlah[]": { required: true, min: 1 }
            },
            messages: {
                penjualan_kode: {
                    required: "Kode penjualan wajib diisi",
                    minlength: "Isi minimal 5 karakter diikuti angka, ex : TRX0011"
                },
                pembeli: {
                    required: "Nama pembeli wajib diisi",
                    minlength: "Isi minimal 3 karakter"
                },
                penjualan_tanggal: {
                    required: "Tanggal penjualan wajib diisi",
                    date: "Format tanggal tidak valid"
                },
                user_id: {
                    required: "Petugas wajib diisi"
                },
                "barang_id[]": {
                    required: "Barang wajib diisi"
                },
                "jumlah[]": {
                    required: "Jumlah wajib diisi",
                    min: "Isi minimal 1"
                }
            },
            submitHandler: function (form) {
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (response) {
                        if (response.status) {
                            $('#myModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });
                            tablePenjualan.ajax.reload();
                        } else {
                            $('.error-text, .error-barang_id, .error-jumlah').text('');
                            if (response.msgField) {
                                $.each(response.msgField, function (prefix, val) {
                                    if (prefix.startsWith('barang_id')) {
                                        $(`#barangTable tbody tr:eq(${prefix.split('.')[1]}) .error-barang_id`).text(val[0]);
                                    } else if (prefix.startsWith('jumlah')) {
                                        $(`#barangTable tbody tr:eq(${prefix.split('.')[1]}) .error-jumlah`).text(val[0]);
                                    } else {
                                        $('#error-' + prefix).text(val[0]);
                                    }
                                });
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message || 'Gagal menyimpan data'
                            });
                        }
                    },
                    error: function (xhr) {
                        let errorMessage = 'Gagal menyimpan data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: errorMessage
                        });
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>