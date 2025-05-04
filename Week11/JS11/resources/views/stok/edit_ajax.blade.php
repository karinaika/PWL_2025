@if(empty($stok))
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <a href="{{ url('/stok') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/stok/' . $stok->stok_id . '/update_ajax') }}" method="POST" id="form-edit">
        @csrf
        @method('PUT')
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Stok</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    {{-- Barang (tampilkan nama + hidden input id) --}}
                    <div class="form-group">
                        <label>Barang</label>
                        <input type="hidden" name="barang_id" value="{{ $stok->barang_id }}">
                        <input type="text" class="form-control"
                            value="{{ $barang->where('barang_id', $stok->barang_id)->first()->barang_nama ?? 'Tidak ditemukan' }}"
                            disabled>
                        <small id="error-barang_id" class="error-text form-text text-danger"></small>
                    </div>

                    {{-- Petugas --}}
                    <div class="form-group">
                        <label>Petugas</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Pilih User</option>
                            @foreach ($user as $u)
                                <option value="{{ $u->user_id }}" {{ $stok->user_id == $u->user_id ? 'selected' : '' }}>
                                    {{ $u->nama }}
                                </option>
                            @endforeach
                        </select>
                        <small id="error-user_id" class="error-text form-text text-danger"></small>
                    </div>

                    {{-- Jumlah --}}
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="stok_jumlah" id="stok_jumlah" class="form-control"
                            value="{{ $stok->stok_jumlah }}" required>
                        <small id="error-stok_jumlah" class="error-text form-text text-danger"></small>
                    </div>

                    {{-- Tanggal --}}
                    <div class="form-group">
                        <label>Tanggal Stok</label>
                        <input type="hidden" name="stok_tanggal" value="{{ $stok->stok_tanggal }}">
                        <input type="text" class="form-control"
                            value="{{ \Carbon\Carbon::parse($stok->stok_tanggal)->format('d-m-Y') }}" readonly>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            $("#form-edit").validate({
                rules: {
                    barang_id: { required: true },
                    user_id: { required: true },
                    stok_jumlah: {
                        required: true,
                        number: true,
                        min: 1
                    },
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
                                $('#table_stok').DataTable().ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function (prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
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
@endif