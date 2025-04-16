@if(empty($penjualan))
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Failed!!!</h5>
                    Data tidak ditemukan.
                </div>
                <a href="{{ url('/penjualan') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <form action="{{ url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax') }}" method="POST" id="form-delete">
        @csrf
        @method('DELETE')
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Data Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h5><i class="icon fas fa-ban"></i> Konfirmasi!!!</h5>
                        Apakah Anda yakin ingin menghapus data berikut ini?
                    </div>
                    <div class="text-left">
                        <hr>
                        <p><strong>Kode Penjualan:</strong> {{ $penjualan->penjualan_kode ?? '-' }}</p>
                        <p><strong>Petugas:</strong> {{ $penjualan->user->nama ?? '-' }}</p>
                        <p><strong>Pembeli:</strong> {{ $penjualan->pembeli ?? '-' }}</p>
                        <p><strong>Tanggal:</strong> {{ $penjualan->penjualan_tanggal ?? '-' }}</p>
                        <hr>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0; 
                                @endphp
                                @foreach ($penjualan->details as $detail)
                                                        @php
                                                            // Menghitung subtotal
                                                            $subtotal = $detail->harga * $detail->jumlah;
                                                            $total += $subtotal;  //Value total
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $detail->barang->barang_nama ?? '-' }}</td>
                                                            <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                                            <td>{{ $detail->jumlah }}</td>
                                                            <td>Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                                        </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <p class="text-center"><strong>Total: </strong>Rp {{ number_format($total, 0, ',', '.') }}</p>
                        <hr>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Ya, Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            $("#form-delete").validate({
                rules: {},
                submitHandler: function (form) {
                    let $btn = $(form).find('button[type="submit"]');
                    $btn.prop('disabled', true).text('Menghapus...');

                    $.ajax({
                        url: form.action,
                        type: 'POST',
                        data: $(form).serialize(),
                        success: function (response) {
                            $btn.prop('disabled', false).text('Ya, Hapus');
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire('Berhasil', response.message, 'success');
                                tablePenjualan.ajax.reload();
                            } else {
                                Swal.fire('Gagal', response.message, 'error');
                            }
                        },
                        error: function (xhr) {
                            $btn.prop('disabled', false).text('Ya, Hapus');
                            Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                        }
                    });

                    return false;
                }

            });
        });
    </script>
@endif