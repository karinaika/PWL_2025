@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Transaksi Penjualan</h3>
            <div class="card-tools">
                <button onclick="modalAction(`{{ url('/penjualan/import') }}`)" class="btn btn-info">Import
                    Penjualan</button>
                <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary">
                    <i class="fa fa-file-excel"></i> Export Penjualan (.xlsx)
                </a>
                <button onclick="modalAction(`{{ url('/penjualan/create_ajax') }}`)" class="btn btn-success">Tambah Data
                    (Ajax)</button>
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning">
                    <i class="fa fa-file-pdf"></i> Export Penjualan (.pdf)
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Filter:</label>
                        <div class="col-3">
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">- Petugas -</option>
                                @foreach ($kasir as $item)
                                    <option value="{{ $item->user_id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Petugas</small>
                        </div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered table-striped table-hover table-sm" id="table-penjualan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Transaksi</th>
                        <th>Pembeli</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Petugas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function modalAction(url = '') {
            console.log('Membuka URL:', url);
            $('#myModal').load(url, function (response, status, xhr) {
                if (status == "error") {
                    console.log('Error:', xhr.status, xhr.statusText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal memuat data: ' + xhr.statusText
                    });
                    $('#btnTambah').prop('disabled', false);
                    $('.btn-warning').prop('disabled', false);
                } else {
                    console.log('Sukses memuat modal');
                    $('#myModal').modal('show');
                    $('#btnTambah').prop('disabled', false);
                    $('.btn-warning').prop('disabled', false);
                }
            });
        }

        $(document).ready(function () {
            var tablePenjualan = $('#table-penjualan').DataTable({
                serverSide: true,
                processing: true,
                ajax: {
                    "url": "{{ url('penjualan/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.user_id = $('#user_id').val();
                        d._token = "{{ csrf_token() }}";
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "penjualan_kode",
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "pembeli",
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "penjualan_tanggal",
                        className: "text-center",
                        orderable: true,
                        searchable: true,
                        render: function (data) {
                            return moment(data).format('DD-MM-YYYY HH:mm');
                        }
                    },
                    {
                        data: "total",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "nama",
                        className: "text-center",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ],
                language: {
                    processing: "Memuat data...",
                    emptyTable: "Tidak ada data transaksi.",
                    zeroRecords: "Tidak ada data yang sesuai dengan pencarian.",
                    //info: "Menampilkan START sampai END dari TOTAL entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    //lengthMenu: "Tampilkan MENU entri",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Next",
                        previous: "Previous"
                    }
                }
            });

            $('#user_id').on('change', function () {
                tablePenjualan.ajax.reload();
            });

            $('#btnTambah').prop('disabled', false);
            $('#table-penjualan').on('draw.dt', function () {
                $('.btn-warning').prop('disabled', false);
            });
        });
    </script>
@endpush