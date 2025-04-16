@empty($penjualan)
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">Data tidak ditemukan.</div>
            </div>
        </div>
    </div>
@else
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Penjualan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                <h3 class="text-center">Struk Penjualan</h3>

                <div class="receipt">
                    <p><strong>Kode Penjualan:</strong> {{ $penjualan->penjualan_kode }}</p>
                    <p><strong>Petugas:</strong> {{ $penjualan->user->nama ?? '-' }}</p>
                    <p><strong>Pembeli:</strong> {{ $penjualan->pembeli }}</p>
                    <p><strong>Tanggal:</strong> {{ $penjualan->penjualan_tanggal }}</p>
                    <hr>
                    <table width="100%">
                        @php $total = 0; @endphp
                        @foreach ($penjualan->details as $detail)
                                        @php
                                            $nama = $detail->barang->barang_nama ?? '-';
                                            $jumlah = $detail->jumlah;
                                            $harga = $detail->harga;
                                            $subtotal = $jumlah * $harga;
                                            $total += $subtotal;
                                        @endphp
                                        <tr>
                                            <td colspan="2"><strong>{{ $nama }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td>{{ $jumlah }} x Rp {{ number_format($harga, 0, ',', '.') }}</td>
                                            <td style="text-align: right;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                        </tr>
                        @endforeach
                    </table>
                    <hr>
                    <p style="text-align: right;"><strong>Total: Rp {{ number_format($total, 0, ',', '.') }}</strong></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" onclick="window.print()">Cetak Struk</button>
            </div>
        </div>
    </div>
@endempty

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .modal-content,
        .modal-content * {
            visibility: visible;
        }

        .modal-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .modal-footer {
            display: none;
        }

        h5,
        h3 {
            text-align: center;
        }

        .receipt {
            font-family: "Courier New", Courier, monospace;
            font-size: 14px;
            line-height: 1.5;
            width: 260px;
            margin: 0 auto;
            padding: 10px;
        }

        .receipt hr {
            border: 1px solid #000;
        }

        .receipt p {
            margin: 0;
            padding: 3px 0;
        }

        .receipt table {
            width: 100%;
        }

        .receipt table td {
            padding: 2px 0;
        }
    }
</style>