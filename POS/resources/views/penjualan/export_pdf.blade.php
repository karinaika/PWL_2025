<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
            font-size: 9pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        td,
        th {
            padding: 3px 6px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .p-1 {
            padding: 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }

        .total-row {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ asset('polinema-bw.png') }}" class="image">
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">
                    KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN TEKNOLOGI
                </span>
                <span class="text-center d-block font-13 font-bold mb-1">
                    POLITEKNIK NEGERI MALANG
                </span>
                <span class="text-center d-block font-10">
                    Jl. Soekarno-Hatta No. 9 Malang 65141
                </span>
                <span class="text-center d-block font-10">
                    Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341) 404420
                </span>
                <span class="text-center d-block font-10">
                    Laman: www.polinema.ac.id
                </span>
            </td>
        </tr>
    </table>

    <h3 class="text-center">LAPORAN DATA PENJUALAN</h3>

    <table class="border-all">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Petugas</th>
                <th>Kode Penjualan</th>
                <th>Pembeli</th>
                <th class="text-center">Tanggal</th>
                <th>Barang</th>
                <th class="text-center">Jumlah</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_per_penjualan = 0;
            @endphp
            @foreach($penjualans as $p)
                        @php
                            $total_penjualan = 0;
                        @endphp
                        @foreach($p->details as $detail)
                                @php
                                    $subtotal = $detail->harga * $detail->jumlah;
                                    $total_penjualan += $subtotal;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->parent->iteration }}</td>
                                    <td>{{ $p->user->nama }}</td>
                                    <td>{{ $p->penjualan_kode }}</td>
                                    <td>{{ $p->pembeli }}</td>
                                    <td class="text-center">{{ $p->penjualan_tanggal }}</td>
                                    <td>{{ $detail->barang->barang_nama ?? '-' }}</td>
                                    <td class="text-center">{{ $detail->jumlah }}</td>
                                    <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="8" class="text-left">Total Penjualan</td>
                            <td class="text-right">Rp {{ number_format($total_penjualan, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            $total_per_penjualan += $total_penjualan;
                        @endphp
            @endforeach
        </tbody>
    </table>
</body>

</html>